<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */
 
namespace titon\libs\bundles\messages\core;

use titon\libs\bundles\messages\MessageBundleAbstract;

/**
 * Bundle used for loading JSON files.
 *
 * @package	titon.libs.bundles.messages.core
 *
 * @link	http://php.net/json_decode
 */
class JsonMessageBundle extends MessageBundleAbstract {

	/**
	 * Bundle file extension.
	 */
	const EXT = 'json';

	/**
	 * Parse the file at the given path and return the result.
	 *
	 * @access public
	 * @param string $filename
	 * @return array
	 */
	public function parseFile($filename) {
		return json_decode(file_get_contents($this->getPath() . $filename), true);
	}

}