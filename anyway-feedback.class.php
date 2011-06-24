<?php
/**
 * Core class for Anyway Feedback
 * 
 * @package Anyway Feedback
 * @since 0.1
 */

class Anyway_Feedback{
	
	/**
	 * Version of this plugin
	 * @var float
	 */
	var $version = 0.3;
	
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
	 * Domain name for i18n
	 * @var string
	 */
	static $domain = "anyway-feedback";
	
	/**
	 * Retrieve data from table
	 * @param int $object_id
	 * @param string $post_type
	 * @return object
	 */
	function get($object_id, $post_type = "post"){
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table} WHERE object_id = %d AND post_type = %s", $object_id, $post_type));
	}
		
	/**
	 * Add new data
	 * 
	 * @param int $oject_id
	 * @param string $post_type (optional)
	 * @param  boolean $affirmative (optional) if false,negative. default true.
	 * @return int 
	 */
	function add($object_id, $post_type = "post", $affirmative = true){
		global $wpdb;
		$data = array(
			"object_id" => $object_id,
			"post_type" => $post_type,
			"updated" => date("Y-m-d H:i:s")
		);
		if($affirmative){
			$data["positive"] = 1;
		}else{
			$data["negative"] = 1;
		}
		$result = $wpdb->insert($this->table, $data, array("%d", "%s", "%s", "%d") );
		if($result){
			return $wpdb->insert_id;
		}else{
			return 0;
		}
	}
	
	/**
	 * Update data
	 * 
	 * @param int $oject_id
	 * @param string $post_type (optional)
	 * @param  boolean $affirmative (optional) if false,negative. default true.
	 * @return boolean
	 */
	function update($object_id, $post_type = "post", $affirmative = true){
		global $wpdb;
		$column = $affirmative ? "positive" : "negative";
		$sql = <<<EOS
			UPDATE {$this->table}
			SET
				`$column` = {$column}+1,
				`updated` = %s
			WHERE
				`object_id` = %d
			AND `post_type` = %s
EOS;
		//Try updating and get updated rows.
		$result = $wpdb->query($wpdb->prepare($sql, date("Y-m-d H:i:s"), $object_id, $post_type));
		return (boolean) $result;
	}
	
	/**
	 * Delete Data
	 * @param int $object_id
	 * @param string $post_type
	 * @return booelan
	 */
	function delete($object_id, $post_type = "post"){
		global $wpdb;
		$sql = <<<EOS
			DELETE FROM {$this->table} WHERE object_id = %d AND post_type = %s
EOS;
		return (boolean) $wpdb->query($sql, $object_id, $post_type);
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
		require_once ABSPATH."wp-admin/includes/upgrade.php";
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
	 * Constructor for PHP4
	 * @param float $version
	 * @return void
	 */
	function Anyway_Feedback(){
		$this->__construct();
	}
	
	/**
	 * Constructor for PHP5
	 * @param float $version
	 * @return void
	 */
	function __construct(){
		global $wpdb;
		//Set directory
		$this->dir = dirname(__FILE__);
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
	}
	
	/**
	 * Handling Ajax
	 * 
	 * @return void
	 */
	function ajax(){
		if(isset($_POST["action"]) && $_POST["action"] == "anyway_feedback"){
			$nonce = isset($_REQUEST["nonce"]) ? $_REQUEST["nonce"] : "boo!";
			$response = array(
				"success" => true,
				"message" => $this->_("Thank you for your feedback.")
			);
			if(wp_verify_nonce($nonce, "anyway_feedback")){
				//Feedback request is valid.
				switch($_POST["class_name"]){
					case "good":
						$affirmative = true;
						break;
					case "bad":
						$affirmative = false;
						break;
					default:
						$response["success"] = false;
						$response["message"] = $this->_("Request is invalid.");
						break;
				}
				//If no error, try updating
				if($response["success"]){
					if(!$this->update($_POST["object_id"], $_POST["post_type"], $affirmative)){
						$this->add($_POST["object_id"], $_POST["post_type"], $affirmative);
					}
				}
			}else{
				//Error.
				$response["success"] = false;
				$response["message"] = $this->_("Request is invalid.");
			}
			//Output result as JSON.
			header("Content-Type: application/json");
			echo json_encode($response);
			//Don't forget exit.
			exit;
		}
	}
	
	/**
	 * Load Javascript
	 * @return void
	 */
	function load_asset(){
		//Load JS file.
		wp_enqueue_script(
			"anyway-feedback",
			plugin_dir_url(__FILE__)."assets/anyway-feedback-handler.js",
			array("jquery"),
			$this->version,
			true
		);
	}
	
	/**
	 * Alias for gettext _e function
	 * @param string $string
	 * @return void
	 */
	function e($string){
		_e($string, self::$domain);
	}
	
	/**
	 * Alias for gettext __ function
	 * @param string $string
	 * @return string
	 */
	function _($string){
		return __($string, self::$domain);
	}
	
	/**
	 * register widgets
	 *
	 * @return void
	 */
	function register_widgets(){
		return register_widget('Anyway_Feedback_Popular');
	}
}
