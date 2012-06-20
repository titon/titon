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
	 * Cached domain lookups.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_domains = array();

	/**
	 * Bind domain locations if they have not been setup.
	 *
	 * @access public
	 * @param string $module
	 * @param string $catalog
	 * @return void
	 */
	public function bindDomains($module, $catalog) {
		$domainKey = $module . '.' . $catalog;

		bind_textdomain_codeset($catalog, Titon::config()->encoding());

		if (isset($this->_domains[$domainKey])) {
			return;
		}

		if ($module) {
			bindtextdomain($catalog, APP_MODULES . $module . '/resources/messages');
		}

		bindtextdomain($catalog, APP_RESOURCES  . 'messages');

		$this->_domains[$domainKey] = true;
	}

	/**
	 * Get the message from the bound domain.
	 *
	 * @access public
	 * @param string $key
	 * @return string
	 * @throws titon\libs\translators\TranslatorException
	 */
	public function getMessage($key) {
		if (isset($this->_cache[$key])) {
			return $this->_cache[$key];
		}

		list($module, $catalog, $id) = $this->parseKey($key);

		$this->bindDomains($module, $catalog);

		$message = dgettext($catalog, $id);

		if ($message != $id) {
			$this->_cache[$key] = $message;

			return $message;
		}

		throw new TranslatorException(sprintf('Message key %s does not exist in %s.', $key, Locale::DEFAULT_LOCALE));
	}

}