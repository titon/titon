<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use titon\Titon;
use titon\libs\dispatchers\Dispatcher;
use titon\libs\dispatchers\front\FrontDispatcher;
use titon\libs\dispatchers\front\FrontDevDispatcher;
use titon\utility\Inflector;
use \Exception;

/**
 * The dispatch handles the HTTP request and response cycle. Once it receives the HTTP request,
 * it determines the correct dispatcher library to use, parses the current route,
 * traverses the controller and module path, and then finally dispatches and outputs the HTTP response.
 *
 * You may use the setup() method to define the use of different dispatchers for specific controller and
 * module scopes. The setup() method should be used within the applications setup center.
 *
 * @package	titon.core
 */
class Dispatch {

	/**
	 * Mapped scopes to custom dispatchers.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_mapping = [];

	/**
	 * Initialize dispatch and detect if a custom dispatcher should be used within the current scope.
	 * If no scope is defined, the default front dispatcher will be instantiated.
	 *
	 * @access public
	 * @param boolean $return
	 * @return titon\libs\dispatchers\Dispatcher
	 */
	public function run($return = false) {
		$params = Titon::router()->current()->param();
		$dispatcher = null;

		if ($this->_mapping) {

			// Specific controller and module
			if (isset($this->_mapping[$params['module'] . '.' . $params['controller']])) {
				$dispatcher = $this->_mapping[$params['module'] . '.' . $params['controller']];

			// All controllers within a specific module
			} else if (isset($this->_mapping[$params['module'] . '.*'])) {
				$dispatcher = $this->_mapping[$params['module'] . '.*'];

			// Specific controller within any module
			} else if (isset($this->_mapping['*.' . $params['controller']])) {
				$dispatcher = $this->_mapping['*.' . $params['controller']];

			// Apply to all controllers and modules
			} else if (isset($this->_mapping['*.*'])) {
				$dispatcher = $this->_mapping['*.*'];
			}
		}

		if ($dispatcher instanceof Dispatcher) {
			$dispatcher->config->set($params);

		} else if (Titon::env()->isDevelopment()) {
			$dispatcher = new FrontDevDispatcher($params);

		} else {
			$dispatcher = new FrontDispatcher($params);
		}

		if ($return) {
			return $dispatcher;
		}

		$dispatcher->run();
		$dispatcher->output();

		return null;
	}

	/**
	 * Apply custom dispatchers to a specific module or controller scope.
	 *
	 * @access public
	 * @param titon\libs\dispatchers\Dispatcher $dispatcher
	 * @param array $scope
	 * @return titon\core\Dispatch
	 */
	public function setup(Dispatcher $dispatcher, array $scope = []) {
		$scope = $scope + [
			'module' => '*',
			'controller' => '*'
		];

		if ($scope['module'] !== '*') {
			$scope['module'] = Inflector::route($scope['module']);
		}

		if ($scope['controller'] !== '*') {
			$scope['controller'] = Inflector::route($scope['controller']);
		}

		$this->_mapping[$scope['module'] . '.' . $scope['controller']] = $dispatcher;

		return $this;
	}

}
