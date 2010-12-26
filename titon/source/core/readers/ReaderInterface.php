<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core\readers;

/**
 * Interface for all Config Readers.
 *
 * @package	titon.source.core.readers
 */
interface ReaderInterface {

	/**
	 * The reader must return the loaded config file as an array.
	 *
	 * @access public
	 * @return array
	 */
	public function toArray();

}