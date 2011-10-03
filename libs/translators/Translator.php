<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators;

/**
 * Interface for G11N string translators.
 * 
 * @package	titon.libs.translators
 */
interface Translator {
	
	/**
	 * Get a list of locales and fallback locales in descending order starting from the current locale. 
	 * This will be used to cycle through the respective domain files to find a match.
	 * 
	 * @access public
	 * @return array
	 */
	public function getFileCycle();
	
	/**
	 * Determine the file path by looping through all the locale options.
	 * 
	 * @access public
	 * @param string $module
	 * @param string $domain
	 * @param string $ext
	 * @return string 
	 */
	public function getFilePath($module, $domain, $ext);

	/**
	 * Locate the key within the domain file. If the domain file has not been loaded, 
	 * load it and cache the collection of strings.
	 * 
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function getMessage($key);

	/**
	 * Load a domain file within a specific module.
	 * 
	 * @access public
	 * @param string $module
	 * @param string $domain
	 * @return array
	 */
	public function loadFile($module, $domain);
	
	/**
	 * Parse out the module, domain and key for string lookup.
	 * 
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function parseKey($key);
	
	/**
	 * Process the located string with dynamic parameters if necessary.
	 * 
	 * @access public
	 * @param string $key
	 * @param array $params
	 * @return string
	 */
	public function translate($key, array $params);
	
}