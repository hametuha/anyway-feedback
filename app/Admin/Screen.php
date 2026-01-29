<?php

namespace AFB\Admin;


use AFB\Pattern\Controller;

/**
 * Admin template
 *
 * @package AFB
 */
class Screen extends Controller {

	/**
	 * Constructor
	 *
	 * @param array $arguments
	 */
	protected function __construct( array $arguments = array() ) {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp_ajax_afb_chart', array( $this, 'ajax' ) );
		add_filter( 'manage_edit-comments_columns', array( $this, 'comment_columns_name' ) );
		add_action( 'manage_comments_custom_column', array( $this, 'comment_columns' ), 10, 2 );
	}

	/**
	 * Add admin menu
	 */
	public function admin_menu() {
		// Add settings page.
		add_options_page(
			__( 'Anyway Feedback Option: ', 'anyway-feedback' ),
			__( 'Anyway Feedback', 'anyway-feedback' ),
			'manage_options',
			'anyway-feedback',
			array( $this, 'render_admin' )
		);

		// Add stats.
		foreach ( $this->option['post_types'] as $post_type ) {
			$page = 'edit.php';
			if ( 'post' !== $post_type ) {
				$page .= '?post_type=' . $post_type;
			}
			$object = get_post_type_object( $post_type );
			// translators: %s is post type name.
			add_submenu_page( $page, sprintf( __( 'Feedback Statistic of %s', 'anyway-feedback' ), $object->labels->name ), __( 'Feedback Statistic', 'anyway-feedback' ), 'edit_posts', 'anyway-feedback-static-' . $post_type, array( $this, 'render_static' ) );
		}
	}

	/**
	 * Returns setting URL
	 *
	 * @param string $view
	 *
	 * @return string
	 */
	public function setting_url( $view = '' ) {
		$base = admin_url( 'options-general.php?page=anyway-feedback' );
		if ( $view ) {
			$base .= '&view=' . $view;
		}
		return esc_url( $base );
	}

	/**
	 * Load template
	 *
	 * @param string $name
	 * @param array $args
	 */
	public function load_template( $name, $args = array() ) {
		$path = $this->dir . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $name;
		if ( file_exists( $path ) ) {
			if ( ! empty( $args ) ) {
				// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
				extract( $args );
			}
			include $path;
		} else {
			printf( '<div class="error"><p>%s</p></div>', __( 'Template file doesn\'t exist. Please check whether install process is valid.', 'anyway-feedback' ) );
		}
	}

	/**
	 * Load assets
	 *
	 * @param string $page
	 */
	public function admin_enqueue_scripts( $page ) {
		$is_statistic = ( false !== strpos( $page, 'anyway-feedback-static' ) );
		if (
			$is_statistic
			|| 'settings_page_anyway-feedback' === $page
			|| 'edit-comments.php' === $page
		) {
			// Main Style sheet
			wp_enqueue_style( 'afb-admin' );
			// Script (translations handled by wp-i18n)
			wp_enqueue_script( 'afb-admin' );
		}
	}

