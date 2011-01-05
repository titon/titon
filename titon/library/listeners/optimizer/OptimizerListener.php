<?php
/**
 * The Optimizer Listener defines the callbacks which in turn trigger the primary Optimizer class.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\events\optimizer;

use \titon\modules\events\EventListenerAbstract;

/**
 * Optimizer Listener Class
 *
 * @package     Titon
 * @subpackage  Titon.Modules.Events.Optimizer
 */
class OptimizerListener extends EventListenerAbstract {

	/**
	 * Enable Gzip and GC based on parent configuration.
	 *
	 * @access public
	 * @return void
	 */
    public function preDispatch() {
		$this->Event->enableGzipCompression();
		$this->Event->enableGarbageCollection();
    }

	/**
	 * Disable the GC.
	 *
	 * @access public
	 * @return void
	 */
	public function postDispatch() {
		$this->Event->disableGarbageCollection();
    }

}