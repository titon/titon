<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators\gettext;

use titon\libs\bundles\messages\gettext\MoMessageBundle;
use titon\libs\translators\TranslatorAbstract;

/**
 * Translator used for parsing GNU gettext MO binary files into an array of translated messages.
 * 
 * @package	titon.libs.translators.gettext
 */
class MoTranslator extends TranslatorAbstract {

	/**
	 * Load the correct resource bundle for the associated file type.
	 *
	 * @access public
	 * @param string $module
	 * @param string $locale
	 * @return titon\libs\bundles\Bundle
	 */
	public function loadBundle($module, $locale) {
		return new MoMessageBundle(array(
			'module' => $module,
			'bundle' => $locale
		));
	}

}