	/**
	 * Update option
	 */
	public function admin_init() {
		if ( wp_doing_ajax() ) {
			return;
		}
		// Refresh option.
		$this->refresh_option();
		// default setting.
		add_settings_section( 'afb-default-section', __( 'Feedback Setting', 'anyway-feedback' ), function () {
			// Register something.
		}, 'anyway-feedback' );
		add_settings_section( 'afb-appearance-section', __( 'Appearance', 'anyway-feedback' ), function () {
			// Register something.
		}, 'anyway-feedback' );
		add_settings_section( 'afb-option-section', __( 'Option', 'anyway-feedback' ), function () {
			// Register something.
		}, 'anyway-feedback' );
		$settings = [
			'post_types'              => [ __( 'Post Types', 'anyway-feedback' ), 'default', [] ],
			'comment'                 => [
				__( 'Comment', 'anyway-feedback' ),
				'default',
				[
					'' => __( 'Not supported', 'anyway-feedback' ),
					1  => __( 'Allow feedback for comments', 'anyway-feedback' ),
				],
			],
			'style'                   => [
				__( 'Controller Appearance', 'anyway-feedback' ),
				'appearance',
				[
					'' => __( 'No style', 'anyway-feedback' ),
					1  => __( 'Plugin Default', 'anyway-feedback' ),
				],
			],
			'hide_default_controller' => [ __( 'Hide default feedback controller', 'anyway-feedback' ), 'appearance', [] ],
			'controller'              => [ __( 'Custom markup', 'anyway-feedback' ), 'appearance', [] ],
		];
		foreach ( $settings as $key => list( $label, $section, $options ) ) {
			add_settings_field( 'afb_' . $key, $label, function () use ( $key, $options ) {
				$option_key = 'afb_' . $key;
				$value      = get_option( $option_key );
				switch ( $key ) {
					case 'post_types':
					case 'hide_default_controller':
						$value = (array) $value;
						foreach ( get_post_types( [ 'public' => true ], OBJECT ) as $post_type ) {
							printf( '<label style="display: inline-block; margin: 0 1.5em 1.5em 0"><input type="checkbox" name="%s[]" value="%s" %s /> %s</label>',
								esc_attr( $option_key ),
								$post_type->name,
								checked( in_array( $post_type->name, $value, true ), true, false ),
								esc_html( $post_type->label )
							);
						}
						break;
					case 'comment':
					case 'style':
						foreach ( $options as $val => $text ) {
							printf( '<label style="display: inline-block; margin: 0 1.5em 1.5em 0"><input type="radio" name="%s" value="%s" %s /> %s</label>',
								esc_attr( $option_key ),
								esc_attr( $val ),
								checked( $val, $value, false ),
								esc_html( $text )
							);
						}
						break;
					case 'controller':
						printf(
							'<textarea name="%s" rows="10" style="width: 100%%; box-sizing: border-box;">%s</textarea>',
							esc_attr( $option_key ),
							esc_textarea( $value )
						);
						break;
				}
				do_action( 'afb_after_setting_field', $key, $value );
			}, 'anyway-feedback', 'afb-' . $section . '-section' );
			register_setting( 'anyway-feedback', 'afb_' . $key );
		}

		// If current user is admin, check table and try update
		if ( current_user_can( 'update_plugins' ) ) {
			if ( $this->feedbacks->try_update_db() ) {
				$message = __( 'Database has been updated.', 'anyway-feedback' );
				add_action('admin_notices', function () use ( $message ) {
					printf( '<div class="updated"><p>%s</p></div>', esc_html( $message ) );
				});
			}
		}
	}


	/**
	 * Render admin screen
	 */
	public function render_admin() {
		$this->load_template( 'admin.php' );
	}

	/**
	 * Render static screen
	 */
	public function render_static() {
		$post_type = get_post_type_object( str_replace( 'anyway-feedback-static-', '', $this->input->get( 'page' ) ) );
		$this->load_template( 'statistic.php', array(
			'post_type' => $post_type,
			'table'     => new Table( array( 'post_type' => $post_type->name ) ),
		) );
	}

	/**
	 * Process Ajax
	 */
	public function ajax() {
		if ( $this->input->check_nonce( 'afb_chart' ) ) {
			$post_type = $this->input->get( 'post_type' );
			$json      = array(
				'ratio'   => $this->feedbacks->get_ratio( $post_type ),
				'ranking' => $this->feedbacks->get_ranking( $post_type ),
			);
			wp_send_json( $json );
		}
	}

	/**
	 * Add custom column to comment list
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function comment_columns_name( $columns ) {
		$columns['feedback'] = __( 'Feedback', 'anyway-feedback' );
		return $columns;
	}

	/**
	 * Show column.
	 *
	 * @param string $column
	 * @param int $comment_id
	 */
	public function comment_columns( $column, $comment_id ) {
		switch ( $column ) {
			case 'feedback':
				$feedback = $this->feedbacks->get( $comment_id, 'comment' );
				if ( $feedback ) {
					$total    = $feedback->positive + $feedback->negative;
					$positive = floor( $feedback->positive / $total * 100 );
					printf('<div class="chart-ratio"><div style="width: %d%%"><span class="positive">%d</span><span class="negative">%d</span></div></div>',
					$positive, $feedback->positive, $feedback->negative);
				} else {
					echo '<div class="chart-ratio empty"></div>';
				}
				break;
			default:
				// Do nothing
				break;
		}
	}
}
