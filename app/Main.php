<?php

namespace AFB;


use AFB\Api\ApiFeedback;
use AFB\Pattern\Controller;

/**
 * Main controller
 *
 * @package AFB
 */
class Main extends Controller {

	/**
	 * Constructor
	 *
	 * @param array $arguments
	 */
	protected function __construct( array $arguments = array() ) {
		// Register API
		ApiFeedback::get_instance();
		// Register Widgets
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		// Register script.
		add_action( 'init', [ $this, 'register_script' ], 9999 );
		if ( ! empty( $this->option['post_types'] ) ) {

			// Add script
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

			//...to post
			add_filter( 'the_content', array( $this, 'the_content' ) );
			if ( $this->option['comment'] ) {
				//...to comment
				add_filter( 'comment_text', array( $this, 'comment_text' ), 10, 2 );
			}
		}
		// Delete post hook
		add_action( 'after_delete_post', array( $this, 'after_delete_post' ) );
		// Delete comment hook
		add_action( 'deleted_comment', array( $this, 'deleted_comment' ) );
		// Exclude afb_negative comments from frontend queries
		add_filter( 'pre_get_comments', array( $this, 'exclude_negative_feedback_comments' ) );
	}

	/**
	 * Enqueue style
	 */
	public function wp_enqueue_scripts() {
		if ( $this->option['style'] ) {
			wp_enqueue_style( 'anyway-feedback' );
		}
	}

	/**
	 * Register scripts.
	 *
	 * @return void
	 */
	public function register_script() {
		// Google Chart
		wp_register_script( 'google-chart-api', 'https://www.google.com/jsapi', null, null );
		// Register from wp-dependencies.json
		$json = dirname( __DIR__ ) . '/wp-dependencies.json';
		if ( file_exists( $json ) ) {
			$deps = json_decode( file_get_contents( $json ), true );
			if ( ! empty( $deps ) ) {
				foreach ( $deps as $dep ) {
					if ( empty( $dep['path'] ) ) {
						continue;
					}
					$url = $this->url . $dep['path'];
					switch ( $dep['ext'] ) {
						case 'css':
							wp_register_style( $dep['handle'], $url, $dep['deps'], $dep['hash'], $dep['media'] );
							break;
						case 'js':
							$footer = [ 'in_footer' => $dep['footer'] ];
							if ( in_array( $dep['strategy'], [ 'defer', 'async' ], true ) ) {
								$footer['strategy'] = $dep['strategy'];
							}
							wp_register_script( $dep['handle'], $url, $dep['deps'], $dep['hash'], $footer );
							// Set translation
							if ( in_array( 'wp-i18n', $dep['deps'], true ) ) {
								wp_set_script_translations( $dep['handle'], 'anyway-feedback' );
							}
							break;
					}
				}
			}
		}
		// Pass PHP options to JS (translations handled by wp-i18n).
		wp_localize_script( 'anyway-feedback', 'AFBP', array(
			'ga' => (int) $this->option['ga'],
		) );
	}

	/**
	 * Default markup.
	 *
	 * @param string $message
	 * @param string $link    Deprecated. Kept for backward compatibility.
	 * @param string $useful
	 * @param string $useless
	 * @param string $status
	 *
	 * @return string
	 */
	public function default_controller_html( $message, $link, $useful, $useless, $status ) {
		return <<<HTML
<span class="message">{$message}</span>
<button class="good" type="button">{$useful}</button>
<button class="bad" type="button">{$useless}</button>
<span class="status">{$status}</span>
HTML;
	}

