<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\library\readers;

/**
 * Interface for all Config Readers.
 *
 * @package	titon.source.core.readers
 */
interface ReaderInterface {

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
	 * @return void
	 */
	public function read();

	/**
	 * Set the path to the file.
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 */
	public function setPath($path);

	/**
	 * The reader must return the loaded config file as an array.
	 *
	 * @access public
	 * @return array
	 */
	public function toArray();

}