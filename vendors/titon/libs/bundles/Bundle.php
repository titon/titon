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
	 * Returns all the data from the loaded resource files.
	 *
	 * @access public
	 * @return array
	 */
	public function data();

	/**
	 * List of all filenames within the resource bundle.
	 *
	 * @access public
	 * @return array
	 */
	public function files();

	/**
	 * List of locations to find the resource bundle in.
	 *
	 * @access public
	 * @return array
	 */
	public function locations();

	/**
	 * Returns the final resource bundle path.
	 *
	 * @access public
	 * @return string
	 */
	public function path();

	/**
	 * Load the file from the resource bundle if it exists and cache the data.
	 * If the file does not exist, return an empty array.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function load($key);

}
