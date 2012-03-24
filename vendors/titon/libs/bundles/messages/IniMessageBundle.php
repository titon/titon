<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */
 
namespace titon\libs\bundles\messages;

use \titon\libs\bundles\messages\MessageBundleAbstract;

/**
 * Bundle used for loading INI files.
 *
 * @package	titon.libs.bundles.messages
 */
class IniMessageBundle extends MessageBundleAbstract {

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'module' => '',
		'bundle' => '',
		'ext' => 'ini'
	);

	/**
	 * Parse the file at the given path and return the result.
	 *
	 * @access public
	 * @param $path
	 * @return array
	 */
	public function parse($path) {
		return parse_ini_file($path, false, INI_SCANNER_NORMAL);
	}

}
