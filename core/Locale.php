<?php

namespace titon\core;

/**
 * http://ha.ckers.org/blog/20100128/micro-php-lfi-backdoor/
 */

class Locale {
	
	protected $_current = 'en-us';
	
	protected $_fallback = 'en-us';
	
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
	
	protected $_localeMapping = array();
	
	public function current($explicit = false) {
		$current = $this->_current;
		
		if (!isset($this->_locales[$current])) {
			$current = $this->_fallback;
		}
		
		if ($explicit) {
			$l10n = $this->_locales[$current];
			
			if ($explicit === true) {
				return $l10n;
				
			} else if (isset($l10n[$explicit])) {
				return $l10n[$explicit];
				
			} else {
				return null;
			}
		}
		
		return $current;
	}
	
	public function fallback($locale) {
		if (empty($this->_locales[$locale])) {
			throw new CoreException(sprintf('Locale %s does not exist.', $locale));
		}

		$this->_fallback = $locale;

		return $this;
	}
	
	public function initialize() {
		$this->_detectLocale();
		$this->_setLocale();
		
		debug($this);
	}
	
	public function setup(array $locales) {
		$this->_locales = $locales;
		
		foreach ($locales as $key => $locale) {
			if (empty($locale['locale'])) {
				throw new CoreException(sprintf('Please provide a locale (xx_XX) for %s.', $locale));
			}
			
			$this->_localeMapping[$key] = $key;
			
			if (!empty($locale['mapping'])) {
				foreach ($locale['mapping'] as $map) {
					$this->_localeMapping[strtolower($map)] = $key;
				}
			}
		}
		
		return $this;
	}
	
	public function setTimezone($timezone) {
		date_default_timezone_set($timezone);
		
		return $this;
	}
	
	public function formatLocale($locale) {
		
	}
	
	protected function _detectLocale() {
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
	}
	
	protected function _setLocale() {
		$l10n = $this->current(true);
		
		// Build array of options to set
		$options = array(
			$l10n['locale'] . '.UTF8',
			$l10n['locale'] . '.UTF-8',
			$l10n['locale']
		);
		
		if (!empty($l10n['iso3'])) {
			$options = array(
				$l10n['iso3'] . '.UTF8',
				$l10n['iso3'] . '.UTF-8',
				$l10n['iso3']
			) + $options;
		}
		
		if (!empty($l10n['iso2'])) {
			$options = array(
				$l10n['iso2'] . '.UTF8',
				$l10n['iso2'] . '.UTF-8',
				$l10n['iso2']
			) + $options;
		}
		
		$options = array(
			'eng.UTF8',
			'eng.UTF-8',
			'eng',
			'en_US'
		) + $options;

		setlocale(LC_ALL, $options);
		
		// Set the default timezone
		if (!empty($l10n['timezone'])) {
			$this->setTimezone($l10n['timezone']);
		}
	}
	
}

