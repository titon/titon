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
use \titon\libs\bundles\locales\LocaleBundle;
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
 * @uses	titon\libs\bundles\locales\LocaleBundle
 * @uses	titon\libs\storage\Storage
 * @uses	titon\libs\translators\Translator
 */
class G11n {

	/**
	 * Possible formats for locale keys.
	 */
	const FORMAT_1 = 1;
	const FORMAT_2 = 2;
	const FORMAT_3 = 3;
	
	/**
	 * Currently active locale bundle based on the client.
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
	 * Loaded locale bundles.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_locales = array();

	/**
	 * Storage engine for caching.
	 * 
	 * @access protected
	 * @var \titon\libs\storage\Storage
	 */
	protected $_storage;

	/**
	 * Translator used for string fetching and parsing.
	 * 
	 * @access protected
	 * @var \titon\libs\translators\Translator
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
		$key = $this->canonicalize($key);

		if (!isset($this->_locales[$key])) {
			throw new CoreException(sprintf('Locale %s does not exist.', $key));
		}

		$bundle = $this->_locales[$key];
		$locale = $bundle->locale();
		
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

		// Use fallback options
		$fallbackLocale = $this->getFallback()->locale();

		$options = array_merge($options, array(
			$fallbackLocale['id'] . '.UTF8',
			$fallbackLocale['id'] . '.UTF-8',
			$fallbackLocale['id']
		));

		// Set environment
		putenv('LC_ALL=' . $locale['id']);
		setlocale(LC_ALL, $options);
		
		if (!empty($locale['timezone'])) {
			$this->applyTimezone($locale['timezone']);
		}

		$this->_current = $bundle;
		
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
	 * @param boolean $full
	 * @return string
	 */
	public function canonicalize($key, $format = self::FORMAT_1, $full = false) {
		$parts = explode('-', str_replace('_', '-', strtolower($key)));

		$return = $parts[0];
		unset($parts[0]);
		
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

			unset($parts[1]);
		}

		if ($full && count($parts) > 0) {
			$return .= '-' . implode('-', $parts);
		}
		
		return $return;
	}
	
	/**
	 * Return the current locale config, or a certain value.
	 * 
	 * @access public
	 * @return \titon\libs\bundles\locales\LocaleBundle
	 */
	public function current() {
		return $this->_current;
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
		$key = $this->canonicalize($key);

		if (!isset($this->_locales[$key])) {
			throw new CoreException(sprintf('Locale %s has not been setup.', $key));
		}

		$this->_fallback = $key;

		ini_set('intl.default_locale', $this->_locales[$key]->config('locale.id'));
		
		return $this;
	}
	
	/**
	 * Return the fallback locale bundle.
	 * 
	 * @access public
	 * @return \titon\libs\bundles\locales\LocaleBundle
	 * @throws \titon\core\CoreException
	 */
	public function getFallback() {
		if (!$this->_fallback || !isset($this->_locales[$this->_fallback])) {
			throw new CoreException('Fallback locale has not been setup.');
		}
		
		return $this->_locales[$this->_fallback];
	}
	
	/**
	 * Returns the setup locales bundles.
	 * 
	 * @access public
	 * @return array
	 */
	public function getLocales() {
		return $this->_locales;
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
			throw new CoreException('A translator is required for G11n message parsing.');
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
		$locale = $this->current()->locale();

		return ($locale['key'] == $key || $locale['id'] == $key);
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
	 * Find the locale within the list of supported locale resource bundles.
	 * If a locale has a parent, merge the parent into the child to gain its values.
	 *
	 * @access public
	 * @param string $key
	 * @param array $config
	 * @return \titon\libs\bundles\locales\LocaleBundle
	 * @throws \titon\core\CoreException
	 */
	public function loadBundle($key, array $config = array()) {
		$bundle = new LocaleBundle(array(
			'folder' => $this->canonicalize($key, self::FORMAT_3),
		));

		$config = $config + $bundle->locale();

		// Merge with parent
		if (!empty($config['parent'])) {
			if (!isset($this->_locales[$config['parent']])) {
				$this->setup($config['parent']);
			}

			$config = $config + $this->_locales[$config['parent']]->locale();
		}

		// Generate meta data
		$config['key'] = $key;
		$config['language'] = substr($config['id'], 0, 2);

		if (strlen($config['id']) > 2) {
			$config['region'] = substr($config['id'], -2);
		}

		$bundle->configure('locale', $config);

		return $bundle;
	}

	/**
	 * Sets up the application with the defined locale key; the key will be formatted to a lowercase dashed URL friendly format.
	 * The system will then attempt to load the locale resource bundle and finalize configuration settings.
	 * 
	 * @access public
	 * @param string $key
	 * @param array $config
	 * @return \titon\core\G11n
	 * @chainable
	 */
	public function setup($key, array $config = array()) {
		$urlKey = $this->canonicalize($key);

		$this->_locales[$urlKey] = $this->loadBundle($key, $config);

		// Set fallback if none defined
		if (empty($this->_fallback)) {
			$this->_fallback = $urlKey;
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

