<?php

namespace AFB;


use AFB\Pattern\Controller;

/**
 * Main controller
 *
 * @package AFB
 */
class Main extends Controller
{
	/**
	 * Constructor
	 *
	 * @param array $arguments
	 */
	protected function __construct( array $arguments = array() ) {
		// Add ajax
		add_action('wp_ajax_nopriv_anyway_feedback', array($this, 'ajax'));
		add_action('wp_ajax_anyway_feedback', array($this, 'ajax'));
		// Register Widgets
		add_action("widgets_init", array($this, "register_widgets"));
		if( !empty($this->option['post_types']) ){

			// Add script
			add_action("wp_enqueue_scripts", array($this, "wp_enqueue_scripts"));

			//...to post
			add_filter('the_content', array($this, 'the_content'));
			if( $this->option['comment'] ){
				//...to comment
				add_filter("comment_text", array($this, "comment_text"), 10, 2);
			}
		}
		// Delete post hook
		add_action('after_delete_post', array($this, 'after_delete_post'));
		// Delete comment hook
		add_action('deleted_comment', array($this, 'deleted_comment'));
	}

	/**
	 * Enqueue script
	 */
	public function wp_enqueue_scripts(){
		if( !wp_script_is('jquery-cookie', 'registered') ){
			wp_register_script('jquery-cookie', $this->assets_url('vendor/jquery.cookie/src/jquery.cookie.js'), array('jquery'), '1.4.1', true);
		}
		wp_enqueue_script('anyway-feedback', $this->assets_url('js/anyway-feedback-handler.js', true), array('jquery-cookie'), $this->version, true);
		if( $this->option['style'] ){
			wp_enqueue_style('anyway-feedback', $this->assets_url('css/afb-style.css'), array(), $this->version, 'screen');
		}
		wp_localize_script('anyway-feedback', 'AFBP', array(
			'ga' => (int)$this->option['ga'],
			'already' => $this->i18n->_('You have already voted.')
		));
	}


	/**
	 * Make controller
	 * 
	 * @param int $object_id
	 * @param string $post_type
	 * @return string
	 */
	public function get_controller_tag($object_id, $post_type){
		$post_type_name = ($post_type == "comment") ? $this->i18n->_("Comment") : get_post_type_object($post_type)->labels->singular_name;
		$message = sprintf($this->i18n->_("Is this %s useful?"), $post_type_name);
		$status = sprintf($this->i18n->_('%1$d of %2$d people say this %3$s is useful.'), afb_affirmative(false, $object_id, $post_type), afb_total(false, $object_id, $post_type), $post_type_name);
		$useful = $this->i18n->_("Useful");
		$useless = $this->i18n->_("Useless");
		$url = admin_url('admin-ajax.php');
		$already_posted = $this->does_current_user_posted($post_type, $object_id) ? ' afb_posted' : '';
		$before = <<<HTML
<!-- Anyway Feedback Container //-->
<div class="afb_container{$already_posted}" id="afb_comment_container_{$object_id}">
HTML;
		if( empty($this->option["controller"]) ){
			$before .= <<<HTML
<span class="message">{$message}</span>
<a class="good" href="{$url}">{$useful}</a>
<a class="bad" href="{$url}">{$useless}</a>
<span class="status">{$status}</span>
HTML;
		}else{
			$replaces = array(
				"POST_TYPE" => $post_type_name,
				"LINK" => $url,
				"POSITIVE" =>  afb_affirmative(false, $object_id, $post_type),
				"TOTAL" => afb_total(false, $object_id, $post_type),
				"NEGATIVE" => afb_negative(false, $object_id, $post_type)
			);
			$content = stripcslashes($this->option["controller"]);
			foreach($replaces as $needle => $repl){
				$content = str_replace("%{$needle}%", $repl, $content);
			}
			$before .= $content;
		}
		$after = <<<HTML
<input type="hidden" name="post_type" value="{$post_type}" />
<input type="hidden" name="object_id" value="{$object_id}" />
</div>
<!-- //Anyway Feedback Container -->
HTML;
		return $before.$after;
	}

