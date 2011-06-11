<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\dispatchers\front;

use \titon\Titon;
use \titon\log\Benchmark;
use \titon\libs\dispatchers\DispatcherAbstract;

/**
 * FrontDevDispatcher is used as the default dispatching mechanism, sometimes referred to as a Front Controller.
 * The class is a duplicate of the base DispatcherAbstract, but is used primarily during development.
 * The major difference is the logging of benchmarks.
 *
 * @package	titon.libs.dispatchers.front
 */
class FrontDevDispatcher extends DispatcherAbstract {

	/**
	 * Dispatches the request internally with magic!
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		$controller = $this->controller;
		$event = Titon::event();
		$view = $this->view;
		
		Benchmark::start('Dispatcher');
		$event->execute('preDispatch');

		// Controller
		Benchmark::start('Controller');
		$controller->preProcess();
		$event->execute('preProcess', $controller);

			// Action
			Benchmark::start('Action');
			$controller->dispatch();
			Benchmark::stop('Action');

		$controller->postProcess();
		$event->execute('postProcess', $controller);
		Benchmark::stop('Controller');

		// View
		Benchmark::start('View');

		if ($view->config('render')) {
			$view->preRender();
			$event->execute('preRender', $view);

			$view->run();

			$view->postRender();
			$event->execute('postRender', $view);
		}

		Benchmark::stop('View');

		$event->execute('postDispatch');
		Benchmark::stop('Dispatcher');
	}

}