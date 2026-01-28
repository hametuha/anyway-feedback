<?php

namespace AFB\Pattern;

/**
 * Detect oif user is already posted or not.
 */
trait UserDetector {

	/**
	 * Set cookie
	 *
	 * @param int $object_id
	 * @param string $post_type
	 */
	public function user_posted( $object_id, $post_type ) {
		$object_id   = intval( $object_id );
		$cookie_name = $this->cookie_name( $post_type );
		$cookie      = isset( $_COOKIE[ $cookie_name ] ) ? array_filter(explode( ',', $_COOKIE[ $cookie_name ] ), function ( $val ) {
			return is_numeric( $val );
		}) : array();
		if ( ! in_array( (string) $object_id, $cookie, true ) ) {
			$cookie[] = $object_id;
		}
		// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
		setcookie( $cookie_name, implode( ',', $cookie ), current_time( 'timestamp' ) + ( 60 * 60 * 24 * 365 * 10 ), '/' );
	}

	/**
	 * Detect if current user has response
	 *
	 * @param string $post_type
	 * @param int $object_id
	 *
	 * @return boolean
	 */
	public function does_current_user_posted( $post_type, $object_id ) {
		$cookie_name = $this->cookie_name( $post_type );
		if ( ! isset( $_COOKIE[ $cookie_name ] ) ) {
			return false;
		}
		$cookie = explode( ',', $_COOKIE[ $cookie_name ] );
		return in_array( (string) $object_id, $cookie, true );
	}

	/**
	 * Get Cookie name
	 *
	 * @param $post_type
	 *
	 * @return string
	 */
	private function cookie_name( $post_type ) {
		return 'afb_' . ( 'comment' === $post_type ? 'comment' : 'post' );
	}
}
