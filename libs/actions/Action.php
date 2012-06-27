<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\actions;

/**
 * Interface for the actions library.
 *
 * @package	titon.libs.actions
 */
interface Action {

	/**
	 * Method that is executed to trigger the actions logic.
	 *
	 * @access public
	 * @return void
	 */
	public function run();

}