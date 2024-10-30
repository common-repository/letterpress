<?php
namespace letterpress;
if ( ! defined( 'ABSPATH' ) ) exit;

class Init {
	public $components = array();
	public $h;
	public $url;

	public function __construct() {
		$this->url = plugin_dir_url( LPRESS_FILE );

		$this->register_autoloader();
		$this->h = new Helper();
	}

	public function run() {
		$this->init_components();
	}

	public function init_components() {
		$this->components['installation']     = new Installation();
		$this->components['rewrite_url_rule'] = new Rewrite_URL();
		$this->components['admin']            = new Admin();
		$this->components['cron']             = new Cron();

		return $this->components;
	}

	private function register_autoloader() {
		spl_autoload_register( array( $this, 'loader' ) );
	}

	private static function loader( $className ) {
		if ( ! class_exists( $className ) ) {
			$className = preg_replace(
				array( '/([a-z])([A-Z])/', '/\\\/' ),
				array( '$1-$2', DIRECTORY_SEPARATOR ),
				$className
			);

			$className = str_replace( 'letterpress' . DIRECTORY_SEPARATOR, '', $className );

			$file_name = LPRESS_PATH . 'classes' . DIRECTORY_SEPARATOR . $className . '.php';

			if ( file_exists( $file_name ) && is_readable( $file_name ) ) {
				require_once $file_name;
			}
		}
	}
}