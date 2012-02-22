<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators;

use \titon\Titon;
use \titon\base\Base;
use \titon\libs\traits\Memoizer;
use \titon\libs\translators\Translator;
use \titon\libs\translators\TranslatorException;

/**
 * Abstract class that implements the string translation functionality for Translators.
 *
 * @package	titon.libs.translators
 * @uses	titon\Titon
 * @abstract
 */
class TranslatorAbstract extends Base implements Translator { 
	use Memoizer;
	
	/**
	 * Collection of cached localization strings.
	 * 
	 * @access public
	 * @var array
	 */
	public $cache = array();
	
	/**
	 * Get a list of locales and fallback locales in descending order starting from the current locale. 
	 * This will be used to cycle through the respective catalogs to find a match.
	 * 
	 * @access public
	 * @return array
	 */
	public function getFileCycle() {
		return $this->cacheMethod(__FUNCTION__, null, function($self) {
			$fallback = Titon::g11n()->getFallback();
			$current = Titon::g11n()->current();
			$cycle = array();

			function addToCycle($locale, &$cycle) {
				$cycle[] = $locale['id'];

				if ($locale['id'] != $locale['iso2']) {
					$cycle[] = $locale['iso2'];
				}
			};

			addToCycle($current, $cycle);
			
			if (!empty($fallback) && $fallback['key'] != $current['key']) {
				addToCycle($fallback, $cycle);
			}

			return array_unique($cycle);
		});
	}
	
	/**
	 * Determine the correct file path by cycling through all the locale catalogs.
	 * 
	 * @access public
	 * @param string $module
	 * @param string $catalog
	 * @param string $ext
	 * @return string 
	 * @throws \titon\libs\translators\TranslatorException
	 */
	public function getFilePath($module, $catalog, $ext) {
		$locales = $this->getFileCycle();
		$finalPath = null;

		foreach ($locales as $locale) {
			$path = APP_MODULES . $module . DS . 'locale' . DS . $locale . DS . $catalog . '.' . $ext;

			if (file_exists($path)) {
				$finalPath = $path;
				break;
			}
		}

		if ($finalPath === null) {
			throw new TranslatorException(sprintf('Translation file %s could not be found in the %s module for the following locales: %s.', $catalog, $module, implode(', ', $locales)));
		}
		
		return $finalPath;
	}
	
	/**
	 * Locate the key within the catalog. If the catalog has not been loaded, 
	 * load it and cache the collection of strings.
	 * 
	 * @access public
	 * @param string $key
	 * @return string
	 * @throws \titon\libs\translators\TranslatorException
	 */
	public function getMessage($key) {
		return $this->cacheMethod(__FUNCTION__, $key, function($self) use ($key) {
			list($module, $catalog, $message) = $self->parseKey($key);

			if (!isset($self->cache[$module][$catalog])) {
				$self->cache[$module][$catalog] = $self->parseFile($module, $catalog);
			}

			if (isset($self->cache[$module][$catalog][$message])) {
				return $self->cache[$module][$catalog][$message];
			}

			throw new TranslatorException(sprintf('Message key %s does not exist.', $key));
		});
	}

	/**
	 * Load a catalog from a specific module.
	 * 
	 * @access public
	 * @param string $module
	 * @param string $catalog
	 * @return array
	 * @throws \titon\libs\translators\TranslatorException
	 */
	public function parseFile($module, $catalog) {
		throw new TranslatorException(sprintf('You must define the parseFile() method within your %s.', get_class($this)));
	}
	
	/**
	 * Parse out the module, catalog and key for string lookup.
	 * 
	 * @access public
	 * @param string $key
	 * @return array
	 * @throws \titon\libs\translators\TranslatorException
	 */
	public function parseKey($key) {
		return $this->cacheMethod(__FUNCTION__, $key, function($self) use ($key) {
			$parts = explode('.', $key);

			if (count($parts) < 3) {
				throw new TranslatorException(sprintf('No module or catalog present for %s key.', $key));
			}

			$module = array_shift($parts);
			$catalog = array_shift($parts);
			$key = implode('.', $parts);

			return array($module, $catalog, $key);
		});
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
		return $this->getMessage($key);
		//return \MessageFormatter::parseMessage(Titon::g11n()->current('locale'), $this->getMessage($key));
	}
	
}
