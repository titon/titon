<?php
/**
 * The OptimizerCommand defines the callbacks which in turn trigger the primary Optimizer class.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\hooks\optimizer;

use \titon\modules\hooks\HookCommandAbstract;

/**
 * Optimizer Command Class
 *
 * @package     Titon
 * @subpackage  Titon.Modules.Hooks.Optimizer
 */
class OptimizerCommand extends HookCommandAbstract {

	/**
	 * Enable Gzip and GC based on parent configuration.
	 *
	 * @access public
	 * @return void
	 */
    public function preDispatch() {
		$this->Hook->enableGzipCompression();
		$this->Hook->enableGarbageCollection();
    }

	/**
	 * Disable the GC.
	 *
	 * @access public
	 * @return void
	 */
	public function postDispatch() {
		$this->Hook->disableGarbageCollection();
    }

}