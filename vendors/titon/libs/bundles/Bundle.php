<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */
 
namespace titon\libs\bundles;

/**
 * Interface for the bundles library.
 *
 * @package	titon.libs.bundles
 */
interface Bundle {

	/**
	 * Attempt to find the resource bundle within the resource locations.
	 *
	 * @access public
	 * @param array $locations
	 * @return void
	 */
	public function findBundle(array $locations);

	/**
	 * Return data based on a key. If data does not exist, load it from the file that matches the key.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function get($key);

	/**
	 * List of all filenames within the resource bundle.
	 *
	 * @access public
	 * @return array
	 */
	public function getFiles();

	/**
	 * List of locations to find the resource bundle in.
	 *
	 * @access public
	 * @return array
	 */
	public function getLocations();

	/**
	 * Returns the final resource bundle path.
	 *
	 * @access public
	 * @return string
	 */
	public function getPath();

	/**
	 * Check if the file exists within the bundle.
	 *
	 * @access public
	 * @param string $filename
	 * @return boolean
	 */
	public function hasFile($filename);

	/**
	 * Load the file from the resource bundle and parse its contents.
	 * If file does not exist, return an empty array.
	 *
	 * @access public
	 * @param string $filename
	 * @return array
	 */
	public function loadFile($filename);

	/**
	 * Parse the file at the given path and return the result.
	 *
	 * @access public
	 * @param string $filename
	 * @return array
	 */
	public function parseFile($filename);

}
