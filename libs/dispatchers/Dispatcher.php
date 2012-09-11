<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\dispatchers;

/**
 * Interface for the dispatchers library.
 *
 * @package	titon.libs.dispatchers
 */
interface Dispatcher {

	/**
	 * Dispatch the Controller action, render the view and notify events.
	 *
	 * @access public
	 * @return void
	 */
	public function dispatch();

	/**
	 * Run the dispatcher by processing the controller, handling exceptions and outputting the response.
	 *
	 * @access public
	 * @return void
	 */
	public function run();

}
