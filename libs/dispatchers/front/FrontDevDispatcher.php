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
use titon\log\Benchmark;
use titon\libs\dispatchers\DispatcherAbstract;
use titon\libs\exceptions\HttpException;
use \Exception;

/**
 * FrontDevDispatcher is used as the default dispatching mechanism, sometimes referred to as a Front Controller.
 * The class is a duplicate of the base DispatcherAbstract, but is used primarily during development.
 * The major difference is the logging of benchmarks.
 *
 * @package	titon.libs.dispatchers.front
 */
class FrontDevDispatcher extends DispatcherAbstract {

	/**
	 * Dispatch the Controller action, render the view and notify events.
	 *
	 * @access public
	 * @return void
	 */
	public function dispatch() {
		$controller = $this->_controller;
		$event = $this->_event;

		Benchmark::start('Dispatcher');
		$event->notify('dispatch.preDispatch', $this);

			Benchmark::start('Controller');
			$controller->preProcess();
			$event->notify('controller.preProcess', $controller);

				Benchmark::start('Action');
				$controller->dispatchAction();
				Benchmark::stop('Action');

			$controller->postProcess();
			$event->notify('controller.postProcess', $controller);
			Benchmark::stop('Controller');

			if ($controller->engine->config->render) {
				$engine = $controller->engine;

				Benchmark::start('Engine');
				$engine->preRender();
				$event->notify('view.preRender', $engine);

					Benchmark::start('View');
					$engine->run();
					Benchmark::stop('View');

				$engine->postRender();
				$event->notify('view.postRender', $engine);
				Benchmark::stop('Engine');
			}

		$event->notify('dispatch.postDispatch', $this);
		Benchmark::stop('Dispatcher');
	}

}