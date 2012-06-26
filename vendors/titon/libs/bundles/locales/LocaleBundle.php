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
use titon\libs\readers\core\PhpReader;
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
	protected $_config = ['bundle' => ''];

	/**
	 * Locale digest configuration overrides.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_override = [];

	/**
	 * Parent locale bundle.
	 *
	 * @access protected
	 * @var titon\libs\bundles\locales\LocaleBundle
	 */
	protected $_parent;

	/**
	 * Apply the configuration and overrides.
	 *
	 * @access public
	 * @param array $config
	 * @param array $override
	 */
	public function __construct(array $config = [], array $override = []) {
		parent::__construct($config);

		$this->_override = $override;
	}

	/**
	 * Convenience method to return the formatting rules.
	 *
	 * @access public
	 * @param string|null $key
	 * @return mixed
	 */
	public function getFormats($key = null) {
		$data = $this->cache(__METHOD__, function() {
			return $this->loadResource('formats');
		});

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
		$data = $this->cache(__METHOD__, function() {
			return $this->loadResource('inflections');
		});

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
		$data = $this->cache(__METHOD__, function() {
			return $this->loadResource('locale');
		});

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
		$data = $this->cache(__METHOD__, function() {
			return $this->loadResource('validations');
		});

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
		$this->addReader(new PhpReader())->addLocation([
			TITON_RESOURCES . 'locales/{bundle}/',
			APP_RESOURCES . 'locales/{bundle}/'
		]);

		// Load locale file and load parent if one exists
		$locale = $this->getLocale();
		$locale = Locale::parseLocale($locale['id']) + $locale;
		$locale['key'] = Titon::g11n()->canonicalize($locale['id']);

		// Apply overrides, but do not allow id
		if (!empty($this->_override)) {
			unset($this->_override['id']);

			$locale = $this->_override + $locale;
		}

		// Cache the parent to ease overhead
		if (!empty($locale['parent'])) {
			$key = 'g11n.bundle.locale.' . $locale['parent'];

			if (Titon::registry()->has($key)) {
				$parent = Titon::registry()->get($key);
			} else {
				$parent = Titon::registry()->set(new LocaleBundle(['bundle' => $locale['parent']]), $key);
			}

			if ($parent instanceof LocaleBundle) {
				$locale = $locale + $parent->getLocale();

				$this->_parent = $parent;
			}
		}

		$this->setCache('titon\libs\bundles\locales\LocaleBundle::getLocale', $locale);
	}

	/**
	 * Load the file from the resource bundle and parse its contents.
	 * If the bundle has a parent, merge its content with the child.
	 *
	 * @access public
	 * @param string $resource
	 * @return array
	 */
	public function loadResource($resource) {
		$data = parent::loadResource($resource);

		if ($parent = $this->getParent()) {
			$data = array_merge($parent->loadResource($resource), $data);
		}

		return $data;
	}

}
