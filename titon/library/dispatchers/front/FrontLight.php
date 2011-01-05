<?php
/**
 * FrontDev is used as the default dispatching mechanism, sometimes referred to as a Front Controller.
 * The class is a duplicate of the base Front dispatcher, but is a lightweight version by stripping
 * out all benchmarking and events.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\dispatchers\front;

use \titon\modules\dispatchers\DispatcherAbstract;

/**
 * Front Dispatcher Class
 *
 * @package     Titon
 * @subpackage	Titon.Modules.Dispatchers.Front
 */
class FrontDev extends DispatcherAbstract {

    /**
	 * Dispatches the request internally with magic!
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		$this->Controller->initialize();
		$this->Controller->preProcess();
		$this->Controller->dispatch();
		$this->Controller->postProcess();

		if ($this->View->getConfig('render') === true) {
			$this->View->initialize();
			$this->View->preRender();
			$this->View->run();
			$this->View->postRender();
		}
	}

}