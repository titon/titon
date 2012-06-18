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
 * @package	titon.libs.renders
 */
interface Reader {

	/**
	 * Check to see if the file exists.
	 *
	 * @access public
	 * @return boolean
	 */
	public function fileExists();

	/**
	 * Return the formatted filename.
	 *
	 * @access public
	 * @return string
	 */
	public function getFilename();

	public function getFullPath();

	/**
	 * Return the folder location.
	 *
	 * @access public
	 * @return string
	 */
	public function getPath();

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return array
	 */
	public function parseFile();

	/**
	 * Set the folder location of the file.
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 */
	public function setPath($path);

}