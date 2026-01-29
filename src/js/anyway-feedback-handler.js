/*!
 * Helper script for Anyway Feedback
 *
 * @handle anyway-feedback
 * @deps wp-api-fetch
 */

/* global AFBP:true */

document.addEventListener( 'DOMContentLoaded', () => {
	/**
	 * Get cookie value by name.
	 *
	 * @param {string} name Cookie name.
	 * @return {string|null} Cookie value or null if not found.
	 */
	const getCookie = ( name ) => {
		const match = document.cookie.match( new RegExp( '(^|; )' + name + '=([^;]*)' ) );
		return match ? decodeURIComponent( match[ 2 ] ) : null;
	};

	/**
	 * Set cookie value.
	 *
	 * @param {string} name    Cookie name.
	 * @param {string} value   Cookie value.
	 * @param {number} expires Days until expiration.
	 */
	const setCookie = ( name, value, expires ) => {
		const date = new Date();
		date.setTime( date.getTime() + ( expires * 24 * 60 * 60 * 1000 ) );
		document.cookie = name + '=' + encodeURIComponent( value ) + '; expires=' + date.toUTCString() + '; path=/';
	};

	/**
	 * Get cookie name from post type.
	 *
	 * @param {string} postType Post type name.
	 * @return {string} Cookie name.
	 */
	const cookieName = ( postType ) => {
		return 'afb_' + ( 'comment' === postType ? 'comment' : 'post' );
	};

	/**
	 * Check if cookie exists
	 *
	 * @param {string} postType Post type name.
	 * @param {number} objectId post id or comment id.
	 * @return {boolean} Whether the cookie exists.
	 */
	const cookieExists = ( postType, objectId ) => {
		let cookie = getCookie( cookieName( postType ) );
		if ( cookie ) {
			cookie = cookie.replace( /[^0-9,]/g, '' );
			cookie = cookie.split( ',' );
			return cookie.indexOf( objectId.toString() ) >= 0;
		}
		return false;
	};

	/**
	 * Save cookie.
	 *
	 * @param {string} postType
	 * @param {number} objectId
	 */
	const saveCookie = ( postType, objectId ) => {
		if ( ! cookieExists( postType, objectId ) ) {
			let cookie = getCookie( cookieName( postType ) );
			if ( cookie ) {
				cookie = cookie.split( ',' );
			} else {
				cookie = [];
			}
			cookie.push( parseInt( objectId, 10 ) );
			setCookie( cookieName( postType ), cookie.join( ',' ), 365 * 2 );
		}
	};

	/**
	 * Show negative feedback reason dialog
	 *
	 * @param {string} postType Post type name.
	 * @param {number} objectId Post id or comment id.
	 */
	const showNegativeReasonDialog = ( postType, objectId ) => {
		// Create dialog element
		const dialog = document.createElement( 'dialog' );
		dialog.className = 'afb-negative-dialog';
		dialog.innerHTML = `
			<form method="dialog">
				<p class="afb-dialog-prompt">${ AFBP.reasonPrompt }</p>
				<textarea class="afb-reason-text" rows="4"></textarea>
				<div class="afb-dialog-buttons">
					<button type="button" class="afb-dialog-cancel">${ AFBP.cancel }</button>
					<button type="submit" class="afb-dialog-submit">${ AFBP.submit }</button>
				</div>
			</form>
		`;

		document.body.appendChild( dialog );

		const textarea = dialog.querySelector( '.afb-reason-text' );
		const cancelBtn = dialog.querySelector( '.afb-dialog-cancel' );
		const form = dialog.querySelector( 'form' );

		// Handle cancel button
		cancelBtn.addEventListener( 'click', () => {
			dialog.close();
		} );

		// Handle backdrop click
		dialog.addEventListener( 'click', ( e ) => {
			if ( e.target === dialog ) {
				dialog.close();
			}
		} );

		// Handle form submit
		form.addEventListener( 'submit', ( e ) => {
			e.preventDefault();
			const reason = textarea.value.trim();

			if ( ! reason ) {
				// No reason provided, just close
				dialog.close();
				return;
			}

			// Send reason to API
			wp.apiFetch( {
				path: `afb/v1/negative-reason/${ postType }/${ objectId }`,
				data: { reason },
				method: 'POST',
			} ).then( () => {
				// Show thank you message
				dialog.innerHTML = `
					<p class="afb-dialog-thanks">${ AFBP.reasonThanks }</p>
					<div class="afb-dialog-buttons">
						<button type="button" class="afb-dialog-close">${ AFBP.close }</button>
					</div>
				`;
				dialog.querySelector( '.afb-dialog-close' ).addEventListener( 'click', () => {
					dialog.close();
				} );
			} ).catch( () => {
				// On error, just close the dialog (vote is already recorded)
				dialog.close();
			} );
		} );

		// Clean up on close
		dialog.addEventListener( 'close', () => {
			dialog.remove();
		} );

		dialog.showModal();
	};

	const containers = document.querySelectorAll( '.afb_container' );

	// Check if user is already posted
	containers.forEach( ( container ) => {
		const objectIdInput = container.querySelector( 'input[name=object_id]' );
		const postTypeInput = container.querySelector( 'input[name=post_type]' );

		if ( ! objectIdInput || ! postTypeInput ) {
			return;
		}

		const objectId = parseInt( objectIdInput.value, 10 );
		const postType = postTypeInput.value;

		if ( cookieExists( postType, objectId ) ) {
			// Remove clickable elements and inputs
			container.querySelectorAll( 'a, button, input' ).forEach( ( el ) => el.remove() );
			const messageEl = container.querySelector( '.message' );
			if ( messageEl ) {
				messageEl.textContent = AFBP.already;
			}
			container.classList.add( 'afb_posted' );
		}
	} );

	// Add event listener using event delegation
	containers.forEach( ( container ) => {
		container.addEventListener( 'click', ( e ) => {
			const clickedEl = e.target.closest( '.good, .bad' );
			if ( ! clickedEl ) {
				return;
			}

			e.preventDefault();

			// Check if already posted
			if ( container.classList.contains( 'afb_posted' ) ) {
				return;
			}

			const objectIdInput = container.querySelector( 'input[name=object_id]' );
			const postTypeInput = container.querySelector( 'input[name=post_type]' );

			if ( ! objectIdInput || ! postTypeInput ) {
				return;
			}

			const objectId = parseInt( objectIdInput.value, 10 );
			const postType = postTypeInput.value;
			const affirmative = clickedEl.classList.contains( 'bad' ) ? 0 : 1;

			wp.apiFetch( {
				path: `afb/v1/feedback/${ postType }/${ objectId }`,
				data: { affirmative },
				method: 'POST',
			} ).then( ( response ) => {
				// Remove clickable elements and inputs
				container.querySelectorAll( 'a, button, .input' ).forEach( ( el ) => el.remove() );

				const messageEl = container.querySelector( '.message' );
				if ( messageEl ) {
					messageEl.classList.add( 'success' );
					messageEl.textContent = response.message;
				}

				const statusEl = container.querySelector( '.status' );
				if ( statusEl ) {
					statusEl.textContent = response.status;
				}

				// Save cookie
				saveCookie( postType, objectId );

				// Record Google Analytics 4
				if ( '1' === AFBP.ga || 1 === AFBP.ga ) {
					try {
						// eslint-disable-next-line no-undef
						gtag( 'event', 'feedback', {
							type: postType === 'comment' ? 'comment' : 'post',
							id: objectId,
							value: affirmative ? 1 : -1,
						} );
					} catch ( err ) {
						// Error.
					}
				}

				// Trigger custom event (vanilla JS)
				container.dispatchEvent( new CustomEvent( 'feedback.afb', {
					bubbles: true,
					detail: {
						type: postType === 'comment' ? 'comment' : 'post',
						objectId,
						affirmative,
					},
				} ) );

				// Show negative reason dialog for negative feedback
				if ( affirmative === 0 ) {
					showNegativeReasonDialog( postType, objectId );
				}
			} ).catch( ( response ) => {
				// Remove clickable elements and inputs
				container.querySelectorAll( 'a, button, .input' ).forEach( ( el ) => el.remove() );

				const messageEl = container.querySelector( '.message' );
				if ( messageEl ) {
					messageEl.classList.add( 'error' );
					messageEl.textContent = response.message;
				}
			} );
		} );
	} );
} );
