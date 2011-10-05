<?php

namespace titon\core;

use \titon\libs\translators\Translator;

class G11n {
	
	/**
	 * Currently active locale key based on the client.
	 * 
	 * @access protected
	 * @var string
	 */
	protected $_current;
	
	/**
	 * Fallback locale if none can be found.
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
	 * Mapping of locales (and their mappings) to the respective locale keys.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_localeMapping = array();
	
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
	 * 
	 * @access public
	 * @param string $key 
	 * @return G11n
	 * @throws CoreException
	 * @chainable
	 */
	public function apply($key) {
		if (empty($this->_locales[$key])) {
			throw new CoreException(sprintf('Locale %s does not exist.', $key));
		}
		
		$locale = $this->_locales[$this->_current];
		
		// Build array of options to set
		$options = array(
			$locale['locale'] . '.UTF8',
			$locale['locale'] . '.UTF-8',
			$locale['locale']
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

		putenv('LC_ALL=' + $locale['locale']);
		setlocale(LC_ALL, $options);
		
		if (!empty($locale['timezone'])) {
			$this->applyTimezone($locale['timezone']);
		}
		
		return $this;
	}
	
	/**
	 * Apply the timezone to use for datetime processing.
	 * 
	 * @access public
	 * @param type $timezone
	 * @return G11n 
	 * @chainable
	 */
	public function applyTimezone($timezone) {
		date_default_timezone_set($timezone);
		
		return $this;
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
	 * @return G11n 
	 * @throws CoreException
	 * @chainable
	 */
	public function fallbackAs($key) {
		if (empty($this->_locales[$key])) {
			throw new CoreException(sprintf('Locale %s does not exist.', $key));
		}

		$this->_fallback = $key;

		return $this;
	}
	
	/**
	 * Return the fallback locale.
	 * 
	 * @access public
	 * @return array
	 */
	public function getFallback() {
		return $this->_locales[$this->_fallback];
	}
	
	/**
	 * Returns the supported locales.
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
	 * @throws CoreException
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
		
		if (count($header) > 0) {
			foreach ($header as $locale) {
				if (isset($this->_localeMapping[$locale])) {
					$this->_current = $this->_localeMapping[$locale];
					break;
				}
			}
		}
		
		// Set current to the fallback if none found
		if (empty($this->_current)) {
			$this->_current = $this->_fallback;
		}
		
		// Apply the locale
		$this->apply($this->_current);
		
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
		return ($this->current('key') == $key || $this->current('locale') == $key);
	}
	
	/**
	 * G11n will be enabled if a locale has been mapped.
	 * 
	 * @access public
	 * @return boolean 
	 */
	public function isEnabled() {
		return !empty($this->_locales);
	}
	
	/**
	 * Define a key and respective locale config to use for translating content.
	 * 
	 * @access public
	 * @param string $key
	 * @param array $locale
	 * @return G11n 
	 * @throws CoreException
	 * @chainable
	 */
	public function setup($key, array $locale) {
		if (empty($locale['locale'])) {
			throw new CoreException(sprintf('Please provide a locale (xx_XX) for %s.', $key));
		}

		// Save a mapping
		$this->_localeMapping[$key] = $key;
		$this->_localeMapping[str_replace('_', '-', strtolower($locale['locale']))] = $key;

		// Set the first locale as a fallback
		if (empty($this->_fallback)) {
			$this->_fallback = $key;
			ini_set('intl.default_locale', $locale['locale']);
		}
		
		// If the fallback defined doesn't exist, error out
		if (isset($locale['fallback']) && empty($this->_locales[$locale['fallback']])) {
			throw new CoreException(sprintf('Fallback locale for %s does not exist.', $key));
		}

		$locale['key'] = $key;
		$locale['language'] = substr($locale['locale'], 0, 2);
		$locale['territory'] = strtolower(substr($locale['locale'], -2));
		
		$this->_locales[$key] = $locale;
		
		return $this;
	}
	
	/**
	 * Sets the translator to use in the string locating and translating process.
	 * 
	 * @access public
	 * @param Translator $translator
	 * @return G11n 
	 * @chainable
	 */
	public function setTranslator(Translator $translator) {
		$this->_translator = $translator;
		
		return $this;
	}
	
	/**
	 * Return a translated string using the translator. 
	 * Will use the built in MessageFormatter to parse strings with dynamic data.
	 * 
	 * @access public
	 * @param string $key
	 * @param array $params
	 * @return string
	 */
	public function translate($key, array $params = array()) {	
		return $this->_translator->translate($key, $params);
	}

}

