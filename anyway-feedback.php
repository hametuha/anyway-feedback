<?php
/**
 * Plugin Name: Anyway Feedback
 * Plugin URI: https://wordpress.org/plugins/anyway-feedback/
 * Description: Help to assemble simple feedback(negative or positive) and get statics of them.
 * Version: nightly
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: Hametuha INC.
 * Author URI: https://hametuha.co.jp
 * Text Domain: anyway-feedback
 * Domain Path: /language/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
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

defined( 'ABSPATH' ) || die( 'Do not load directly.' );

/**
 * Bootstrap
 */
function _afb_init() {
	// Set Text Domain
	load_plugin_textdomain( 'anyway-feedback', false, 'anyway-feedback/language' );
	// Check PHP version
	if ( version_compare( PHP_VERSION, '7.4.0', '<' ) ) {
		// NG. Show message.
		add_action( 'admin_notices', '_afb_too_old' );
	} else {
		// Load composer.
		require __DIR__ . '/vendor/autoload.php';
		// Load functions
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';
		// Load main instance.
		AFB\Main::get_instance();
		// Load Admin instance
		AFB\Admin\Screen::get_instance();
	}
}
add_action( 'plugins_loaded', '_afb_init' );

/**
 * Show error message.
 *
 * @ignore
 * @internal
 */
function _afb_too_old() {
	// translators: %s is PHP version.
	$message = esc_html( sprintf( __( 'Oops, Anyway Feedback doesn\'t work. You PHP Version is %s but PHP 7.4 and over required.', 'anyway-feedback' ), PHP_VERSION ) );
	echo <<<HTML
<div class="error"><p>{$message}</p></div>
HTML;
}
