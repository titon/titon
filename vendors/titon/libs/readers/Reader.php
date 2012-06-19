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
	 * Check to see if the file exists.
	 *
	 * @access public
	 * @return boolean
	 */
	public function fileExists();

	/**
	 * Return the file extension for the reader.
	 *
	 * @access protected
	 * @return mixed
	 */
	public function getExtension();

	/**
	 * Return the filename.
	 *
	 * @access public
	 * @return string
	 */
	public function getFilename();

	/**
	 * Return the full file path including filename and extension.
	 *
	 * @access public
	 * @return string
	 */
	public function getFullPath();

	/**
	 * Return the file path.
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
	 * Read the file after checking for existence.
	 *
	 * @access public
	 * @return array
	 */
	public function readFile();

	/**
	 * Set the filename.
	 *
	 * @access public
	 * @param string $filename
	 * @return titon\libs\readers\Reader
	 */
	public function setFilename($filename);

	/**
	 * Set the file path.
	 *
	 * @access public
	 * @param string $path
	 * @return titon\libs\readers\Reader
	 */
	public function setPath($path);

}