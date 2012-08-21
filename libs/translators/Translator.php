<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators;

use titon\libs\readers\Reader;
use titon\libs\storage\Storage;

/**
 * Interface for G11N string translators.
 *
 * @package	titon.libs.translators
 */
interface Translator {

	/**
	 * Locate the key within the catalog. If the catalog has not been loaded,
	 * load it and cache the collection of strings.
	 *
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function getMessage($key);

	/**
	 * Load the correct resource bundle for the associated file type.
	 *
	 * @access public
	 * @param string $module
	 * @param string $locale
	 * @return \titon\libs\bundles\Bundle
	 */
	public function loadBundle($module, $locale);

	/**
	 * Parse out the module, catalog and key for string lookup.
	 *
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function parseKey($key);

	/**
	 * Set the file reader to use for resource parsing.
	 *
	 * @access public
	 * @param \titon\libs\readers\Reader $reader
	 * @return \titon\libs\translators\Translator
	 * @chainable
	 */
	public function setReader(Reader $reader);

	/**
	 * Set the storage engine to use for catalog caching.
	 *
	 * @access public
	 * @param \titon\libs\storage\Storage $storage
	 * @return \titon\libs\translators\Translator
	 * @chainable
	 */
	public function setStorage(Storage $storage);

	/**
	 * Process the located string with dynamic parameters if necessary.
	 *
	 * @access public
	 * @param string $key
	 * @param array $params
	 * @return string
	 */
	public function translate($key, array $params = []);

}