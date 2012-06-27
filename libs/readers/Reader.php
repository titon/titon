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
 * Interface for the file readers library.
 *
 * @package	titon.libs.readers
 */
interface Reader {

	/**
	 * Return the file extension for the reader.
	 *
	 * @access protected
	 * @return mixed
	 */
	public function getExtension();

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return array
	 */
	public function parse();

	/**
	 * Read the file after checking for existence.
	 *
	 * @access public
	 * @param string $path
	 * @return array
	 */
	public function read($path = null);

}