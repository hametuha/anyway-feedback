<?php
/*
 * Delete all data for Anyway Feedback
 * 
 * @package AnywayFeedback
 * @since 1.0
 */

//Check whether WordPress is initialized or not.
if( !defined( 'ABSPATH') || !defined('WP_UNINSTALL_PLUGIN') ){
	exit();
}

// Delete Option
delete_option('afb_db_version');
delete_option('afb_setting');

// Delete table
global $wpdb;
$query = <<<SQL
	DROP TABLE IF EXISTS {$wpdb->prefix}afb_feedbacks
SQL;
$wpdb->query($query);
