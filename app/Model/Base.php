<?php

namespace AFB\Model;

use AFB\Pattern\Singleton;
use AFB\Helper\i18n;

/**
 * Model base
 *
 * @package AFB\Model
 * @property-read \wpdb $db
 * @property-read string $table
 * @property-read string $key
 * @property-read i18n $i18n
 */
abstract class Base extends Singleton
{

	/**
	 * Version of table
	 *
	 * @var string
	 */
	protected $version = '1.0';

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Constructor
	 *
	 * @param array $arguments
	 */
	protected function __construct( array $arguments = array() ) {
		// TODO: Implement __construct() method.
	}

	/**
	 * Check version is outdated.
	 *
	 * @return bool
	 */
	public function require_update(){
		$current_version = get_option($this->key, 0);
		return version_compare($current_version, $this->version, '<');
	}

	/**
	 * Do dbDelta
	 *
	 * @param string $query
	 *
	 * @return array
	 */
	protected function dbDelta($query){
		// Here starts database update!
		// Load required files.
		require_once ABSPATH . "wp-admin/includes/upgrade.php";
		// Do dbDelta!
		return dbDelta($query);
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return null|string|\wpdb
	 */
	public function __get($name){
		switch( $name ){
			case 'db':
				global $wpdb;
				return $wpdb;
				break;
			case 'table':
				return $this->db->prefix.'afb_'.$this->name;
				break;
			case 'key':
				return $this->table.'_version';
				break;
			case 'i18n':
				return i18n::get_instance();
				break;
			default:
				return null;
				break;
		}
	}

} 