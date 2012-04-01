<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators\core;

use titon\libs\translators\TranslatorAbstract;

/**
 * Translator used for parsing INI files into an array of translated messages.
 * 
 * @package	titon.libs.translators.core
 * 
 * @link	http://php.net/parse_ini_file
 */
class IniTranslator extends TranslatorAbstract {

	/**
	 * Load a catalog from a specific module.
	 * 
	 * @access public
	 * @param string $module
	 * @param string $catalog
	 * @return array
	 */
	public function parseFile($module, $catalog) {
		return parse_ini_file($this->getFilePath($module, $catalog, 'ini'), false, INI_SCANNER_NORMAL);
	}

}