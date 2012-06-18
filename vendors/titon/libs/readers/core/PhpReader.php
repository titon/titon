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

/**
 * A file reader that parses PHP files.
 * The PHP file must contain a return statement that returns an array.
 *
 * @package	titon.libs.readers.core
 * @uses	titon\libs\readers\ReaderException
 * 
 * @link	http://php.net/manual/function.include.php
 */
class PhpReader extends ReaderAbstract {

	/**
	 * File type extension.
	 */
	const EXT = 'php';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return array
	 * @throws titon\libs\readers\ReaderException
	 */
	public function parseFile() {
		if ($this->fileExists()) {
			$data = include_once $this->getPath();

			if (is_array($data)) {
				return $data;
			}
		}

		throw new ReaderException(sprintf('File reader failed to include PHP file: %s', $this->getFilename()));
	}

}