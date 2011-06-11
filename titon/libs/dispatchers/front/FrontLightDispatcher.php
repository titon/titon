<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\dispatchers\front;

use \titon\libs\dispatchers\DispatcherAbstract;

/**
 * FrontLightDispatcher is a very lightweight replacement for the FrontDispatcher. 
 * Provides the full dispatching functionality, without the benchmarking and event listening.
 *
 * @package	titon.libs.dispatchers.front
 */
class FrontLightDispatcher extends DispatcherAbstract {

	/**
	 * Dispatches the request internally with magic!
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		$controller = $this->controller;
		$view = $this->view;

		// Controller and action
		$controller->preProcess();
		$controller->dispatch();
		$controller->postProcess();

		// View
		if ($view->config('render')) {
			$view->preRender();
			$view->run();
			$view->postRender();
		}
	}
	
}