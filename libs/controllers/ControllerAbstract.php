<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\controllers;

use titon\Titon;
use titon\base\Base;
use titon\constant\Http;
use titon\libs\actions\Action;
use titon\libs\controllers\Controller;
use titon\libs\controllers\ControllerException;
use titon\libs\engines\Engine;
use titon\libs\engines\core\ViewEngine;
use titon\libs\traits\Attachable;
use titon\utility\Inflector;
use \Closure;

/**
 * The Controller (MVC) acts as the median between the request and response within the dispatch cycle.
 * It splits up its responsibility into multiple Actions, where each Action deals with specific business logic.
 * The logical data is retrieved from a Model (database or logic entity) or a PHP super global (POST, GET).
 *
 * The Controller receives an instance of the View object allowing the Controller to set data to the view,
 * overwrite the View and Engine configuration, attach helpers, etc.
 *
 * Furthermore, the Controller inherits all functionality from the Attachable class, allowing you to attach
 * external classes to use their functionality and trigger specific callbacks.
 *
 * @package	titon.libs.controllers
 * @abstract
 */
abstract class ControllerAbstract extends Base implements Controller {
	use Attachable;

	/**
	 * Configuration.
	 *
	 *	module 			- Current application module
	 *	controller 		- Current controller within the module
	 *	action 			- Current action within the controller
	 *	ext 			- The extension within the address bar, and what content-type to render the page as
	 *	args 			- Action arguments
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'module' => '',
		'controller' => '',
		'action' => '',
		'ext' => '',
		'args' => []
	];

	/**
	 * Dispatch the request to the correct controller action. Checks to see if the action exists and is not protected.
	 *
	 * @access public
	 * @param string $action
	 * @param array $args
	 * @return mixed
	 * @throws titon\libs\controllers\ControllerException
	 */
	public function dispatchAction($action = null, array $args = []) {
		if (empty($action)) {
			$action = $this->config->action;
		}

		if (empty($args)) {
			$args = $this->config->args;
		}

		// Do not include the base controller methods
		$methods = array_diff(get_class_methods($this), get_class_methods(__CLASS__));

		if (!in_array($action, $methods) || mb_substr($action, 0, 1) === '_') {
			throw new ControllerException('Your action does not exist, or is not public, or is found within the parent Controller.');
		}

		return call_user_func_array([$this, $action], $args);
	}

	/**
	 * Forward the current request to a new action, instead of doing an additional HTTP request.
	 *
	 * @access public
	 * @param string $action
	 * @param array $args
	 * @return mixed
	 */
	public function forwardAction($action, array $args = []) {
		$this->config->action = $action;
		$this->engine->setup($action);
		$this->dispatchAction($action, $args);
	}

	/**
	 * Attach the request and response objects. Can overwrite or remove for high customization.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('request', function() {
			return Titon::registry()->factory('titon\net\Request');
		});

		$this->attachObject('response', function() {
			return Titon::registry()->factory('titon\net\Response');
		});

		$this->setEngine(function() {
			$config = $this->config->get();
			unset($config['args']);

			$engine = new ViewEngine();
			$engine->setup(['template' => $config]);

			return $engine;
		});
	}

	/**
	 * Trigger a custom Action class.
	 *
	 * @access public
	 * @param titon\libs\actions\Action $action
	 * @return titon\libs\actions\Action
	 */
	public function runAction(Action $action) {
		$action->setController($this)->run();

		return $action;
	}

	/**
	 * Functionality to throw up an error page (like a 404). The error template is derived from the $action passed.
	 *
	 * @access public
	 * @param string $action
	 * @param array $args
	 * @return void
	 */
	public function throwError($action, array $args = []) {
		if (empty($action)) {
			$action = 'error';
		}

		if (empty($args['pageTitle'])) {
			if (is_numeric($action)) {
				$args['pageTitle'] = $action . ' - ' . Http::getStatusCode($action);
			} else {
				$args['pageTitle'] = Inflector::titleCase($action);
			}
		}

		// Must be an HTTP code, set the status
		if (is_numeric($action)) {
			$this->response->status($action);
		}

		$args['referrer'] = $this->request->referrer();
		$args['url'] = Titon::router()->current()->url();

		$this->engine->set($args);
		$this->engine->setup([
			'error' => $action,
			'layout' => 'error'
		]);
	}

	/**
	 * Triggered before the Controller processes the requested Action.
	 *
	 * @access public
	 * @return void
	 */
	public function preProcess() {
		$this->notifyObjects('preProcess');
	}

	/**
	 * Triggered after the Action processes, but before the View renders.
	 *
	 * @access public
	 * @return void
	 */
	public function postProcess() {
		$this->notifyObjects('postProcess');
	}

	/**
	 * Setup the rendering engine to use.
	 *
	 * @access public
	 * @param Closure $engine
	 * @return titon\libs\controllers\Controller
	 * @final
	 */
	final public function setEngine(Closure $engine) {
		$this->attachObject([
			'alias' => 'engine',
			'interface' => 'titon\libs\engines\Engine'
		], $engine);

		return $this;
	}

}
