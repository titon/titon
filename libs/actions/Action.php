<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\actions;

use titon\libs\controllers\Controller;

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

	/**
	 * Store the parent Controller.
	 *
	 * @access public
	 * @param \titon\libs\controllers\Controller $controller
	 * @return \titon\libs\actions\Action
	 */
	public function setController(Controller $controller);

}