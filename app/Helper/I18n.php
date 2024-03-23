<?php

namespace AFB\Helper;


use AFB\Pattern\Singleton;

/**
 * i18n helper
 *
 * @package AFB\Helper
 */
class I18n extends Singleton {


	/**
	 * Domain name for i18n
	 * @var string
	 */
	private static $domain = 'anyway-feedback';

	/**
	 * Constructor
	 *
	 * @param array $arguments
	 */
	protected function __construct( array $arguments = array() ) {
		// TODO: Implement __construct() method.
	}

	/**
	 * Alias for gettext _e function
	 *
	 * @deprecated
	 * @param string $string
	 * @return void
	 */
	public function e( $string ) {
		// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
		_e( $string, 'anyway-feedback' );
	}

	/**
	 * Alias for gettext __ function
	 *
	 * @deprecated
	 * @param string $string
	 * @return string
	 */
	public function _( $string ) {
		// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
		return __( $string, 'anyway-feedback' );
	}

}
