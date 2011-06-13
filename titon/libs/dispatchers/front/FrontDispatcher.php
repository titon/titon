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

		if ($controller->hasObject('engine') && $controller->engine->config('render')) {
			$engine = $controller->engine;
			
			$engine->preRender();
			$event->execute('preRender', $engine);

			$engine->run();

			$engine->postRender();
			$event->execute('postRender', $engine);
		}

		$event->execute('postDispatch');
	}

}