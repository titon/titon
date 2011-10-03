<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators;

use \titon\Titon;
use \titon\base\Base;
use \titon\libs\translators\Translator;
use \titon\libs\translators\TranslatorException;

/**
 * Abstract class that implements the string translation functionality for Translators.
 *
 * @package	titon.libs.translators
 * @abstract
 */
class TranslatorAbstract extends Base implements Translator { 
	
	/**
	 * Collection of cached localization strings.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_cache = array();
	
	/**
	 * Get a list of locales and fallback locales in descending order starting from the current locale. 
	 * This will be used to cycle through the respective domain files to find a match.
	 * 
	 * @access public
	 * @return array
	 */
	public function getFileCycle() {
		return $this->lazyLoad(__FUNCTION__, function($self) {
			$fallback = Titon::g11n()->getFallback();
			$locales = Titon::g11n()->getLocales();
			$current = Titon::g11n()->current();
			$cycle = array();

			function addToCycle($locale, $locales, &$cycle) {
				$cycle[] = $locale['locale'];

				if (strlen($locale['key']) == 2) {
					$cycle[] = $locale['key'];
				}

				if (isset($locale['fallback']) && isset($locales[$locale['fallback']])) {
					addToCycle($locales[$locale['fallback']], $locales, $cycle);
				}
			};

			addToCycle($current, $locales, $cycle);
			addToCycle($fallback, $locales, $cycle);

			return array_unique($cycle);
		});
	}
	
	/**
	 * Determine the correct file path by cycling through all the locale files.
	 * 
	 * @access public
	 * @param string $module
	 * @param string $domain
	 * @param string $ext
	 * @return string 
	 */
	public function getFilePath($module, $domain, $ext) {
		$locales = $this->getFileCycle();
		$finalPath = null;

		foreach ($locales as $locale) {
			$path = APP_MODULES . $module . DS . 'locale' . DS . $locale . DS . $domain . '.' . $ext;

			if (file_exists($path)) {
				$finalPath = $path;
				break;
			}
		}

		if ($finalPath === null) {
			throw new TranslatorException(sprintf('Translation file %s.php could not be found in the %s module for the following locales: %s.', $domain, $module, implode(', ', $locales)));
		}
		
		return $finalPath;
	}
	
	/**
	 * Locate the key within the domain file. If the domain file has not been loaded, 
	 * load it and cache the collection of strings.
	 * 
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function getMessage($key) {
		list($module, $domain, $message) = $this->parseKey($key);
		
		if (!isset($this->_cache[$module][$domain])) {
			$this->_cache[$module][$domain] = $this->loadFile($module, $domain);
		}
		
		if (isset($this->_cache[$module][$domain][$message])) {
			return $this->_cache[$module][$domain][$message];
		}
		
		throw new TranslatorException(sprintf('Message key %s does not exist.', $key));
	}

	/**
	 * Load a domain file within a specific module.
	 * 
	 * @access public
	 * @param string $module
	 * @param string $domain
	 * @return array
	 */
	public function loadFile($module, $domain) {
		throw new TranslatorException(sprintf('You must define the loadFile() method within your %s.', get_class($this)));
	}
	
	/**
	 * Parse out the module, domain and key for string lookup.
	 * 
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function parseKey($key) {
		$parts = explode('.', $key);
		
		if (count($parts) < 3 && $parts[0] != 'common') {
			throw new TranslatorException(sprintf('No module or domain preset for %s key.', $key));
		}
		
		$module = array_shift($parts);
		$domain = array_shift($parts);
		$key = implode('.', $parts);
		
		return array($module, $domain, $key);
	}

	/**
	 * Process the located string with dynamic parameters if necessary.
	 * 
	 * @access public
	 * @param string $key
	 * @param array $params
	 * @return string
	 */
	public function translate($key, array $params = array()) {	
		$locale = Titon::g11n()->current();

		return $this->getMessage($key);
		//$format = new \MessageFormatter($locale['locale'], $this->getMessage($key));
		
		//return $format->format($params);
	}
	
}
