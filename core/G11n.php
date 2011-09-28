<?php

namespace titon\core;

use \titon\libs\translators\Translator;

class G11n {
	
	/**
	 * Currently active locale.
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
	 * Mapping of locales and related meta data.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_locales = array(
		'en' => array(
			'language' => 'English (United States)',
			'iso2' => 'us',
			'iso3' => 'usa',
			'locale' => 'en_US',
			'timezone' => 'America/New_York',
			'mapping' => array('en', 'en-us', 'en_us')
		)
	);
	
	/**
	 * Mapping of locales (and their mappings) to the respective locale keys.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_localeMapping = array();
	
	/**
	 * Translator class to use for string fetching and parsing.
	 * 
	 * @access protected
	 * @var Translator
	 */
	protected $_translator;
	
	/**
	 * Return the currently set locale key.
	 * 
	 * @access public
	 * @return string
	 */
	public function current() {
		return $this->_locales[$this->_current];
	}
	
	/**
	 * Define the fallback language if none can be found or is not supported.
	 * 
	 * @access public
	 * @param string $key
	 * @return G11n 
	 * @chainable
	 */
	public function fallback($key) {
		if (empty($this->_locales[$key])) {
			throw new CoreException(sprintf('Locale %s does not exist.', $key));
		}

		$this->_fallback = $key;

		return $this;
	}
	
	/**
	 * Detect which locale to use based on the clients Accept-Language header.
	 * 
	 * @access public
	 * @return void
	 */
	public function initialize() {
		if (empty($this->_locales)) {
			// Don't do anything if G11n isn't being used
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
		$this->setLocale($this->_current);
		
		// Check for a translator
		if (empty($this->_translator)) {
			throw new CoreException('A translator is required for G11n string parsing.');
		}
	}
	
	/**
	 * Setup the application with a list of supported locales. 
	 * These locales define the list of translatable content.
	 * 
	 * @access public
	 * @param array $locales
	 * @return G11n 
	 * @chainable
	 */
	public function setup(array $locales) {
		foreach ($locales as $key => &$locale) {
			if (empty($locale['locale'])) {
				throw new CoreException(sprintf('Please provide a locale (xx_XX) for %s.', $locale));
			}
			
			$this->_localeMapping[$key] = $key;
			
			if (!empty($locale['mapping'])) {
				foreach ($locale['mapping'] as $map) {
					$this->_localeMapping[strtolower($map)] = $key;
				}
			}
			
			if (empty($this->_fallback)) {
				$this->_fallback = $key;
			}
			
			$locale['key'] = $key;
		}
		
		$this->_locales = $locales;
		
		return $this;
	}
	
	/**
	 * Trigger PHPs built in setlocale().
	 * 
	 * @link http://php.net/setlocale
	 * 
	 * @access public
	 * @param string $key 
	 * @return G11n
	 * @chainable
	 */
	public function setLocale($key) {
		if (empty($this->_locales[$key])) {
			throw new CoreException(sprintf('Locale %s does not exist.', $key));
		}
		
		$bundle = $this->_locales[$key];
		
		// Build array of options to set
		$options = array(
			$bundle['locale'] . '.UTF8',
			$bundle['locale'] . '.UTF-8',
			$bundle['locale']
		);
		
		if (!empty($bundle['iso3'])) {
			$options = array(
				$bundle['iso3'] . '.UTF8',
				$bundle['iso3'] . '.UTF-8',
				$bundle['iso3']
			) + $options;
		}
		
		if (!empty($bundle['iso2'])) {
			$options = array(
				$bundle['iso2'] . '.UTF8',
				$bundle['iso2'] . '.UTF-8',
				$bundle['iso2']
			) + $options;
		}
		
		$options = array(
			'eng.UTF8',
			'eng.UTF-8',
			'eng',
			'en_US'
		) + $options;

		setlocale(LC_ALL, $options);
		
		if (!empty($bundle['timezone'])) {
			$this->setTimezone($bundle['timezone']);
		}
		
		return $this;
	}
	
	/**
	 * Set the timezone for datetime formatting.
	 * 
	 * @link http://php.net/manual/timezones.php
	 * 
	 * @access public
	 * @param type $timezone
	 * @return G11n 
	 * @chainable
	 */
	public function setTimezone($timezone) {
		date_default_timezone_set($timezone);
		
		return $this;
	}
	
	/**
	 * Set the translator to use.
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
	 * Return a translated string using the translator. Will use the built in 
	 * MessageFormatter to parse strings with dynamic data.
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

