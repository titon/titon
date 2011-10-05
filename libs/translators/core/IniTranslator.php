<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators\core;

use \titon\libs\translators\TranslatorAbstract;

/**
 * Translator used for parsing INI files into an array of translated messages.
 * 
 * @package	titon.libs.translators.core
 * 
 * @link	http://php.net/parse_ini_file
 */
class IniTranslator extends TranslatorAbstract {

	/**
	 * Load a domain file within a specific module.
	 * 
	 * @access public
	 * @param string $module
	 * @param string $domain
	 * @return array
	 */
	public function loadFile($module, $domain) {
		return parse_ini_file($this->getFilePath($module, $domain, 'ini'), false, INI_SCANNER_NORMAL);
	}

}