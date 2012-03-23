<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use \titon\core\CoreException;
use \titon\libs\storage\Storage;
use \titon\libs\translators\Translator;

/**
 * @todo
 *
 * @link http://en.wikipedia.org/wiki/IETF_language_tag
 * @link http://en.wikipedia.org/wiki/ISO_639
 * @link http://en.wikipedia.org/wiki/ISO_3166-1
 * @link http://loc.gov/standards/iso639-2/php/code_list.php
 *
 * @package	titon.core
 * @uses	titon\core\CoreException
 */
class G11n {

	/**
	 * Possible formats for locale keys.
	 */
	const FORMAT_1 = 1;
	const FORMAT_2 = 2;
	const FORMAT_3 = 3;
	
	/**
	 * Currently active locale key based on the client.
	 * 
	 * @access protected
	 * @var string
	 */
	protected $_current;
	
	/**
	 * Fallback locale key if none can be found.
	 * 
	 * @access protected
	 * @var string
	 */
	protected $_fallback;
	
	/**
	 * Supported locales and related meta data.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_locales = array();

	/**
	 * Storage engine for caching.
	 * 
	 * @access protected
	 * @var Storage
	 */
	protected $_storage;

	/**
	 * Translator object to use for string fetching and parsing.
	 * 
	 * @access protected
	 * @var Translator
	 */
	protected $_translator;
	
	/**
	 * Apple the locale using PHPs built in setlocale().
	 * 
	 * @link http://php.net/setlocale
	 * @link http://php.net/manual/locale.setdefault.php
	 * 
	 * @access public
	 * @param string $key 
	 * @return \titon\core\G11n
	 * @throws \titon\core\CoreException
	 * @chainable
	 */
	public function apply($key) {
		if (empty($this->_locales[$key])) {
			throw new CoreException(sprintf('Locale %s does not exist.', $key));
		}
		
		$locale = $this->loadBundle($key);
		$this->_current = $key;
		
		// Build array of options to set
		$options = array(
			$locale['id'] . '.UTF8',
			$locale['id'] . '.UTF-8',
			$locale['id']
		);
		
		if (!empty($locale['iso3'])) {
			$options = array_merge($options, array(
				$locale['iso3'] . '.UTF8',
				$locale['iso3'] . '.UTF-8',
				$locale['iso3']
			));
		}
		
		if (!empty($locale['iso2'])) {
			$options = array_merge($options, array(
				$locale['iso2'] . '.UTF8',
				$locale['iso2'] . '.UTF-8',
				$locale['iso2']
			));
		}
		
		$options = array_merge($options, array(
			'eng.UTF8',
			'eng.UTF-8',
			'eng',
			'en_US'
		));

		putenv('LC_ALL=' . $locale['id']);
		setlocale(LC_ALL, $options);
		
		if (!empty($locale['timezone'])) {
			$this->applyTimezone($locale['timezone']);
		}
		
		return $this;
	}
	
	/**
	 * Apply the timezone.
	 * 
	 * @access public
	 * @param string $timezone
	 * @return \titon\core\G11n
	 * @chainable
	 */
	public function applyTimezone($timezone) {
		date_default_timezone_set($timezone);
		
		return $this;
	}
	
	/**
	 * Convert a locale key to 3 possible formats.
	 * 
	 *	FORMAT_1 - en-us
	 *	FORMAT_2 - en-US
	 *	FORMAT_3 - en_US
	 * 
	 * @access public
	 * @param string $key
	 * @param int $format
	 * @return string
	 */
	public function canonicalize($key, $format = self::FORMAT_1) {
		$parts = explode('-', str_replace('_', '-', strtolower($key)));
		$return = $parts[0];
		
		if (isset($parts[1])) {
			switch ($format) {
				case self::FORMAT_1:
					$return .= '-' . $parts[1];
				break;
				case self::FORMAT_2:
					$return .= '-' . strtoupper($parts[1]);
				break;
				case self::FORMAT_3:
					$return .= '_' . strtoupper($parts[1]);
				break;
			}
		}
		
		return $return;
	}
	
	/**
	 * Return the current locale config, or a certain value.
	 * 
	 * @access public
	 * @param string $key
	 * @return string|array
	 */
	public function current($key = null) {
		$locale = $this->_locales[$this->_current];
		
		if (isset($locale[$key])) {
			return $locale[$key];
		}
		
		return $locale;
	}
	
	/**
	 * Define the fallback language if none can be found or is not supported.
	 * 
	 * @access public
	 * @param string $key
	 * @return \titon\core\G11n
	 * @throws \titon\core\CoreException
	 * @chainable
	 */
	public function fallbackAs($key) {
		if (empty($this->_locales[$key])) {
			throw new CoreException(sprintf('Locale %s has not been setup.', $key));
		}

		$this->_fallback = $key;
		
		$locale = $this->loadBundle($key);
		
		ini_set('intl.default_locale', $locale['id']);
		
		return $this;
	}
	
