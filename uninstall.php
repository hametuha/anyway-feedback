<?php
/*
 * Delete all data for Anyway Feedback
 *
 * @package AnywayFeedback
 * @since 1.0
 */

//Check whether WordPress is initialized or not.
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Delete Option
delete_option( 'afb_db_version' );
delete_option( 'afb_post_types' );
delete_option( 'afb_ga' );
delete_option( 'afb_comment' );
delete_option( 'afb_style' );
delete_option( 'afb_controller' );
delete_option( 'afb_hide_default_controller' );

// Delete table
global $wpdb;
$query = <<<SQL
	DROP TABLE IF EXISTS {$wpdb->prefix}afb_feedbacks
SQL;
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
$wpdb->query( $query );
