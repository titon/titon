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
use titon\libs\readers\ReaderException;
use titon\utility\Set;

/**
 * A file reader that parses INI files.
 *
 * @package	titon.libs.readers.core
 * @uses	titon\libs\readers\ReaderException
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
	 * @throws titon\libs\readers\ReaderException
	 */
	public function parseFile() {
		$data = parse_ini_file($this->getPath(), true, INI_SCANNER_NORMAL);

		if (is_array($data)) {
			return Set::expand($data);
		}

		throw new ReaderException('File reader failed to parse INI file.');
	}

}