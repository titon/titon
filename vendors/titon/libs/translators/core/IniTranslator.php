<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators\core;

use titon\libs\bundles\messages\core\IniMessageBundle;
use titon\libs\translators\TranslatorAbstract;

/**
 * Translator used for parsing INI files into an array of translated messages.
 * 
 * @package	titon.libs.translators.core
 */
class IniTranslator extends TranslatorAbstract {

	/**
	 * Load the correct resource bundle for the associated file type.
	 *
	 * @access public
	 * @param string $module
	 * @param string $locale
	 * @return titon\libs\bundles\Bundle
	 */
	public function loadBundle($module, $locale) {
		return new IniMessageBundle(array(
			'module' => $module,
			'bundle' => $locale
		));
	}

}