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
 * The LocaleBundle manages the loading of locale resources which contain locale specific configuration,
 * validation rules (phone numbers, zip codes, etc) and inflection rules (plurals, singulars, irregulars, etc).
 *
 * @package	titon.libs.bundles.g11n
 * @uses	titon\utility\Inflector
 */
class LocaleBundle extends BundleAbstract {

	/**
	 * Define the locations for the locale resources and load the default locale.php file once initialized.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->_locations = array(
			APP_RESOURCES . 'locales/',
			TITON_RESOURCES . 'locales/'
		);

		parent::initialize();

		$this->load('locale');
	}

	/**
	 * Load the file from the resource bundle if it exists and cache the data.
	 * If the file does not exist, return an empty array.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function load($key) {
		if ($data = $this->config($key)) {
			return $data;
		}

		$filename = Inflector::filename($key, $this->config('ext'), false);

		if (in_array($filename, $this->files())) {
			$data = include_once $this->path() . $filename;
		} else {
			$data = array();
		}

		$this->configure($key, $data);

		return $data;
	}

	/**
	 * Convenience method to return the locale configuration.
	 *
	 * @access public
	 * @return array
	 */
	public function locale() {
		return $this->load('locale');
	}

	/**
	 * Convenience method to return the inflection rules.
	 *
	 * @access public
	 * @return array
	 */
	public function inflections() {
		return $this->load('inflections');
	}

	/**
	 * Convenience method to return the validation rules.
	 *
	 * @access public
	 * @return array
	 */
	public function validations() {
		return $this->load('validations');
	}

}
