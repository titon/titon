<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\readers\core;

use titon\libs\readers\ReaderAbstract;

/**
 * A file reader that parses JSON files; must have the JSON module installed.
 *
 * @package	titon.libs.readers.core
 *
 * @link	http://php.net/json_decode
 */
class JsonReader extends ReaderAbstract {

	/**
	 * File type extension.
	 */
	const EXT = 'json';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return array
	 */
	public function parse() {
		return @json_decode(file_get_contents($this->path()), true);
	}

}