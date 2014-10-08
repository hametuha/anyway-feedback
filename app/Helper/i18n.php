<?php

namespace AFB\Helper;


use AFB\Pattern\Singleton;

/**
 * i18n helper
 *
 * @package AFB\Helper
 */
class i18n extends Singleton
{

	/**
	 * Domain name for i18n
	 * @var string
	 */
	private static $domain = "anyway-feedback";

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
	 * @param string $string
	 * @return void
	 */
	public function e($string){
		_e($string, self::$domain);
	}

	/**
	 * Alias for gettext __ function
	 *
	 * @param string $string
	 * @return string
	 */
	public function _($string){
		return __($string, self::$domain);
	}

}
