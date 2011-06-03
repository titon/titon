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
 * A required interface for all custom Dispatchers to implement.
 *
 * @package	titon.source.library.dispatchers
 */
interface DispatcherInterface {

    /**
     * Primary method to run the dispatcher and process its logic.
     *
     * @access public
     * @return void
     */
    public function run();

}
