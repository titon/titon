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
	 * @return array
	 */
	public function getFormats() {
		return $this->get('formats');
	}

	/**
	 * Convenience method to return the inflection rules.
	 *
	 * @access public
	 * @return array
	 */
	public function getInflections() {
		return $this->get('inflections');
	}

	/**
	 * Convenience method to return the locale configuration.
	 *
	 * @access public
	 * @return array
	 */
	public function getLocale() {
		return $this->get('locale');
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
	 * @return array
	 */
	public function getValidations() {
		return $this->get('validations');
	}

	/**
	 * Define the locations for the locale resources and load the default locale.php file once initialized.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->findBundle(array(
			APP_RESOURCES . 'locales/{bundle}/',
			TITON_RESOURCES . 'locales/{bundle}/'
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
				$parent = $registry->store(new LocaleBundle(array(
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

	/**
	 * Parse the file at the given path and return the result.
	 *
	 * @access public
	 * @param string $filename
	 * @return array
	 */
	public function parseFile($filename) {
		return include $this->getPath() . $filename;
	}

}
