<?php

namespace AFB\Helper;


use AFB\Pattern\Singleton;


/**
 * Input Helper
 *
 * @package AFB\Helper
 */
class Input extends Singleton {

	/**
	 * Constructor
	 *
	 * @param array $arguments
	 */
	protected function __construct( array $arguments = array() ) {
		// TODO: Implement __construct() method.
	}

	/**
	 * Get param
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get( $key ) {
		return isset( $_GET[ $key ] ) ? $_GET[ $key ] : null;
	}

	/**
	 * Post param
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function post( $key ) {
		return isset( $_POST[ $key ] ) ? $_POST[ $key ] : null;
	}

	/**
	 * Request param
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function request( $key ) {
		return isset( $_REQUEST[ $key ] ) ? $_REQUEST[ $key ] : null;
	}

	/**
	 * Check nonce
	 *
	 * @param string $action
	 * @param string $key
	 *
	 * @return bool
	 */
	public function check_nonce( $action, $key = '_wpnonce' ) {
		return wp_verify_nonce( $this->request( $key ), $action );
	}

}
