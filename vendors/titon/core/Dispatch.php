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
	protected $_mapping = array();

	/**
	 * Initialize dispatch and detect if a custom dispatcher should be used within the current scope.
	 * If no scope is defined, the default front dispatcher will be instantiated.
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		$params = Titon::router()->current()->getParams();
		$dispatcher = null;

		if (!empty($this->_mapping)) {

			// Specific controller and module
			if (isset($this->_mapping[$params['module'] . '.' . $params['controller']])) {
				$dispatcher = $this->_mapping[$params['module'] . '.' . $params['controller']];

			// All controllers within a specific container
			} else if (isset($this->_mapping[$params['module'] . '.*'])) {
				$dispatcher = $this->_mapping[$params['module'] . '.*'];

			// Specific controller within any container
			} else if (isset($this->_mapping['*.' . $params['controller']])) {
				$dispatcher = $this->_mapping['*.' . $params['controller']];

			// Apply to all controllers and containers
			} else if (isset($this->_mapping['*.*'])) {
				$dispatcher = $this->_mapping['*.*'];
			}
		}

		if ($dispatcher instanceof Dispatcher) {
			$dispatcher->config->set($params);

		} else if (Titon::environment()->isDevelopment()) {
			$dispatcher = new FrontDevDispatcher($params);

		} else {
			$dispatcher = new FrontDispatcher($params);
		}

		$dispatcher->run();
		$dispatcher->output();
	}

	/**
	 * Apply custom dispatchers to a specific module or controller scope.
	 *
	 * @access public
	 * @param titon\libs\dispatchers\Dispatcher $dispatcher
	 * @param array $scope
	 * @return void
	 */
	public function setup(Dispatcher $dispatcher, array $scope = array()) {
		$scope = $scope + array(
			'module' => '*',
			'controller' => '*'
		);

		if ($scope['module'] !== '*') {
			$scope['module'] = Inflector::slugify($scope['module']);
		}

		if ($scope['controller'] !== '*') {
			$scope['controller'] = Inflector::slugify($scope['controller']);
		}

		$this->_mapping[$scope['module'] . '.' . $scope['controller']] = $dispatcher;
	}

}
