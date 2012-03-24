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
	 * List of all filenames within the resource bundle.
	 *
	 * @access public
	 * @return array
	 */
	public function files();

	/**
	 * Load the file from the resource bundle if it exists and cache the data.
	 * If the file does not exist, return an empty array.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function load($key);

	/**
	 * List of locations to find the resource bundle in.
	 *
	 * @access public
	 * @return array
	 */
	public function locations();

	/**
	 * Parse the file at the given path and return the result.
	 *
	 * @access public
	 * @param $path
	 * @return array
	 */
	public function parse($path);

	/**
	 * Returns the final resource bundle path.
	 *
	 * @access public
	 * @return string
	 */
	public function path();

}
