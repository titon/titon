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
		debug('Front development dispatcher');
		
		Benchmark::start('Dispatcher');
		Titon::event()->execute('preDispatch');

		// Controller
		Benchmark::start('Controller');
		$this->controller->preProcess();
		Titon::event()->execute('preProcess', $this->controller);

			// Action
			Benchmark::start('Action');
			$this->controller->dispatch();
			Benchmark::stop('Action');

		$this->controller->postProcess();
		Titon::event()->execute('postProcess', $this->controller);
		Benchmark::stop('Controller');

		// View
		Benchmark::start('View');

		if ($this->view->config('render')) {
			$this->view->preRender();
			Titon::event()->execute('preRender', $this->view);

			$this->view->run();

			$this->view->postRender();
			Titon::event()->execute('postRender', $this->view);
		}

		Benchmark::stop('View');

		Titon::event()->execute('postDispatch');
		Benchmark::stop('Dispatcher');
	}

}