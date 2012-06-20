<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\bundles\locales;

use titon\Titon;
use titon\libs\bundles\BundleAbstract;
use \Locale;

/**
 * The LocaleBundle manages the loading of locale resources which contain locale specific configuration,
 * validation rules (phone numbers, zip codes, etc), inflection rules (plurals, singulars, irregulars, etc)
 * and formatting rules (dates, times, etc).
 *
 * @package	titon.libs.bundles.locales
 */
class LocaleBundle extends BundleAbstract {

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
	 * Parent locale bundle.
	 *
	 * @access protected
	 * @var titon\libs\bundles\locales\LocaleBundle
	 */
	protected $_parent;

	/**
	 * Convenience method to return the formatting rules.
	 *
	 * @access public
	 * @param string|null $key
	 * @return mixed
	 */
	public function getFormats($key = null) {
		$data = $this->get('formats');

		if ($key) {
			return isset($data[$key]) ? $data[$key] : null;
		}

		return $data;
	}

	/**
	 * Convenience method to return the inflection rules.
	 *
	 * @access public
	 * @param string|null $key
	 * @return mixed
	 */
	public function getInflections($key = null) {
		$data = $this->get('inflections');

		if ($key) {
			return isset($data[$key]) ? $data[$key] : null;
		}

		return $data;
	}

	/**
	 * Convenience method to return the locale configuration.
	 *
	 * @access public
	 * @param string|null $key
	 * @return mixed
	 */
	public function getLocale($key = null) {
		$data = $this->get('locale');

		if ($key) {
			return isset($data[$key]) ? $data[$key] : null;
		}

		return $data;
	}

	/**
	 * Return the parent bundle if it exists.
	 *
	 * @access public
	 * @return titon\libs\bundles\locales\LocaleBundle
	 */
	public function getParent() {
		return $this->_parent;
	}

	/**
	 * Convenience method to return the validation rules.
	 *
	 * @access public
	 * @param string|null $key
	 * @return mixed
	 */
	public function getValidations($key = null) {
		$data = $this->get('validations');

		if ($key) {
			return isset($data[$key]) ? $data[$key] : null;
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
		$this->setLocations(array(
			TITON_RESOURCES . 'locales/{bundle}/',
			APP_RESOURCES . 'locales/{bundle}/'
		));

		// Load locale file and load parent if one exists
		$locale = $this->getLocale();
		$locale = Locale::parseLocale($locale['id']) + $locale;

		// Cache the parent to ease overhead
		if (!empty($locale['parent'])) {
			$registry = Titon::registry();
			$registryKey = 'g11n.bundle.locale.' . $locale['parent'];

			if ($registry->has($registryKey)) {
				$parent = $registry->get($registryKey);
			} else {
				$parent = $registry->set(new LocaleBundle(array(
					'bundle' => $locale['parent']
				)), $registryKey);
			}

			if ($parent instanceof LocaleBundle) {
				$locale = $locale + $parent->getLocale();
				$this->_parent = $parent;
			}
		}

		$this->_config['locale'] = $locale;
	}

	/**
	 * Load the file from the resource bundle and parse its contents.
	 * If the bundle has a parent, merge its content with the child.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function get($key) {
		if (isset($this->_config[$key])) {
			return $this->_config[$key];
		}

		$data = parent::get($key);

		if ($parent = $this->getParent()) {
			$data = $data + $parent->get($key);
		}

		$this->_config[$key] = $data;

		return $data;
	}

}
