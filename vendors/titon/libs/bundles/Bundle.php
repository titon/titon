<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\bundles;

use titon\libs\readers\Reader;

/**
 * Interface for the bundles library.
 *
 * @package	titon.libs.bundles
 */
interface Bundle {

	/**
	 * Add a folder location to use during the lookup cycle.
	 *
	 * @access public
	 * @param array|string $locations
	 * @return titon\libs\bundles\Bundle
	 */
	public function addLocation($locations);

	/**
	 * Add a file reader to use for resource parsing.
	 *
	 * @access public
	 * @param titon\libs\readers\Reader $reader
	 * @return titon\libs\bundles\Bundle
	 */
	public function addReader(Reader $reader);

	/**
	 * Return all defined locations.
	 *
	 * @access public
	 * @return array
	 */
	public function getLocations();

	/**
	 * Return all loaded Readers.
	 *
	 * @access public
	 * @return array
	 */
	public function getReaders();

	/**
	 * Parse the contents of every file that matches the resource name.
	 * Begin by looping through all resource locations and all Readers.
	 * If a resource is found that matches the name and a loaded Reader extension,
	 * parse the file and merge its contents with the previous resource of the same name.
	 *
	 * @access public
	 * @param string $resource
	 * @return array
	 * @throws titon\libs\bundles\BundleException
	 */
	public function loadResource($resource);

}