	/**
	 * Process Ajax
	 */
	public function ajax(){
		try{
			$post_type = $this->input->post('post_type');
			$object_id = $this->input->post('object_id');
			if( $this->does_current_user_posted($post_type, $object_id) ){
				throw new \Exception($this->i18n->_('Sorry, but you have already voted.'));
			}

			$post_type_name = 'comment' === $post_type ? $this->i18n->_('Comment') : get_post_type_object($post_type)->labels->singular_name;

			// Feedback request is valid.
			switch( $this->input->post("class_name") ){
				case "good":
					$affirmative = true;
					break;
				case "bad":
					$affirmative = false;
					break;
				default:
					throw new \Exception($this->i18n->_("Request is invalid."));
					break;
			}
			if( !$this->feedbacks->update($object_id, $post_type, $affirmative) ){
				if( !$this->feedbacks->add($object_id, $post_type, $affirmative) ){
					throw new \Exception($this->i18n->_("Sorry, failed to save your request. Please try again later."));
				}
			}
			// This user is posted.
			$this->user_posted($this->input->post("object_id"), $this->input->post("post_type"));
			// Create request
			$response = array(
				"success" => true,
				"message" => $this->i18n->_("Thank you for your feedback."),
				'status'  => sprintf(
					$this->i18n->_('%1$d of %2$d people say this %3$s is useful.'),
					afb_affirmative(false, $object_id, $post_type),
					afb_total(false, $object_id, $post_type),
					$post_type_name
				)
			);
		}catch ( \Exception $e ){
			$response = array(
				'success' => false,
				'message' => $e->getMessage(),
			);
		}
		//Output result as JSON.
		wp_send_json($response);
	}



	/**
	 * Add controller panel to the_content()
	 *
	 * @param string $content
	 * @return string
	 */
	function the_content($content){
		if( false !== array_search(get_post_type(), $this->option["post_types"]) ){
			$content .= $this->get_controller_tag(get_the_ID(), get_post_type());
		}
		return $content;
	}


	/**
	 * Add controller to comment
	 *
	 * @param string $comment_text
	 * @param \stdClass $comment
	 *
	 * @return string
	 */
	public function comment_text($comment_text, $comment){
		if( !is_admin() && $this->is_allowed(get_post_type($comment->comment_post_ID)) ){
			$comment_text .= str_replace("\n", "", $this->get_controller_tag($comment->comment_ID, "comment"));
		}
		return $comment_text;
	}

	/**
	 * Set cookie
	 *
	 * @param int $object_id
	 * @param string $post_type
	 */
	public function user_posted($object_id, $post_type){
		$object_id = intval($object_id);
		$cookie_name = $this->cookie_name($post_type);
		$cookie = isset($_COOKIE[$cookie_name]) ? array_filter(explode(',', $_COOKIE[$cookie_name]), function($val){
			return is_numeric($val);
		}) : array();
		if( false === array_search($object_id, $cookie) ){
			$cookie[] = $object_id;
		}
		setcookie($cookie_name, implode(',', $cookie), current_time('timestamp') + (60 * 60 * 24 * 365 * 10), '/');
	}

	/**
	 * Detect if current user has response
	 * 
	 * @param string $post_type
	 * @param int $object_id
	 *
	 * @return boolean
	 */
	public function does_current_user_posted($post_type, $object_id){
		$cookie_name = $this->cookie_name($post_type);
		if( !isset($_COOKIE[$cookie_name]) ){
			return false;
		}
		$cookie = explode(',', $_COOKIE[$cookie_name]);
		return false !== array_search($object_id, $cookie);
	}


	/**
	 * Get Cookie name
	 *
	 * @param $post_type
	 *
	 * @return string
	 */
	private function cookie_name($post_type){
		return 'afb_'.( 'comment' == $post_type ? 'comment' : 'post' );
	}

	/**
	 * Delete post
	 *
	 * @param int $post_id
	 */
	public function after_delete_post($post_id){
		$this->feedbacks->delete_post($post_id);
	}

	/**
	 * Delete comment
	 *
	 * @param int $comment_id
	 */
	public function deleted_comment($comment_id){
		$this->feedbacks->delete($comment_id, 'comment');
	}

	/**
	 * register widgets
	 *
	 */
	function register_widgets(){
		register_widget('AFB\\Widget\\Popular');
	}


} 