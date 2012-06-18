<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\dispatchers;

use titon\Titon;
use titon\base\Base;
use titon\libs\dispatchers\Dispatcher;
use titon\libs\dispatchers\DispatcherException;
use titon\libs\traits\Decorator;

/**
 * The Dispatcher acts as the base for all child dispatchers. The Dispatcher should not be confused with Dispatch.
 * Dispatch determines the current request and then calls the Dispatcher to output the current request.
 * The Dispatcher has many default methods for locating and validating objects within the MVC paradigm.
 *
 * @package	titon.libs.dispatchers
 * @uses	titon\Titon
 * @uses	titon\libs\dispatchers\DispatcherException
 * @abstract
 */
abstract class DispatcherAbstract extends Base implements Dispatcher {
	//use Decorator;

	/**
	 * Lazy load the controller object. Do not allow overrides.
	 *
	 * @access public
	 * @return void
	 * @final
	 */
	final public function initialize() {
		$this->attachObject(array(
			'alias' => 'controller',
			'interface' => '\titon\libs\controllers\Controller'
		), function($self) {
			return $self->loadController();
		});
	}

	/**
	 * Load the controller based on the routing params. If the controller does not exist, throw exceptions.
	 *
	 * @access public
	 * @return titon\libs\controllers\Controller
	 * @throws titon\libs\dispatchers\DispatcherException
	 * @final
	 */
	final public function loadController() {
		$config = $this->config();
		$module = Titon::app()->module($config['module']);
		$controller = $module['controllers'][$config['controller']];
		$path = $module['path'] . 'controllers/' . $controller . '.php';

		if (file_exists($path)) {
			return Titon::registry()->factory($path, $config);
		}

		throw new DispatcherException(sprintf('%s could not be located in the file system.', $controller));
	}

	/**
	 * The final result from the action and the rending engine.
	 *
	 * @access public
	 * @return void
	 */
	public function output() {
		$controller = $this->controller;

		if ($controller->hasObject('response')) {
			if ($type = $controller->config->ext) {
				$controller->response->type($type);
			}

			if ($controller->hasObject('engine')) {
				$controller->response->body($controller->engine->content());
			}

			$controller->response->respond();
		}
	}

	/**
	 * Primary method to run the dispatcher and its process its logic.
	 *
	 * @access public
	 * @return void
	 * @throws titon\libs\dispatchers\DispatcherException
	 */
	public function run() {
		throw new DispatcherException('You must define your own run() method to dispatch the current request.');
	}

}
