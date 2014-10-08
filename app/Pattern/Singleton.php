<?php

namespace AFB\Pattern;

/**
 * Singleton
 *
 * @package AFB\Pattern
 */
abstract class Singleton
{

	/**
	 * Instances
	 *
	 * @var array
	 */
	private static $instances = array();

	/**
	 * Constructor
	 *
	 * @param array $arguments
	 */
	abstract protected function __construct( array $arguments = array() );

	/**
	 * Get instance
	 *
	 * @param array $arguments
	 *
	 * @return Singleton
	 */
	final public static function get_instance( array $arguments = array() ){
		$class_name = get_called_class();
		if( !isset(self::$instances[$class_name]) ){
			self::$instances[$class_name] = new $class_name($arguments);
		}
		return self::$instances[$class_name];
	}

} 