<?php
/*
Plugin Name: Anyway Feedback
Plugin URI: http://wordpress.org/extend/plugins/anyway-feedback/
Description: Help to assemble simple feedback(negative or positive) and get statics of them.
Version: 1.0
Author: Takahashi_Fumiki
Author URI: http://takahashifumiki.com
TextDomain: anyway-feedback
Domain Path: /language/
License: GPL2 or Later
*/


/*

Copyright 2011 Takahashi Fumiki (email : takahashi.fumiki@hametuha.co.jp)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

// Register Bootstrap
add_action("plugins_loaded", '_afb_init');

/**
 * Bootstrap
 */
function _afb_init(){
	// Set Text Domain
	load_plugin_textdomain('anyway-feedback', false, 'anyway-feedback/language');
	// Check PHP version
	if( version_compare(PHP_VERSION, '5.3.0', '<') ){
		// NG. Show message.
		add_action('admin_notices', '_afb_too_old');
	}else{
		// O.K.
		spl_autoload_register('_afb_auto_load');
		// Load functions
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions.php';
		// Load main instance.
		call_user_func(array('AFB\\Main', 'get_instance'));
		// Load Admin instance
		call_user_func(array( 'AFB\\Admin\\Screen', 'get_instance'));
	}
}

/**
 * Auto loader
 *
 * @internal
 * @ignore
 * @param string $class_name
 */
function _afb_auto_load($class_name){
	$class_name = explode('\\', trim($class_name, '\\'));
	if( 'AFB' === $class_name[0] ){
		$class_name[0] = dirname(__FILE__).DIRECTORY_SEPARATOR.'app';
		$path = implode(DIRECTORY_SEPARATOR, $class_name).'.php';
		if( file_exists($path) ){
			require_once $path;
		}
	}
}

/**
 * Show error message.
 *
 * @ignore
 * @internal
 */
function _afb_too_old(){
	$message = esc_html(sprintf(__('Oops, Anyway Feedback doesn\'t work. You PHP Version is %s but PHP 5.3 and over required.', 'anyway-feedback'), PHP_VERSION));
	echo <<<HTML
<div class="error"><p>{$message}</p></div>
HTML;

}

/**
 * Just define for translation.
 *
 * @ignore
 * @internal
 * @global Anyway_Feedback $afb
 * @return void
 */
function _afb_translation() {
	global $afb;
	$afb->_('Oops, Anyway Feedback doesn\'t work. You PHP Version is %s but PHP 5.3 and over required.');
	$afb->_( "Help to assemble simple feedback(negative or positive) and get statics of them." );
}
