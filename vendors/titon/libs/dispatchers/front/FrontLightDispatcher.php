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
	 * Dispatches the request internally with magic!
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		$controller = $this->controller;
		$controller->preProcess();

		try {
			$controller->dispatchAction();

		} catch (HttpException $e) {
			$controller->throwError($e->getCode(), array(
				'message' => $e->getMessage()
			));

		} catch (Exception $e) {
			debug($e->getCode());
		}

		$controller->postProcess();

		if ($controller->hasObject('engine') && $controller->engine->config('render')) {
			$engine = $controller->engine;
			$engine->preRender();
			$engine->run();
			$engine->postRender();
		}
	}
	
}