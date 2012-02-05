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
 * Interface for the config readers library.
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
	 * Returns the final path.
	 *
	 * @access public
	 * @return string
	 */
	public function getPath();

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return void
	 */
	public function parseFile();

}