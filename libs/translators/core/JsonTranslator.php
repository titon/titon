<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators\core;

use \titon\libs\translators\TranslatorAbstract;

/**
 * Translator used for parsing JSON files into an array of translated messages.
 * 
 * @package	titon.libs.translators.core
 * 
 * @link	http://php.net/json_decode
 */
class JsonTranslator extends TranslatorAbstract {

	/**
	 * Load a catalog from a specific module.
	 * 
	 * @access public
	 * @param string $module
	 * @param string $catalog
	 * @return array
	 */
	public function parseFile($module, $catalog) {
		return json_decode(file_get_contents($this->getFilePath($module, $catalog, 'json')), true);
	}

}