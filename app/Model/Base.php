<?php

namespace AFB\Model;

use AFB\Pattern\Singleton;

/**
 * Model base
 *
 * @package AFB\Model
 * @property-read \wpdb $db
 * @property-read string $table
 * @property-read string $key
 */
abstract class Base extends Singleton {


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
	public function require_update() {
		$current_version = get_option( $this->key, 0 );
		return version_compare( $current_version, $this->version, '<' );
	}

	/**
	 * Try db and return true on success
	 *
	 * @return bool
	 */
	public function try_update_db() {
		// Check if requires Update?
		if ( ! $this->require_update() ) {
			return false;
		}
		// Is there creation script?
		$query = $this->create_sql();
		if ( ! $query ) {
			return false;
		}
		// O.K. Let's create table
		$this->dbDelta( $query );
		// Do something after update.
		$this->after_update();
		// Update option
		update_option( $this->key, $this->version );
		return true;
	}

	/**
	 * Detect if innodb is available
	 *
	 * @return bool
	 */
	protected function have_innodb() {
		$query = <<<SQL
			SHOW VARIABLES LIKE 'have_innodb';
SQL;
		$row   = $this->db->get_row( $query );
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		return 'YES' === $row->Value;
	}

	/**
	 * Check if specific table is InnoDB
	 *
	 * @param string $table
	 *
	 * @return bool
	 */
	protected function is_innodb( $table ) {
		$query = <<<SQL
			SHOW TABLE STATUS
			WHERE `Name` = %s
			  AND `Engine` = 'InnoDB';
SQL;
		return (bool) $this->db->get_row( $this->db->prepare( $query, $table ) );
	}

	/**
	 * Executed after table has been updated
	 *
	 * For example, index or strage engine.
	 */
	protected function after_update() {
		// Do nothing.
	}

	/**
	 * Do dbDelta
	 *
	 * @param string $query
	 *
	 * @return array
	 */
	protected function dbDelta( $query ) {
		// Here starts database update!
		// Load required files.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		// Do dbDelta!
		return dbDelta( $query );
	}

	/**
	 * Creation SQL
	 *
	 * If you need create SQL, override this.
	 *
	 * @return string
	 */
	protected function create_sql() {
		return '';
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return null|string|\wpdb
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'db':
				global $wpdb;
				return $wpdb;
			case 'table':
				return $this->db->prefix . 'afb_' . $this->name;
			case 'key':
				return $this->table . '_version';
			default:
				return null;
		}
	}

}
