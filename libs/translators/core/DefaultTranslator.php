<?php

namespace titon\libs\translators\core;

use \titon\Titon;
use \titon\libs\translators\TranslatorAbstract;
use \titon\libs\translators\TranslatorException;

class DefaultTranslator extends TranslatorAbstract {
	
	/**
	 * Load a locale domain file from a specific module and return an array of key/value messages.
	 * 
	 * @access public
	 * @param string $module
	 * @param string $domain
	 * @return array
	 */
	public function loadFile($module, $domain) {
		$locale = Titon::g11n()->current();
		$path = APP_MODULES . $module . DS . 'locale' . DS . $locale['locale'] . DS . $domain . '.php';
		
		if (!file_exists($path)) {
			throw new TranslatorException(sprintf('Translation file %s could not be found in the %s module for the %s locale.', $domain, $module, $locale['locale']));
		}
		
		$messages = include $path;

		return $messages;
	}
	
}