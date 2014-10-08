<?php

namespace AFB\Admin;


use AFB\Helper\i18n;
use AFB\Pattern\Controller;

/**
 * Admin template
 *
 * @package AFB
 */
class Screen extends Controller
{

	/**
	 * Session key
	 *
	 * @var string
	 */
	private $session_key = 'afb_session';

	/**
	 * Constructor
	 *
	 * @param array $arguments
	 */
	protected function __construct( array $arguments = array() ) {
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		add_action('admin_init', array($this, 'admin_init'));
		add_action('admin_notices', array($this, 'admin_notices'));
	}

	/**
	 * Add admin menu
	 */
	public function admin_menu(){
		add_options_page(
			$this->i18n->_("Anyway Feedback Option: "),
			$this->i18n->_('Anyway Feedback'),
			'manage_options',
			"anyway-feedback",
			array($this, "render_admin")
		);

		foreach( $this->option['post_types'] as $post_type ){
			$page = 'edit.php';
			if( 'post' != $post_type ){
				$page .= '?post_type='.$post_type;
			}
			$object = get_post_type_object($post_type);
			add_submenu_page($page, sprintf($this->i18n->_('Feedback Statistic of %s'), $object->labels->name), $this->i18n->_('Feedback Statistic'), 'edit_post', 'anyway-feedback-static-'.$post_type, array($this, 'render_static'));
		}
	}

	/**
	 * Render admin screen
	 */
	public function render_admin(){
		$this->load_template('admin.php');
	}

	/**
	 * Render static screen
	 */
	public function render_static(){
		$post_type = get_post_type_object(str_replace('anyway-feedback-static-', '', $this->input->get('page')));
		$this->load_template('statistic.php', array(
			'post_type' => $post_type,
			'table' => new Table(array('post_type' => $post_type->name)),
		));
	}

	/**
	 * Returns setting URL
	 *
	 * @param string $view
	 *
	 * @return bool
	 */
	public function setting_url($view = ''){
		$base = admin_url('options-general.php?page=anyway-feedback');
		if( $view ){
			$base .= '&view='.$view;
		}
		return esc_url($base);
	}

	/**
	 * Load template
	 *
	 * @param string $name
	 * @param array $args
	 */
	public function load_template($name, $args = array()){
		$path = $this->dir.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$name;
		if( file_exists($path) ){
			if( !empty($args) ){
				extract($args);
			}
			include $path;
		}else{
			printf('<div class="error"><p>%s</p></div>', $this->i18n->_('Template file doesn\'t exist. Please check whether install process is valid.'));
		}
	}

	/**
	 * Load assets
	 *
	 * @param string $page
	 */
	public function admin_enqueue_scripts($page){

		if( ($is_statistic = (false !== strpos($page, 'anyway-feedback-static'))) || 'settings_page_anyway-feedback' === $page ){
			$deps = array('jquery');
			if( $is_statistic ){
				wp_register_script('google-chart-api', 'https://www.google.com/jsapi', null, null);
				$deps[] = 'google-chart-api';
			}
			// Main Style sheet
			wp_enqueue_style(
				'afb-admin',
				$this->assets_url("css/admin-style.css"),
				null,
				$this->version,
				'screen'
			);
			// Script
			wp_enqueue_script(
				"afb-util",
				$this->assets_url('js/admin-script.js', true),
				$deps,
				$this->version,
				true
			);
			if( $is_statistic ){
				wp_localize_script('afb-util', 'AFB', array(
					'pieTitle' => $this->i18n->_('Feedback Ratio'),
					'piePositive' => $this->i18n->_('Positive'),
					'pieNegative' => $this->i18n->_('Negative'),
					'noData' => $this->i18n->_('No data'),
				));
			}
		}
	}

	/**
	 * Update option
	 */
	public function admin_init(){
		if( !defined('DOING_AJAX') || !DOING_AJAX ){

			// If session doesn't exist, start it.
			if( !session_id() ){
				session_start();
			}
			// Check nonce and process form.
			if( $this->input->check_nonce('afb_option', '_afb_nonce') ){
				$new_option = array(
					"style" => intval((bool)$this->input->post('afb_style')),
					"post_types" => (array)$this->input->post('afb_post_types'),
					"comment" => intval((bool)$this->input->post('afb_comment')),
					"controller" => (string)$this->input->post('afb_text'),
					'ga' => intval((bool)$this->input->post('afb_ga')),
				);
				update_option('afb_setting', $new_option);

				// Add message
				$_SESSION[$this->session_key] = $this->i18n->_('Option was updated.');
				// Redirect
				wp_safe_redirect($this->setting_url());
				exit;
			}
		}else{
			add_action('wp_ajax_afb_chart', array($this, 'ajax'));
		}
	}

	/**
	 * Process Ajax
	 */
	public function ajax(){
		if( $this->input->check_nonce('afb_chart') ){
			$post_type = $this->input->get('post_type');
			$json = array(
				'ratio' => $this->feedbacks->get_ratio($post_type),
				'ranking' => $this->feedbacks->get_ranking($post_type),
			);
			wp_send_json($json);
		}
	}

	/**
	 * Show update message
	 */
	public function admin_notices(){
		if( isset($_SESSION[$this->session_key]) ){
			printf('<div class="updated"><p>%s</p></div>', esc_html($_SESSION[$this->session_key]));
			unset($_SESSION[$this->session_key]);
		}
	}

} 