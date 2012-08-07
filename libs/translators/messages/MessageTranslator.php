<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators\messages;

use titon\libs\bundles\messages\MessageBundle;
use titon\libs\translators\TranslatorAbstract;
use titon\libs\translators\TranslatorException;

/**
 * Translator used for parsing resource files into an array of translated messages.
 *
 * @package	titon.libs.translators.messages
 */
class MessageTranslator extends TranslatorAbstract {

	/**
	 * Initialize the MessageBundle and inject the Reader dependency.
	 *
	 * @access public
	 * @param string $module
	 * @param string $locale
	 * @return titon\libs\bundles\Bundle
	 * @throws titon\libs\translators\TranslatorException
	 */
	public function loadBundle($module, $locale) {
		if (!$this->_reader) {
			throw new TranslatorException('No Reader has been loaded for message translating.');
		}

		$bundle = new MessageBundle([
			'module' => $module,
			'bundle' => $locale
		]);

		$bundle->addReader($this->_reader);

		return $bundle;
	}

}