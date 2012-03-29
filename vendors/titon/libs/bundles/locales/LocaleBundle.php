<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */
 
namespace titon\libs\bundles\locales;

use \titon\libs\bundles\BundleAbstract;

/**
 * The LocaleBundle manages the loading of locale resources which contain locale specific configuration,
 * validation rules (phone numbers, zip codes, etc) and inflection rules (plurals, singulars, irregulars, etc).
 *
 * @package	titon.libs.bundles.locales
 */
class LocaleBundle extends BundleAbstract {

	/**
	 * Bundle file extension.
	 */
	const EXT = 'php';

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'bundle' => ''
	);

	/**
	 * Convenience method to return the inflection rules.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function inflections($key = null) {
		$data = $this->loadFile('inflections');

		if ($key) {
			return $this->config('inflections.' . $key);
		}

		return $data;
	}

	/**
	 * Define the locations for the locale resources and load the default locale.php file once initialized.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->_locations = array(
			APP_RESOURCES . 'locales/{bundle}/',
			TITON_RESOURCES . 'locales/{bundle}/'
		);

		parent::initialize();

		$this->locale();
	}

	/**
	 * Convenience method to return the locale configuration.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function locale($key = null) {
		$data = $this->loadFile('locale');

		if ($key) {
			return $this->config('locale.' . $key);
		}

		return $data;
	}

	/**
	 * Parse the file at the given path and return the result.
	 *
	 * @access public
	 * @param $path
	 * @return array
	 */
	public function parseFile($path) {
		return include_once $path;
	}

	/**
	 * Convenience method to return the validation rules.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function validations($key = null) {
		$data = $this->loadFile('validations');

		if ($key) {
			return $this->config('validations.' . $key);
		}

		return $data;
	}

}