	/**
	 * Return the fallback locale.
	 * 
	 * @access public
	 * @return array
	 * @throws \titon\core\CoreException
	 */
	public function getFallback() {
		if (!$this->_fallback || empty($this->_locales[$this->_fallback])) {
			throw new CoreException('Fallback locale has not been setup.');
		}
		
		return $this->_locales[$this->_fallback];
	}
	
	/**
	 * Returns the setup locales.
	 * 
	 * @access public
	 * @return array
	 */
	public function getLocales() {
		return $this->_locales;
	}
	
	/**
	 * Returns the supported locales based on the resources/locales folder.
	 *
	 * @todo Memoizer
	 * 
	 * @access public
	 * @return array
	 */
	public function getSupportedBundles() {
		if (!empty($this->_supportedLocales)) {
			return $this->_supportedLocales;
		}

		$this->_supportedLocales = array_map('basename', glob(RES_LOCALES . '*', GLOB_ONLYDIR));

		return $this->_supportedLocales;
	}
	
	/**
	 * Detect which locale to use based on the clients Accept-Language header.
	 * 
	 * @access public
	 * @throws \titon\core\CoreException
	 * @return void
	 */
	public function initialize() {
		if (!$this->isEnabled()) {
			return;
		}
		
		$header = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);

		if (strpos($header, ';') !== false) {
			$header = strstr($header, ';', true);
		}
		
		$header = explode(',', $header);
		$current = null;
		
		if (count($header) > 0) {
			foreach ($header as $locale) {
				if (isset($this->_locales[$locale])) {
					$current = $locale;
					break;
				}
			}
		}
		
		// Set current to the fallback if none found
		if ($current == null) {
			$current = $this->_fallback;
		}
		
		// Apply the locale
		$this->apply($current);
		
		// Check for a translator
		if (empty($this->_translator)) {
			throw new CoreException('A translator is required for G11n string parsing.');
		}
	}
	
	/**
	 * Does the current locale matched the passed key?
	 * 
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function is($key) {
		return ($this->current('key') == $key || $this->current('id') == $key);
	}
	
	/**
	 * G11n will be enabled if a locale has been setup.
	 * 
	 * @access public
	 * @return boolean 
	 */
	public function isEnabled() {
		return !empty($this->_locales);
	}

	/**
	 * Find the locale within the list of supported locale bundles based on the given key.
	 * If a locale has a parent, merge the parent into the child to gain its values.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 * @throws \titon\core\CoreException
	 */
	public function loadBundle($key) {
		$id = $this->canonicalize($key, self::FORMAT_3);
		$folder = RES_LOCALES . $id . '/';
		$path = $folder . 'locale.php';

		if (!file_exists($folder) || !file_exists($path)) {
			throw new CoreException(sprintf('%s is not a supported locale.', $id));
		}

		$locale = include $path;

		if (isset($locale['fallback'])) {
			$locale = $locale + $this->loadBundle($locale['fallback']);
		}

		$locale['key'] = $key;
		$locale['language'] = substr($locale['id'], 0, 2);

		if (strlen($locale['id']) > 2) {
			$locale['region'] = substr($locale['id'], -2);
		}

		return $locale;
	}

	/**
	 * Accepts a list of locale keys to setup the application with. 
	 * The list may accept the locale key in the array index or value position. 
	 * If the locale key is placed in the index, the value may consist of an array to overwrite with.
	 * 
	 * @access public
	 * @param array $keys
	 * @return \titon\core\G11n
	 * @chainable
	 */
	public function setup(array $keys) {
		foreach ($keys as $key => $locale) {
			if (is_string($locale)) {
				$key = $locale;
				$locale = array();
			}
			
			$this->_locales[$key] = $locale + $this->loadBundle($key);
		}
		
		return $this;
	}
	
	/**
	 * Sets the translator to use in the string locating and translating process.
	 * 
	 * @access public
	 * @param \titon\libs\translators\Translator $translator
	 * @return \titon\core\G11n
	 * @chainable
	 */
	public function setTranslator(Translator $translator) {
		$this->_translator = $translator;
		
		return $this;
	}
	
	/**
	 * Set the storage engine to use for catalog caching.
	 * 
	 * @access public
	 * @param \titon\libs\storage\Storage $storage
	 * @return \titon\core\G11n
	 * @chainable
	 */
	public function setStorage(Storage $storage) {
		$this->_storage = $storage;
		$this->_storage->configure('storage', 'g11n');

		return $this;
	}
	
	/**
	 * Return a translated string using the translator.
	 * If a storage engine is present, read and write from the cache.
	 * 
	 * @access public
	 * @param string $key
	 * @param array $params
	 * @return string
	 */
	public function translate($key, array $params = array()) {	
		list($module, $catalog) = $this->_translator->parseKey($key);
		
		$cacheKey = $module . '.' . $catalog . '.' . $this->current('id');

		if ($this->_storage instanceof Storage) {
			$messages = $this->_storage->get($cacheKey);
			
			if (empty($messages)) {
				$messages = $this->_translator->translate($key, $params);
				$this->_storage->set($cacheKey, $messages);
			}
		} else {
			$messages = $this->_translator->translate($key, $params);
		}
		
		return $messages;
	}

}

