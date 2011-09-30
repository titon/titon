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
		$locales = $this->getLocalesCycle();
		$finalPath = null;
		
		foreach ($locales as $locale) {
			$path = APP_MODULES . $module . DS . 'locale' . DS . $locale . DS . $domain . '.php';

			if (file_exists($path)) {
				$finalPath = $path;
				break;
			}
		}

		if ($finalPath === null) {
			throw new TranslatorException(sprintf('Translation file %s.php could not be found in the %s module for the following locales: %s.', $domain, $module, implode(', ', $locales)));
		}

		$messages = include $finalPath;

		return $messages;
	}

}