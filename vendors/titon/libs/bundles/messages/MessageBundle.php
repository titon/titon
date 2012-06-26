<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\bundles\messages;

use titon\libs\bundles\BundleAbstract;
use titon\libs\readers\Reader;

/**
 * The MessageBundle manages the loading of message catalogs for localization.
 *
 * @package	titon.libs.bundles.messages
 */
class MessageBundle extends BundleAbstract {

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'module' => '',
		'bundle' => ''
	];

	/**
	 * Define the locations for the message resources.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->addLocation([
			TITON_RESOURCES . 'messages/{bundle}/',
			APP_RESOURCES . 'messages/{bundle}/',
			APP_MODULES . '{module}/resources/messages/{bundle}/',
		]);
	}

	/**
	 * Add a file reader to use for resource parsing.
	 * If PoReader or MoReader is passed, use alternate lookup locations.
	 *
	 * @access public
	 * @param titon\libs\readers\Reader $reader
	 * @return titon\libs\bundles\Bundle
	 */
	public function addReader(Reader $reader) {
		$ext = $reader->getExtension();

		if (in_array($ext, ['po', 'mo']) && empty($this->_readers[$ext])) {
			$this->_locations = [];

			$this->addLocation([
				APP_RESOURCES . 'messages/{bundle}/LC_MESSAGES/',
				APP_MODULES . '{module}/resources/messages/{bundle}/LC_MESSAGES/'
			]);
		}

		return parent::addReader($reader);
	}

}
