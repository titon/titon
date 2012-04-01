<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */
 
namespace titon\libs\bundles\messages\gettext;

use \titon\libs\bundles\messages\MessageBundleAbstract;

/**
 * Bundle used for loading gettext PO files.
 *
 * @package	titon.libs.bundles.messages.gettext
 */
class PoMessageBundle extends MessageBundleAbstract {

	/**
	 * Bundle file extension.
	 */
	const EXT = 'po';

	/**
	 * Define the locations for the message resources.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->findBundle(array(
			APP_MODULES . '{module}/resources/messages/{bundle}/LC_MESSAGES/',
			APP_RESOURCES . 'messages/{bundle}/LC_MESSAGES/'
		));
	}

	/**
	 * Parse the file at the given path and return the result.
	 *
	 * @access public
	 * @param $path
	 * @return array
	 */
	public function parseFile($path) {
		return; // @todo
	}

}
