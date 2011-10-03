<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators\core;

use \titon\Titon;
use \titon\libs\translators\TranslatorAbstract;
use \titon\libs\translators\TranslatorException;

/**
 * Basic translator used for parsing simple PHP files into an array of translated messages.
 * 
 * @package	titon.libs.translators.core
 */
class DefaultTranslator extends TranslatorAbstract {

	/**
	 * Load a domain file within a specific module.
	 * 
	 * @access public
	 * @param string $module
	 * @param string $domain
	 * @return array
	 */
	public function loadFile($module, $domain) {
		return include $this->getFilePath($module, $domain, 'php');
	}

}