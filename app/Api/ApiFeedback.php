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
}
