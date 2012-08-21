<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators\messages;

use titon\Titon;
use titon\libs\translators\TranslatorAbstract;
use titon\libs\translators\TranslatorException;
use \Locale;

/**
 * Translator used for hooking into the GNU gettext library and fetching messages from locale domain files.
 *
 * @package	titon.libs.translators.messages
 */
class GettextTranslator extends TranslatorAbstract {

	/**
	 * Bind domain locations if they have not been setup.
	 *
	 * @access public
	 * @param string $module
	 * @param string $catalog
	 * @return boolean
	 */
	public function bindDomains($module, $catalog) {
		bind_textdomain_codeset($catalog, Titon::config()->encoding());

		return $this->cache([__METHOD__, $module, $catalog], function() use ($module, $catalog) {
			if ($module) {
				bindtextdomain($catalog, APP_MODULES . $module . '/resources/messages');
			}

			bindtextdomain($catalog, APP_RESOURCES  . 'messages');

			return true;
		});
	}

	/**
	 * Get the message from the bound domain.
	 *
	 * @access public
	 * @param string $key
	 * @return string
	 * @throws \titon\libs\translators\TranslatorException
	 */
	public function getMessage($key) {
		return $this->cache([__METHOD__, $key], function() use ($key) {
			list($module, $catalog, $id) = $this->parseKey($key);

			$this->bindDomains($module, $catalog);

			textdomain($catalog);

			$message = gettext($id);

			if ($message !== $id) {
				return $message;
			}

			throw new TranslatorException(sprintf('Message key %s does not exist in %s.', $key, Locale::DEFAULT_LOCALE));
		});
	}

}