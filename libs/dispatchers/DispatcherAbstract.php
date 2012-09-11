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
use titon\libs\controllers\core\ErrorController;
use titon\libs\dispatchers\Dispatcher;
use titon\libs\dispatchers\DispatcherException;
use titon\libs\exceptions\HttpException;
use titon\libs\traits\Attachable;
use \Exception;

/**
 * The Dispatcher acts as the base for all child dispatchers. The Dispatcher should not be confused with Dispatch.
 * Dispatch determines the current request and then calls the Dispatcher to output the current request.
 * The Dispatcher has many default methods for locating and validating objects within the MVC paradigm.
 *
 * @package	titon.libs.dispatchers
 * @abstract
 */
abstract class DispatcherAbstract extends Base implements Dispatcher {
	use Attachable;

	/**
	 * Controller instance.
	 *
	 * @access protected
	 * @var \titon\libs\controllers\Controller
	 */
	protected $_controller;

	/**
	 * Event instance.
	 *
	 * @access protected
	 * @var \titon\core\Event
	 */
	protected $_event;

	/**
	 * Lazy load the controller object. Do not allow overrides.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$self = $this;

		Titon::event()->addCallback(function() use ($self) {
			$self->notifyObjects('preDispatch');
		}, 'dispatch.preDispatch');

		Titon::event()->addCallback(function() use ($self) {
			$self->notifyObjects('postDispatch');
		}, 'dispatch.postDispatch');

		$this->_event = Titon::event();
	}

	/**
	 * Load the controller based on the routing params. If the controller does not exist, throw exceptions.
	 *
	 * @access public
	 * @return \titon\libs\controllers\Controller
	 * @throws \titon\libs\dispatchers\DispatcherException
	 * @final
	 */
	final public function loadController() {
		$config = $this->config->get();
		$module = Titon::app()->getModule($config['module']);

		if (!isset($module['controllers'][$config['controller']])) {
			throw new DispatcherException(sprintf('%s has not been loaded for the %s module.', $config['controller'], $config['module']));
		}

		$controller = $module['controllers'][$config['controller']];
		$path = $module['path'] . 'controllers/' . $controller . '.php';

		if (file_exists($path)) {
			return Titon::registry()->factory($path, $config);
		}

		throw new DispatcherException(sprintf('%s could not be located in the file system.', $controller));
	}

	/**
	 * Run the dispatcher by processing the controller, handling exceptions and outputting the response.
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		$error = null;
		$template = null;

		// Load the controller
		try {
			$controller = $this->loadController();

		} catch (Exception $e) {
			$controller = new ErrorController($this->config->get());
			$controller->forwardAction('index');

			$error = $e->getMessage();
		}

		$this->_controller = $controller;

		// Dispatch the action and catch exceptions
		if (!$error) {
			try {
				$this->dispatch();

			} catch (HttpException $e) {
				$error = $e->getMessage();
				$template = $e->getCode();

			} catch (Exception $e) {
				$error = $e->getMessage();
			}
		}

		// Render the exception
		if ($error) {
			$this->_controller->throwError($template, [
				'message' => $error
			]);
		}

		// Output the response
		$controller = $this->_controller;

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

}
