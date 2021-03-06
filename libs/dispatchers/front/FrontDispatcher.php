<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\dispatchers\front;

use titon\Titon;
use titon\libs\dispatchers\DispatcherAbstract;
use titon\libs\exceptions\HttpException;
use \Exception;

/**
 * FrontDispatcher is used as the default dispatching mechanism, sometimes referred to as a Front Controller.
 * It implements the base DispatcherAbstract class to inherit the methods for locating Controllers, Views, etc.
 * Once located, it dispatches the current request, all the while executing events.
 *
 * @package	titon.libs.dispatchers.front
 */
class FrontDispatcher extends DispatcherAbstract {

	/**
	 * Dispatch the Controller action, render the view and notify events.
	 *
	 * @access public
	 * @return void
	 */
	public function dispatch() {
		$controller = $this->_controller;
		$event = $this->_event;

		$event->notify('dispatch.preDispatch', $this);

			$controller->preProcess();
			$event->notify('controller.preProcess', $controller);

				$controller->dispatchAction();

			$controller->postProcess();
			$event->notify('controller.postProcess', $controller);

			if ($controller->engine->config->render) {
				$engine = $controller->engine;

				$engine->preRender();
				$event->notify('view.preRender', $engine);

					$engine->run();

				$engine->postRender();
				$event->notify('view.postRender', $engine);
			}

		$event->notify('dispatch.postDispatch', $this);
	}

}