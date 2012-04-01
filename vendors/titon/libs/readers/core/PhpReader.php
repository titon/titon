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
 * A reader that loads its configuration from a PHP file.
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
	 * @return void
	 * @throws titon\libs\readers\ReaderException
	 */
	public function parseFile() {
		$data = include_once $this->getPath();
		
		if (is_array($data)) {
			$this->configure($data);
		} else {
			throw new ReaderException('Reader failed to import PHP configuration.');
		}
	}

}