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
 * A file reader that parses INI files.
 *
 * @package	titon.libs.readers.core
 *
 * @link	http://php.net/parse_ini_file
 */
class IniReader extends ReaderAbstract {

	/**
	 * File type extension.
	 */
	const EXT = 'ini';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return array
	 */
	public function parse() {
		return parse_ini_file($this->_path, true, INI_SCANNER_NORMAL);
	}

}