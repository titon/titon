<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use titon\Titon;
use titon\core\CoreException;
use titon\libs\bundles\locales\LocaleBundle;
use titon\libs\traits\Cacheable;
use titon\libs\translators\Translator;
use \Locale;

/**
 * @todo
 *
 * @link http://en.wikipedia.org/wiki/IETF_language_tag
 * @link http://en.wikipedia.org/wiki/ISO_639
 * @link http://en.wikipedia.org/wiki/ISO_3166-1
 * @link http://loc.gov/standards/iso639-2/php/code_list.php
 *
 * @package	titon.core
 */
class G11n {
	use Cacheable;

	/**
	 * Possible formats for locale keys.
	 *
	 *	FORMAT_1 - en-us (URL format)
	 *	FORMAT_2 - en-US
	 *	FORMAT_3 - en_US (Preferred)
	 *	FORMAT_4 - enUS
	 */
	const FORMAT_1 = 1;
	const FORMAT_2 = 2;
	const FORMAT_3 = 3;
	const FORMAT_4 = 3;

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
	protected $_locales = [];

	/**
	 * Translator used for string fetching and parsing.
	 *
	 * @access protected
	 * @var \titon\libs\translators\Translator
	 */
	protected $_translator;

	/**
	 * Convert a locale key to 3 possible formats.
	 *
	 * @access public
	 * @param string $key
	 * @param int $format
	 * @return string
	 */
	public function canonicalize($key, $format = self::FORMAT_1) {
		return $this->cache([__METHOD__, $key, $format], function() use ($key, $format) {
			$parts = explode('-', str_replace('_', '-', mb_strtolower($key)));
			$return = $parts[0];

			if (isset($parts[1])) {
				switch ($format) {
					case self::FORMAT_1:
						$return .= '-' . $parts[1];
					break;
					case self::FORMAT_2:
						$return .= '-' . mb_strtoupper($parts[1]);
					break;
					case self::FORMAT_3:
						$return .= '_' . mb_strtoupper($parts[1]);
					break;
					case self::FORMAT_4:
						$return .= mb_strtoupper($parts[1]);
					break;
				}
			}

			return $return;
		});
	}

	/**
	 * Get a list of locales and fallback locales in descending order starting from the current locale.
	 *
	 * @access public
	 * @return array
	 */
	public function cascade() {
		return $this->cache(__METHOD__, function() {
			$cycle = [];

			foreach ([$this->current(), $this->getFallback()] as $bundle) {
				while ($bundle instanceof LocaleBundle) {
					$cycle[] = $bundle->getLocale('id');

					$bundle = $bundle->getParent();
				}
			}

			return array_unique($cycle);
		});
	}

	/**
	 * Takes an array of key-values and returns a correctly ordered and delimited locale ID.
	 *
	 * @access public
	 * @param array $tags
	 * @return string
	 */
	public function compose(array $tags) {
		return Locale::composeLocale($tags);
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
	 * Parses a locale string and returns an array of key-value locale tags.
	 *
	 * @access public
	 * @param string $locale
	 * @return string
	 */
	public function decompose($locale) {
		return Locale::parseLocale($locale);
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

		ini_set('intl.default_locale', $this->_locales[$key]->getLocale('id'));

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
	 * @return void
	 * @throws \titon\core\CoreException
	 */
	public function initialize() {
		if (!$this->isEnabled()) {
			return;
		}

		$header = mb_strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);

		if (mb_strpos($header, ';') !== false) {
			$header = mb_strstr($header, ';', true);
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
		if ($current === null) {
			$current = $this->_fallback;
		}

		// Apply the locale
		$this->set($current);

		// Check for a translator
		if (!$this->_translator) {
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
		$locale = $this->current()->getLocale();

		return ($locale['key'] === $key || $locale['id'] === $key);
	}

	/**
	 * G11n will be enabled if more than 1 locale has been setup, excluding family chains.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isEnabled() {
		return $this->cache(__METHOD__, function() {
			$locales = $this->getLocales();

			if (!$locales) {
				return false;
			}

			$loaded = [];

			foreach ($locales as $bundle) {
				$locale = $bundle->getLocale();
				$loaded[] = $locale['id'];

				if (isset($locale['parent'])) {
					$loaded[] = $locale['parent'];
				}
			}

			return (count(array_unique($loaded)) > 1);
		});
	}

	/**
	 * Return an array of setup locale keys.
	 *
	 * @access public
	 * @return array
	 */
	public function listing() {
		return array_keys($this->_locales);
	}

	/**
	 * Set the locale using PHPs built in setlocale().
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
	public function set($key) {
		$key = $this->canonicalize($key);

		if (!isset($this->_locales[$key])) {
			throw new CoreException(sprintf('Locale %s does not exist.', $key));
		}

		$bundle = $this->_locales[$key];
		$bundles = [$bundle, $this->getFallback()];
		$options = [];

		foreach ($bundles as $tempBundle) {
			$locale = $tempBundle->getLocale();

			$options[] = $locale['id'] . '.UTF8';
			$options[] = $locale['id'] . '.UTF-8';
			$options[] = $locale['id'];

			if (!empty($locale['iso3'])) {
				foreach ((array) $locale['iso3'] as $iso3) {
					$options[] = $iso3 . '.UTF8';
					$options[] = $iso3 . '.UTF-8';
					$options[] = $iso3;
				}
			}

			if (!empty($locale['iso2'])) {
				$options[] = $locale['iso2'] . '.UTF8';
				$options[] = $locale['iso2'] . '.UTF-8';
				$options[] = $locale['iso2'];
			}
		}

		// Set environment
		$locale = $bundle->getLocale();

		putenv('LC_ALL=' . $locale['id']);
		setlocale(LC_ALL, $options);
		Locale::setDefault($locale['id']);

		$this->_current = $bundle;

		return $this;
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
	public function setup($key, array $config = []) {
		$urlKey = $this->canonicalize($key);

		if (isset($this->_locales[$urlKey])) {
			return $this;
		}

		// Load the bundle
		$bundle = new LocaleBundle(['bundle' => $this->canonicalize($key, self::FORMAT_3)], $config);

		// Cache the bundle
		$this->_locales[$urlKey] = Titon::registry()->set($bundle, 'g11n.bundle.locale.' . $bundle->getLocale('id'));

		// Set the parent as well
		$config = $bundle->getLocale();

		if (isset($config['parent']) && !isset($this->_locales[$config['parent']])) {
			$this->setup($config['parent']);
		}

		// Set fallback if none defined
		if (!$this->_fallback) {
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
	 * Return a translated string using the translator.
	 * If a storage engine is present, read and write from the cache.
	 *
	 * @access public
	 * @param string $key
	 * @param array $params
	 * @return string
	 */
	public function translate($key, array $params = []) {
		return $this->_translator->translate($key, $params);
	}

}

