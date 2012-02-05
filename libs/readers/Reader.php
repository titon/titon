<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\readers;

/**
 * Interface for the config readers library.
 *
 * @package	titon.libs.renders
 */
interface Reader {

	/**
	 * Return the file type extension for the reader.
	 *
	 * @access public
	 * @return string
	 */
	public function extension();

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 */
	public function read($path);

}