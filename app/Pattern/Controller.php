<?php

namespace AFB\Pattern;


use AFB\Helper\Input;
use AFB\Model\FeedBacks;

/**
 * Controller Base
 *
 * @package AFB\Pattern
 * @property-read Input $input
 * @property-read FeedBacks $feedbacks
 * @property-read string $url
 * @property-read string $dir
 * @property-read array $option
 */
abstract class Controller extends Singleton {

	/**
	 * @var string Version number.
	 */
	public $version = '1.1.0';

	/**
	 * URL for assets.
	 *
	 * @deprecated
	 * @param string $name
	 * @param bool $is_compressed Default false.
	 * @param string $suffix Default '.min'
	 * @return string
	 */
	public function assets_url( $name, $is_compressed = false, $suffix = '.min' ) {
		$dir = 'assets/';
		return $this->url . $dir . $name;
	}

	/**
	 * Get assets hash for versioning.
	 *
	 * @param string $path
	 * @return string
	 */
	public function assets_hash( $path ) {
		$path = $this->dir . '/assets/' . ltrim( $path, '/' );
		if ( file_exists( $path ) ) {
			return md5_file( $path );
		}
		return $this->version;
	}

	/**
	 * Detect if this post type is allowed
	 *
	 * @param string $post_type
	 *
	 * @return bool
	 */
	public function is_allowed( $post_type ) {
		return in_array( $post_type, $this->option['post_types'], true );
	}

	/**
	 * If option is old format, update.
	 *
	 * @return void
	 */
	protected function refresh_option() {
		$option = get_option( 'afb_setting', [] );
		if ( ! empty( $option ) ) {
			foreach ( [
				'style'                   => 'bool',
				'post_types'              => 'array',
				'hide_default_controller' => 'array',
				'comment'                 => 'bool ',
				'controller'              => 'string',
			] as $key => $format ) {
				$value = $option[ $key ] ?? null;
				switch ( $format ) {
					case 'bool':
						$value = $value ? '1' : '';
						break;
					case 'array':
						$value = (array) $value;
						break;
				}
				update_option( 'afb_' . $key, $value );
			}
			delete_option( 'afb_setting' );
		}
	}

	/**
	 * Force array.
	 *
	 * @param string|array $value Option value.
	 *
	 * @return string[]
	 */
	private function force_array( $value ) {
		if ( ! is_array( $value ) ) {
			$value = (array) $value;
		}
		return array_values( array_filter( $value ) );
	}

	/**
	 * Getter
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'input':
				return Input::get_instance();
			case 'dir':
				return dirname( __DIR__, 2 );
			case 'url':
				return plugin_dir_url( dirname( __DIR__, 1 ) );
			case 'feedbacks':
				return FeedBacks::get_instance();
			case 'option':
				return [
					'style'                   => (bool) get_option( 'afb_style', '' ),
					'post_types'              => $this->force_array( get_option( 'afb_post_types', [] ) ),
					'hide_default_controller' => $this->force_array( get_option( 'afb_hide_default_controller', [] ) ),
					'comment'                 => (bool) get_option( 'afb_comment', '' ),
					'controller'              => get_option( 'afb_controller', '' ),
				];
			default:
				return null;
		}
	}
}
