<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */
 
namespace titon\libs\bundles\g11n;

use \titon\libs\bundles\BundleAbstract;
use \titon\utility\Inflector;

/**
 * @todo
 *
 * @package	titon.libs.bundles.g11n
 */
class LocaleBundle extends BundleAbstract {

	/**
	 * Define the locations for the locale resources.
	 *
	 * @access public
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		$this->_locations = array(
			'app' => APP_RESOURCES . 'locales/',
			'titon' => TITON_RESOURCES . 'locales/'
		);

		parent::__construct($config);
	}

	/**
	 * Load the default locale file once initialized.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		parent::initialize();

		$this->load('locale');
	}

	/**
	 * Load the file from the resource bundle if it exists and cache the data.
	 * If the file does not exist, return an empty array.
	 *
	 * @access public
	 * @param string $key
	 * @return void
	 */
	public function load($key) {
		if (isset($this->_data[$key])) {
			return $this->_data[$key];
		}

		$filename = Inflector::filename($key, $this->config('ext'), false);
		$path = $this->path() . $filename;

		if (in_array($filename, $this->files())) {
			$data = include_once $path;
		} else {
			$data = array();
		}

		$this->_data[$key] = $data;

		return $data;
	}

}
