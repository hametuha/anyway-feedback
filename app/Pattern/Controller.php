<?php

namespace AFB\Pattern;


use AFB\Helper\i18n;
use AFB\Helper\Input;
use AFB\Model\FeedBacks;

/**
 * Controller Base
 *
 * @package AFB\Pattern
 * @property-read Input $input
 * @property-read i18n $i18n
 * @property-read FeedBacks $feedbacks
 * @property-read string $url
 * @property-read string $dir
 * @property-read array $option
 */
abstract class Controller extends Singleton
{

	public $version = '0.8';

	/**
	 * URL for assets.
	 *
	 * @param string $name
	 * @param bool $is_compressed Default false.
	 * @param string $suffix Default '.min'
	 * @return string
	 */
	public function assets_url($name, $is_compressed = false, $suffix = '.min'){
		$name = ltrim($name, '/');
		if( $is_compressed && !WP_DEBUG ){
			$name = preg_replace('/\.(css|js)$/u', $suffix.'.$1', $name);
		}
		return $this->url.'assets/'.$name;
	}

	/**
	 * Getter
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get($key){
		switch( $key ){
			case 'input':
				return Input::get_instance();
				break;
			case 'dir':
				return dirname(dirname(dirname(__FILE__)));
				break;
			case 'url':
				return plugin_dir_url(dirname(dirname(__FILE__)));
				break;
			case 'i18n':
				return i18n::get_instance();
				break;
			case 'feedbacks':
				return FeedBacks::get_instance();
				break;
			case 'option':
				$option = get_option('afb_setting', array());
				$default = array(
					"style" => 0,
					"post_types" => array(),
					"comment" => 0,
					"controller" => '',
					'ga' => false,
				);
				foreach( $default as $key => $val){
					if( !isset($option[$key]) ){
						$option[$key] = $val;
					}
				}
				return $option;
				break;
			default:
				return null;
				break;
		}
	}

} 