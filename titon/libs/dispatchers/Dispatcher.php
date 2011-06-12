<?php
/**
 * Titon: The PHP 5.3 Micro Framework
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
	 * Primary method to run the dispatcher and process its logic.
	 *
	 * @access public
	 * @return void
	 */
	public function run();

}
