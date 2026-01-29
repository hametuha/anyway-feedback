<?php

namespace AFB\Api;


use AFB\Pattern\Controller;
use AFB\Pattern\UserDetector;

/**
 * Feedback API
 *
 * @package AFB
 */
class ApiFeedback extends Controller {

	use UserDetector;

	/**
	 * {@inheritDoc}
	 */
	public function __construct( array $arguments = array() ) {
		add_action( 'rest_api_init', [ $this, 'register_rest_api' ] );
	}

	/**
	 * Register REST API.
	 *
	 * @return void
	 */
	public function register_rest_api() {
		register_rest_route( 'afb/v1', '/feedback/(?P<post_type>[^/]+)/(?P<object_id>\d+)/?', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'post_feedback' ],
			'permission_callback' => [ $this, 'permission_callback' ],
			'args'                => [
				'post_type'   => [
					'required'          => true,
					'type'              => 'string',
					'validate_callback' => function ( $post_type ) {
						return ( 'comment' === $post_type ) || post_type_exists( $post_type );
					},
				],
				'object_id'   => [
					'required'          => true,
					'type'              => 'integer',
					'validate_callback' => function ( $object_id ) {
						return is_numeric( $object_id ) && 0 < $object_id;
					},
				],
				'affirmative' => [
					'required'          => true,
					'type'              => 'integer',
					'validate_callback' => function ( $affirmative ) {
						return in_array( $affirmative, [ 1, 0 ], true );
					},
				],
			],
		] );

		// Negative feedback reason endpoint
		register_rest_route( 'afb/v1', '/negative-reason/(?P<post_type>[^/]+)/(?P<object_id>\d+)/?', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'post_negative_reason' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'post_type' => [
					'required'          => true,
					'type'              => 'string',
					'validate_callback' => function ( $post_type ) {
						return ( 'comment' === $post_type ) || post_type_exists( $post_type );
					},
				],
				'object_id' => [
					'required'          => true,
					'type'              => 'integer',
					'validate_callback' => function ( $object_id ) {
						return is_numeric( $object_id ) && 0 < $object_id;
					},
				],
				'reason'    => [
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_textarea_field',
				],
			],
		] );
	}

	/**
	 * Is user allowed to post feedback?
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return bool|\WP_Error
	 */
	public function permission_callback( $request ) {
		if ( $this->does_current_user_posted( $request->get_param( 'post_type' ), $request->get_param( 'object_id' ) ) ) {
			return new \WP_Error( 'afb_already_voted', __( 'Sorry, but you have already voted.', 'anyway-feedback' ), [
				'status' => 403,
			] );
		}
		return true;
	}

	/**
	 * Handle feedback request.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function post_feedback( \WP_REST_Request $request ) {
		$post_type      = $request->get_param( 'post_type' );
		$object_id      = $request->get_param( 'object_id' );
		$post_type_name = 'comment' === $post_type ? __( 'Comment', 'anyway-feedback' ) : get_post_type_object( $post_type )->labels->singular_name;

		// Feedback request is valid.
		$affirmative = (bool) $request->get_param( 'affirmative' );
		if ( ! $this->feedbacks->update( $object_id, $post_type, $affirmative ) ) {
			if ( ! $this->feedbacks->add( $object_id, $post_type, $affirmative ) ) {
				return new \WP_Error( 'afb_update_error', __( 'Sorry, failed to save your request. Please try again later.', 'anyway-feedback' ), [
					'status' => 500,
				] );
			}
		}
		// This user is posted.
		$this->user_posted( $object_id, $post_type );
		do_action( 'afb_user_voted', $post_type, $object_id, $affirmative );
		// Create request
		return new \WP_REST_Response( [
			'success' => true,
			'message' => __( 'Thank you for your feedback.', 'anyway-feedback' ),
			'status'  => sprintf(
				// translators: %1$d is number of positive feedbacks, %2$d is total number, %3$s is post type name.
				__( '%1$d of %2$d people say this %3$s is useful.', 'anyway-feedback' ),
				afb_affirmative( false, $object_id, $post_type ),
				afb_total( false, $object_id, $post_type ),
				$post_type_name
			),
		] );
	}

	/**
	 * Handle negative feedback reason request.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function post_negative_reason( \WP_REST_Request $request ) {
		$post_type = $request->get_param( 'post_type' );
		$object_id = $request->get_param( 'object_id' );
		$reason    = $request->get_param( 'reason' );

		// Determine comment_post_ID
		if ( 'comment' === $post_type ) {
			$comment = get_comment( $object_id );
			if ( ! $comment ) {
				return new \WP_Error( 'afb_invalid_comment', __( 'Invalid comment.', 'anyway-feedback' ), [
					'status' => 400,
				] );
			}
			$comment_post_id = $comment->comment_post_ID;
		} else {
			$comment_post_id = $object_id;
		}

		// Prepare comment data
		$comment_data = [
			'comment_post_ID'  => $comment_post_id,
			'comment_content'  => $reason,
			'comment_type'     => 'afb_negative',
			'comment_approved' => 1,
		];

		// Set user data if logged in
		if ( is_user_logged_in() ) {
			$user                                 = wp_get_current_user();
			$comment_data['user_id']              = $user->ID;
			$comment_data['comment_author']       = $user->display_name;
			$comment_data['comment_author_email'] = $user->user_email;
		}

		// Insert comment (use wp_insert_comment to skip spam check and notifications)
		$comment_id = wp_insert_comment( $comment_data );

		if ( ! $comment_id ) {
			return new \WP_Error( 'afb_insert_error', __( 'Failed to save feedback reason.', 'anyway-feedback' ), [
				'status' => 500,
			] );
		}

		// Save metadata
		add_comment_meta( $comment_id, '_afb_post_type', $post_type, true );
		add_comment_meta( $comment_id, '_afb_object_id', $object_id, true );

		// If feedback was for a comment, save the source comment ID
		if ( 'comment' === $post_type ) {
			add_comment_meta( $comment_id, '_afb_source_comment_id', $object_id, true );
		}

		return new \WP_REST_Response( [
			'success' => true,
			'message' => __( 'Thank you for your feedback.', 'anyway-feedback' ),
		] );
	}
}
