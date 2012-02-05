<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\dispatchers\front;

use \titon\Titon;
use \titon\libs\dispatchers\DispatcherAbstract;

/**
 * FrontDispatcher is used as the default dispatching mechanism, sometimes referred to as a Front Controller.
 * It implements the base DispatcherAbstract class to inherit the methods for locating Controllers, Views, etc.
 * Once located, it dispatches the current request, all the while executing events.
 *
 * @package	titon.libs.dispatchers.front
 */
class FrontDispatcher extends DispatcherAbstract {

	/**
	 * Dispatches the request internally with magic!
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		$controller = $this->controller;
		$event = Titon::event();

		$event->execute('preDispatch');

		$controller->preProcess();
		$event->execute('preProcess', $controller);
		
		$controller->dispatch();
		
		$controller->postProcess();
		$event->execute('postProcess', $controller);

		if ($controller->hasObject('view') && $controller->view->config('render')) {
			$view = $controller->view;
			
			$view->preRender();
			$event->execute('preRender', $view);

			$view->run();

			$view->postRender();
			$event->execute('postRender', $view);
		}

		$event->execute('postDispatch');
		$controller->output();
	}

}