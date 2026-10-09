<?php
/**
 * Smoke test for plugin bootstrap.
 *
 * @package Anyway Feedback
 */

/**
 * Check that the plugin is loaded.
 */
class Test_Plugin extends WP_UnitTestCase {

	/**
	 * Bootstrap function is hooked.
	 */
	public function test_bootstrap_hooked() {
		$this->assertNotFalse( has_action( 'plugins_loaded', '_afb_init' ) );
	}

	/**
	 * Classes are autoloaded and template tags are available.
	 */
	public function test_classes_and_functions_exist() {
		$this->assertTrue( class_exists( 'AFB\\Main' ) );
		$this->assertTrue( class_exists( 'AFB\\Admin\\Screen' ) );
		$this->assertTrue( function_exists( 'afb_display' ) );
		$this->assertTrue( function_exists( 'afb_total' ) );
		$this->assertInstanceOf( 'AFB\\Main', _afb() );
	}

	/**
	 * Main hooks are registered.
	 */
	public function test_hooks_registered() {
		$main = _afb();
		$this->assertNotFalse( has_action( 'widgets_init', [ $main, 'register_widgets' ] ) );
		$this->assertNotFalse( has_action( 'after_delete_post', [ $main, 'after_delete_post' ] ) );
		$this->assertNotFalse( has_filter( 'pre_get_comments', [ $main, 'exclude_negative_feedback_comments' ] ) );
	}

	/**
	 * REST routes are registered.
	 */
	public function test_rest_routes_registered() {
		$routes = rest_get_server()->get_routes();
		$this->assertArrayHasKey( '/afb/v1/feedback/(?P<post_type>[^/]+)/(?P<object_id>\\d+)/?', $routes );
		$this->assertArrayHasKey( '/afb/v1/negative-reason/(?P<post_type>[^/]+)/(?P<object_id>\\d+)/?', $routes );
	}
}
