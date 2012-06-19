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
	 * Add a file reader to use.
	 *
	 * @access public
	 * @param titon\libs\readers\Reader $reader
	 * @return titon\libs\bundles\Bundle
	 */
	public function addReader(Reader $reader);

	/**
	 * Set the folder locations to use for cycling through.
	 *
	 * @access public
	 * @param array $locations
	 * @return titon\libs\bundles\Bundle
	 */
	public function setLocations(array $locations);

}
