/*!
 * Helper script for Anyway Feedback
 *
 * @package AnywayFeedback
 */

/*global AFBP:true*/
/*global Cookies:false*/
/*global gtag:false*/

jQuery( document ).ready( function ( $ ) {

	/**
	 * Check if cookie exists
	 *
	 * @param {string} postType Post type name.
	 * @param {number} objectId post id or comment id.
	 * @returns {boolean}
	 */
	const cookieExists = function ( postType, objectId ) {
		let cookie = Cookies.get( cookieName( postType ) );
		if ( cookie ) {
			if ( typeof cookie !== 'string' ) {
				cookie = cookie.toString();
			}
			cookie = cookie.replace( /[^0-9,]/g, '' );
			cookie = cookie.split( ',' );
			return !( cookie.indexOf( objectId.toString() ) < 0 );
		}
			return false;

	};

	/**
	 * Get cookie name from post type.
	 *
	 * @param {string} postType Post type name.
	 * @returns {string}
	 */
	const cookieName = function ( postType ) {
		return 'afb_' + ( 'comment' === postType ? 'comment' : 'post' );
	};

	/**
	 * Save cookie.
	 *
	 * @param {string} postType
	 * @param {number} objectId
	 */
	const saveCookie = function ( postType, objectId ) {
		if ( ! cookieExists( postType, objectId ) ) {
			let cookie = Cookies.get( cookieName( postType ) );
			if ( cookie ) {
				if ( typeof cookie !== 'string' ) {
					cookie = cookie.toString();
				}
				cookie = cookie.split( ',' );
			} else {
				cookie = [];
			}
			cookie.push( parseInt( objectId ) );
			Cookies.set( cookieName( postType ), cookie.join( ',' ), {
				expires: 365 * 2,
				path: '/'
			} );
		}
	};

	const containers = $( '.afb_container' );

	// Check if use is already posted
	containers.each( function ( index, container ) {
		const objectId = parseInt( $( container ).find( 'input[name=object_id]' ).val() );
		const postType = $( container ).find( 'input[name=post_type]' ).val();
		if ( cookieExists( postType, objectId ) ) {
			$( container ).find( 'a, input' ).remove();
			$( container ).find( '.message' ).text( AFBP.already );
			$( container ).addClass( 'afb_posted' );
		}
	} );

	// Add event listener.
	containers.on( 'click', 'a', function ( e ) {
		e.preventDefault();
		// Check posted?
		const target = $( this ).parent( ".afb_container" );
		if ( ! target.hasClass( 'afb_posted' ) ) {
			const object_id = parseInt( target.find( "input[name=object_id]" ).val() );
			const postType = target.find( 'input[name=post_type]' ).val();
			const affirmative = $( this ).hasClass( 'bad' ) ? 0 : 1;
			wp.apiFetch( {
				path: 'afb/v1/feedback/' + postType + '/' + object_id,
				data: {
					affirmative: affirmative
				},
				method: 'post'
			} ).then( function( response ) {
				target.find( 'a, .input' ).remove();
				target.find( '.message' ).addClass( 'success' ).text( response.message );
				target.find( '.status' ).text( response.status );
				// Save cookie
				saveCookie( postType, object_id );
				// Record Google Analytics 4
				if ( '1' === AFBP.ga ) {
					try {
						gtag( 'event', 'feedback', {
							type: ( postType === 'comment' ? 'comment' : 'post' ),
							id: object_id,
							value: affirmative ? 1 : -1,
						} );
					} catch ( err ) {
						// Error.
					}
				}
				// Trigger event
				target.trigger( 'feedback.afb', [ ( postType === 'comment' ? 'comment' : 'post' ), object_id, affirmative ] );
			} ).catch( function( response ) {
				target.find( 'a, .input' ).remove();
				target.find( '.message' ).addClass( 'error' ).text( response.message );
			} );
		}
	} );
} );
