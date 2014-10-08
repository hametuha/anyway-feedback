<?php
/**
 * Core class for Anyway Feedback
 * 
 * @package Anyway Feedback
 * @since 0.1
 */

class Anyway_Feedback
{
	
	/**
	 * Version of this plugin
	 * @var float
	 */
	var $version = 0.7;
	
	/**
	 * Version of database
	 * @var float
	 */
	var $db_version;
	
	/**
	 * Table name of this plugin
	 * @var string
	 */
	var $table = "afb_feedbacks";
	
	/**
	 * Directory of this plugin
	 * @var string
	 */
	var $dir;
	
	/**
	 * Name of Session key
	 * @var string
	 */
	private $session = 'afb_session';

	/**
	 * Domain name for i18n
	 * @var string
	 */
	public static $domain = "anyway-feedback";
	
	/**
	 * Default option
	 * @var array
	 */
	var $default_option = array(
		"style" => 0,
		"post_types" => array(),
		"comment" => 0,
		"controller" => ''
	);
	
	/**
	 * undocumented class variable
	 *
	 * @var array
	 */
	var $option;
	
	/**
	 * Message for admin panel
	 * @var array
	 */
	var $message = array();
	
	/**
	 * Error message for admin panel
	 */
	var $error = array();
	
	/**
	 * Retrieve all type of data 
	 * @param string $post_type
	 * @return array
	 */
	function get_all($post_type = "post"){
		global $wpdb;
		if($post_type == "comment"){
			$sql = <<<EOS
				SELECT post.ID, post.post_title, comment.comment_ID, comment.comment_author, comment.user_id, afb.positive, afb.negative, (afb.positive + afb.negative) AS total, afb.updated
				FROM {$this->table} AS afb
				LEFT JOIN {$wpdb->comments} AS comment
				ON afb.object_id = comment.comment_ID
				LEFT JOIN {$wpdb->posts} AS post
				ON comment.comment_post_ID = post.ID
				WHERE afb.post_type = %s
				ORDER BY (afb.positive + afb.negative) DESC
EOS;
			return $wpdb->get_results($wpdb->prepare($sql, $post_type));
		}else{
			$sql = <<<EOS
				SELECT post.ID, post.post_title, afb.positive, afb.negative, (afb.positive + afb.negative) AS total, afb.updated
				FROM {$this->table} AS afb
				LEFT JOIN {$wpdb->posts} AS post
				ON afb.object_id = post.ID
				WHERE afb.post_type = %s
				ORDER BY (afb.positive + afb.negative) DESC
EOS;
			return $wpdb->get_results($wpdb->prepare($sql, $post_type));
		}
	}
	
	/**
	 * Retrive statistic inforamtion
	 * @param string $case 
	 * @param mixed $post_type string or array
	 * @return mixed
	 */
	function statistic($case, $post_type = ""){
		global $wpdb;
		switch($case){
			case "total":
				$sql = <<<EOS
					SELECT SUM(positive) AS positive, SUM(negative) AS negative
					FROM {$this->table}
EOS;
				break;
		}
		if(empty($post_type)){
			
		}elseif(is_array($post_type)){
			$where = " WHERE post_type IN (";
			$counter = 0;
			foreach($post_type as $p){
				$where .= $wpdb->prepare("%s", $p);
				$counter++;
			}
			$where .= ")";
			$sql .= $where;
		}else{
			$sql .= $wpdb->prepare(" WHERE post_type = %s", $post_type);
		}
		return $wpdb->get_row($sql);
	}

	
	/**
	 * Check version and table structure on Plugin Activation
	 * @return void
	 */
	function activate(){
		global $wpdb;
		//Check if the database exists.
		$is_db_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $this->table));
		if($is_db_exists){
			//Check if the database is old.
			if($this->db_version >= $this->version){
				//Exit and do nothing.
				return;
			}
		}
		//Here starts database update!
		//Load required files.
		require_once ABSPATH . "wp-admin/includes/upgrade.php";
		//Do dbDelta!
		dbDelta($this->sql());
		//And save current db version
		update_option("afb_db_version", $this->version);
	}
	
	/**
	 * Return Query to create table
	 * @return string
	 */
	function sql(){
		//Set character set
		$char = defined("DB_CHARSET") ? DB_CHARSET : "utf8";
		return <<<EOS
			CREATE TABLE {$this->table} (
				`ID` BIGINT(11) NOT NULL AUTO_INCREMENT,
				`object_id` BIGINT(11) NOT NULL,
				`post_type` VARCHAR(45) NOT NULL,
				`positive` BIGINT(11) NOT NULL,
				`negative` BIGINT(11) NOT NULL,
				`updated` DATETIME NOT NULL,
				UNIQUE(`ID`)
			) ENGINE = MyISAM DEFAULT CHARSET = {$char} ;
EOS;
	}
	
	/**
	 * Constructor for PHP5
	 *
	 */
	function __construct(){
		global $wpdb;
		//Start Session
		if( !isset($_SESSION) ){
			session_start();
		}
		if(!isset($_SESSION[$this->session]) || empty($_SESSION[$this->session])){
			$_SESSION[$this->session] = array();
		}
		//Set directory
		$this->dir = dirname(__FILE__);
		//option
		$this->option = get_option('afb_setting', $this->default_option);
		//Set required options for upgrade
		if( count($this->option) != count($this->default_option) ){
			foreach($this->default_option as $key => $val){
				if( !isset($this->option[$key]) ){
					$this->option[$key] = $val;
				}
			}
		}
		// Strip slashed
		if( !empty($this->option["controller"]) ){
			$this->option["controller"] = stripslashes($this->option["controller"]);
		}
		//Set Text Domain
		load_plugin_textdomain(self::$domain, false, basename($this->dir).DIRECTORY_SEPARATOR."language");
		//Define table name.
		$this->table = $wpdb->prefix.$this->table;
		//Get installed version
		$this->db_version = get_option('afb_db_version', 0);
		//Add action hook to load assets.
		add_action("wp_enqueue_scripts", array($this, "load_asset"));
		//Register Widgets
		add_action("widgets_init", array($this, "register_widgets"));
		//Add ajax handler
		//TODO: Despite Ajax request, this use init hook because Theme My Login prepend.
		add_action('init', array($this, "ajax"));
		//Add admin menu
		add_action('admin_menu', array($this, "create_admin"));
		//Add contorller
		if(!empty($this->option["post_types"])){
			//...to post
			add_filter("the_content", array($this, "the_content"));
			//...to comment
			if($this->option["comment"]){
				add_filter("comment_text", array($this, "comment_text"), 10, 2);
			}
		}
	}

}
