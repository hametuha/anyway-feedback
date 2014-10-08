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
			if(wp_verify_nonce($nonce, "anyway_feedback") && !$this->does_current_user_posted($_REQUEST['post_type'], $_REQUEST['object_id'])){
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
					$_SESSION[$this->session][] = (string)$_POST['post_type'].'_'.$_POST['object_id'];
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
	 * Make controller
	 * @param int $object_id
	 * @param string $post_type
	 * @return string
	 */
	function get_conroller_tag($object_id, $post_type){
		global $wp_post_types;
		$nonce = wp_create_nonce("anyway_feedback");
		$post_type_name = ($post_type == "comment") ? __("Comment") : $wp_post_types[$post_type]->labels->name;
		$message = sprintf($this->_("Is this %s usefull?"), $post_type_name);
		$status = sprintf($this->_("%1\$d of %2\$d people say this %3\$s is usefull."), afb_affirmative(false, $object_id, $post_type), afb_total(false, $object_id, $post_type), $post_type_name);
		$usefull = $this->_("Usefull");
		$userless = $this->_("Useless");
		$url = $post_type == "comment" ? get_permalink() : get_permalink($object_id);
		$already_posted = $this->does_current_user_posted($post_type, $object_id) ? ' afb_posted' : '';
		$before = <<<EOS
<!-- Anyway Feedback Container //-->
<div class="afb_container{$already_posted}" id="afb_comment_container_{$object_id}">
EOS;
		if(empty($this->option["controller"])){
			$before .= <<<EOS
<span class="message">{$message}</span>
<a class="good" href="{$url}">{$usefull}</a>
<a class="bad" href="{$url}">{$userless}</a>
<span class="status">{$status}</span>
EOS;
		}else{
			$replaces = array(
				"POST_TYPE" => $post_type_name,
				"LINK" => $url,
				"POSITIVE" =>  afb_affirmative(false, $object_id, $post_type),
				"TOTAL" => afb_total(false, $object_id, $post_type),
				"NEGATIVE" => afb_negative(false, $object_id, $post_type)
			);
			$content = $this->option["controller"];
			foreach($replaces as $needle => $repl){
				$content = str_replace("%{$needle}%", $repl, $content);
			}
			$before .= $content;
		}
		$after = <<<EOS
<input type="hidden" name="post_type" value="{$post_type}" />
<input type="hidden" name="object_id" value="{$object_id}" />
<input type="hidden" name="nonce" value="{$nonce}" />
</div>
<!-- //Anyway Feedback Container -->
EOS;
		return $before.$after;
	}
	
	/**
	 * 現在のユーザーが回答済みか否か
	 * @param string $post_type
	 * @param int $object_id
	 * @return boolean 
	 */
	function does_current_user_posted($post_type, $object_id){
		if(isset($_SESSION[$this->session])){
			return (false !== array_search("{$post_type}_{$object_id}", $_SESSION[$this->session]));
		}else{
			return false;
		}
	}
	
	/**
	 * Add controller panel to the_content()
	 * 
	 * @param string $content
	 * @return string
	 */
	function the_content($content){
		if(false !== array_search(get_post_type(), $this->option["post_types"])){
			$content .= $this->get_conroller_tag(get_the_ID(), get_post_type());
		}
		return $content;
	}
	
	
	/**
	 * 
	 */
	function comment_text($comment_text, $comment){
		if(false !== array_search(get_post_type(), $this->option["post_types"])){
			$comment_text .= str_replace("\n", "", $this->get_conroller_tag($comment->comment_ID, "comment"));
		}
		return $comment_text;
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
