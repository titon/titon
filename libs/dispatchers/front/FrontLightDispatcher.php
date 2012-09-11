<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\dispatchers\front;

use titon\libs\dispatchers\DispatcherAbstract;
use titon\libs\exceptions\HttpException;
use \Exception;

/**
 * FrontLightDispatcher is a very lightweight replacement for the FrontDispatcher.
 * Provides the full dispatching functionality, without the benchmarking and event listening.
 *
 * @package	titon.libs.dispatchers.front
 */
class FrontLightDispatcher extends DispatcherAbstract {

	/**
	 * Dispatch the Controller action, render the view and notify events.
	 *
	 * @access public
	 * @return void
	 */
	public function dispatch() {
		$controller = $this->_controller;
		$controller->preProcess();
		$controller->dispatchAction();
		$controller->postProcess();

		if ($controller->engine->config->render) {
			$engine = $controller->engine;
			$engine->preRender();
			$engine->run();
			$engine->postRender();
		}
	}

}