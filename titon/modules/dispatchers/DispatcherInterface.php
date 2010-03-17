<?php
/**
 * A required interface for all custom Dispatchers to implement.
 * Defines the method for running the dispatch.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\dispatchers;

/**
 * Dispatcher Interface
 *
 * @package		Titon
 * @subpackage	Titon.Modules
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
