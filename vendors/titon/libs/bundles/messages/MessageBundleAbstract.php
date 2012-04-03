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

/**
 * The MessageBundle manages the loading of message catalogs for localization.
 *
 * @package	titon.libs.bundles.messages
 * @abstract
 */
abstract class MessageBundleAbstract extends BundleAbstract {

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'module' => '',
		'bundle' => ''
	);

	/**
	 * Define the locations for the message resources.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->findBundle(array(
			APP_MODULES . '{module}/resources/messages/{bundle}/',
			APP_RESOURCES . 'messages/{bundle}/',
			TITON_RESOURCES . 'messages/{bundle}/'
		));
	}

}
