<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators;

use titon\Titon;
use titon\base\Base;
use titon\libs\storage\Storage;
use titon\libs\traits\Memoizer;
use titon\libs\translators\Translator;
use titon\libs\translators\TranslatorException;
use \MessageFormatter;
use \Locale;

/**
 * Abstract class that implements the string translation functionality for Translators.
 *
 * @package	titon.libs.translators
 * @uses	titon\Titon
 * @uses	titon\libs\storage\Storage
 * @abstract
 */
abstract class TranslatorAbstract extends Base implements Translator {
	//use Memoizer;

	/**
	 * List of MessageBundle's.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_bundles = array();

	/**
	 * Cache the messages for easy lookup.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_cache = array();

	/**
	 * Storage engine for caching.
	 *
	 * @access protected
	 * @var titon\libs\storage\Storage
	 */
	protected $_storage;

	/**
	 * Get a list of locales and fallback locales in descending order starting from the current locale. 
	 * This will be used to cycle through the respective bundles and catalogs to find a match.
	 * 
	 * @access public
	 * @return array
	 */
	public function getFileCycle() {
		return $this->cacheMethod(__FUNCTION__, null, function($self) {
			$cycle = array();

			function addToCycle($bundle, &$cycle) {
				$locale = $bundle->getLocale();
				$parent = $bundle->getParent();

				$cycle[] = $locale['id'];

				if ($parent) {
					addToCycle($parent, $cycle);
				}
			}

			addToCycle(Titon::g11n()->current(), $cycle);
			addToCycle(Titon::g11n()->getFallback(), $cycle);

			return array_unique($cycle);
		});
	}

	/**
	 * Locate the key within the catalog. If the catalog has not been loaded, 
	 * load it and cache the collection of strings.
	 * 
	 * @access public
	 * @param string $key
	 * @return string
	 * @throws titon\libs\translators\TranslatorException
	 */
	public function getMessage($key) {
		if (isset($this->_cache[$key])) {
			return $this->_cache[$key];
		}

		list($module, $catalog, $id) = $this->parseKey($key);

		// Cycle through each locale till a message is found
		$locales = $this->getFileCycle();

		foreach ($locales as $locale) {
			$cacheKey = 'g11n.' . ($module == null ? 'root' : $module) . '.' . $catalog . '.' . $locale;
			$messages = array();

			// Check within the cache first
			if ($this->_storage instanceof Storage) {
				$messages = $this->_storage->get($cacheKey);
			}

			// Else check within the bundle
			if (empty($messages)) {
				$bundleKey = $module . '.' . $locale;

				if (!isset($this->_bundles[$bundleKey])) {
					$this->_bundles[$bundleKey] = $this->loadBundle($module, $locale);
				}

				$bundle = $this->_bundles[$bundleKey];

				// If the catalog doesn't exist, try the next locale
				if ($bundle->hasFile($catalog)) {
					$messages = $bundle->loadFile($catalog);
				}
			}

			// Return message if it exists, else continue cycle
			if (isset($messages[$id])) {
				if ($this->_storage instanceof Storage) {
					$this->_storage->set($cacheKey, $messages);
				}

				$this->_cache[$key] = $messages[$id];

				return $messages[$id];
			}
		}

		throw new TranslatorException(sprintf('Message key %s does not exist in: %s.', $key, implode(', ', $locales)));
	}

	/**
	 * Load the correct resource bundle for the associated file type.
	 *
	 * @access public
	 * @param string $module
	 * @param string $locale
	 * @return titon\libs\bundles\Bundle
	 */
	public function loadBundle($module, $locale) {
		throw new TranslatorException(sprintf('You must define the loadBundle() method within your %s.', get_class($this)));
	}

	/**
	 * Parse out the module, catalog and key for string lookup.
	 * 
	 * @access public
	 * @param string $key
	 * @return array
	 * @throws titon\libs\translators\TranslatorException
	 */
	public function parseKey($key) {
		return $this->cacheMethod(__FUNCTION__, $key, function($self) use ($key) {
			$parts = explode('.', $key);
			$count = count($parts);
			$module = null;
			$catalog = null;

			if ($count < 2) {
				throw new TranslatorException(sprintf('No module or catalog present for %s key.', $key));

			} else if ($count == 2) {
				$catalog = $parts[0];
				$key = $parts[1];

			} else {
				$module = array_shift($parts);
				$catalog = array_shift($parts);
				$key = implode('.', $parts);
			}

			return array($module, $catalog, $key);
		});
	}

	/**
	 * Set the storage engine to use for catalog caching.
	 *
	 * @access public
	 * @param titon\libs\storage\Storage $storage
	 * @return titon\libs\translators\Translator
	 * @chainable
	 */
	public function setStorage(Storage $storage) {
		$this->_storage = $storage;
		$this->_storage->configure('storage', 'g11n');

		return $this;
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
		return MessageFormatter::formatMessage(Locale::DEFAULT_LOCALE, $this->getMessage($key), $params);
	}
	
}