	/**
	 * Make controller
	 *
	 * @param int $object_id
	 * @param string $post_type
	 * @return string
	 */
	public function get_controller_tag( $object_id, $post_type ) {
		$post_type_name = ( 'comment' === $post_type ) ? __( 'Comment', 'anyway-feedback' ) : get_post_type_object( $post_type )->labels->singular_name;
		// translators: %s is post type name.
		$message = sprintf( __( 'Is this %s useful?', 'anyway-feedback' ), $post_type_name );
		// translators: %1$d is number of positive feedback, %2$d is number of total feedback.
		$status  = sprintf( __( '%1$d of %2$d people say this %3$s is useful.', 'anyway-feedback' ), afb_affirmative( false, $object_id, $post_type ), afb_total( false, $object_id, $post_type ), $post_type_name );
		$useful  = __( 'Useful', 'anyway-feedback' );
		$useless = __( 'Useless', 'anyway-feedback' );
		$url     = "#afb-{$post_type}-{$object_id}";
		$id      = esc_attr( "afb-container-{$post_type}-{$object_id}" );
		$before  = <<<HTML
<!-- Anyway Feedback Container //-->
<div class="afb_container" id="{$id}">
HTML;
		if ( empty( $this->option['controller'] ) ) {
			$before .= $this->default_controller_html( $message, esc_url( $url ), $useful, $useless, $status );
		} else {
			$replaces = array(
				'POSITIVE_RATE' => afb_positive_rate( false, $object_id, $post_type ) . '%',
				'NEGATIVE_RATE' => afb_negative_rate( false, $object_id, $post_type ) . '%',
				'POST_TYPE'     => $post_type_name,
				'LINK'          => $url,
				'POSITIVE'      => afb_affirmative( false, $object_id, $post_type ),
				'TOTAL'         => afb_total( false, $object_id, $post_type ),
				'NEGATIVE'      => afb_negative( false, $object_id, $post_type ),
			);
			$content  = stripcslashes( $this->option['controller'] );
			foreach ( $replaces as $needle => $repl ) {
				$content = str_replace( "%{$needle}%", $repl, $content );
			}
			$before .= $content;
		}
		$after = <<<HTML
<input type="hidden" name="post_type" value="{$post_type}" />
<input type="hidden" name="object_id" value="{$object_id}" />
</div>
<!-- //Anyway Feedback Container -->
HTML;
		wp_enqueue_script( 'anyway-feedback' );
		return $before . $after;
	}

	/**
	 * Add controller panel to the_content()
	 *
	 * @param string $content
	 * @return string
	 */
	public function the_content( $content ) {
		if ( in_array( get_post_type(), $this->option['post_types'], true ) && ! in_array( get_post_type(), $this->option['hide_default_controller'], true ) ) {
			$content .= $this->get_controller_tag( get_the_ID(), get_post_type() );
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
	public function comment_text( $comment_text, $comment ) {
		if ( ! is_admin() && $this->is_allowed( get_post_type( $comment->comment_post_ID ) ) ) {
			$comment_text .= str_replace( "\n", '', $this->get_controller_tag( $comment->comment_ID, 'comment' ) );
		}
		return $comment_text;
	}


	/**
	 * Delete post
	 *
	 * @param int $post_id
	 */
	public function after_delete_post( $post_id ) {
		$this->feedbacks->delete_post( $post_id );
	}

	/**
	 * Delete comment
	 *
	 * @param int $comment_id
	 */
	public function deleted_comment( $comment_id ) {
		$this->feedbacks->delete( $comment_id, 'comment' );
	}

	/**
	 * register widgets
	 *
	 */
	public function register_widgets() {
		register_widget( 'AFB\\Widget\\Popular' );
	}

	/**
	 * Exclude negative feedback comments from frontend comment queries.
	 *
	 * @param \WP_Comment_Query $query Comment query object.
	 * @return \WP_Comment_Query
	 */
	public function exclude_negative_feedback_comments( $query ) {
		// Only exclude on frontend
		if ( is_admin() ) {
			return $query;
		}

		// Get current type__not_in and add afb_negative
		$type_not_in = $query->query_vars['type__not_in'] ?? array();
		if ( ! is_array( $type_not_in ) ) {
			$type_not_in = array( $type_not_in );
		}
		$type_not_in[] = 'afb_negative';

		$query->query_vars['type__not_in'] = $type_not_in;

		return $query;
	}
}
