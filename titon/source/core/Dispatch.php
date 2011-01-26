<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

use \titon\source\core\Core;
use \titon\library\dispatchers\front\Front;
use \titon\library\dispatchers\front\FrontDev;
use \titon\source\library\dispatchers\DispatcherInterface;
use \titon\source\utility\Inflector;

/**
 * The dispatch handles the HTTP request and response cycle. Once it receives the HTTP request,
 * it determines the correct dispatcher library to use, parses the current route,
 * traverses the controller and module path, and then finally dispatches and outputs the HTTP response.
 *
 * You may use the setup() method to define the use of different dispatchers for specific controller and
 * module scopes. The setup() method should be used within the applications setup center.
 *
 * @package	titon.source.core
 */
class Dispatch extends Core {

    /**
     * Mapped scopes to custom dispatchers.
     *
     * @access private
     * @var array
     */
    private $__mapping = array();

    /**
     * Initialize dispatch and detects if a custom dispatcher should be used within the current scope.
     * If no scope is defined, the default dispatcher will be instantiated.
     *
     * @access public
     * @return void
     */
    public function run() {
        $params = $this->app->router->current();
        $dispatch = null;

        if (!empty($this->__mapping)) {
            
            // Specific controller and container
            if (isset($this->__mapping[$params['container'] .'.'. $params['controller']])) {
                $dispatch = $this->__mapping[$params['container'] .'.'. $params['controller']];

            // All controllers within a specific container
            } else if (isset($this->__mapping[$params['container'] .'.*'])) {
                $dispatch = $this->__mapping[$params['container'] .'.*'];

            // Specific controller within any container
            } else if (isset($this->__mapping['*.'. $params['controller']])) {
                $dispatch = $this->__mapping['*.'. $params['controller']];

            // Apply to all controllers and containers
            } else if (isset($this->__mapping['*.*'])) {
                $dispatch = $this->__mapping['*.*'];
            }
        }

        if ($dispatch) {
            $dispatcher = $dispatch;
			$dispatcher->configure($params);
			
        } else {
			switch ($this->app->environment->current()) {
				case 'development':
					$dispatcher = new FrontDev($params);
				break;
				default:
					$dispatcher = new Front($params);
				break;
			}
        }

		$dispatcher->run();
		exit();
    }

    /**
     * Method to apply custom dispatchers to specific module or controller scopes.
     *
     * @access public
     * @param DispatcherInterface $dispatcher
     * @param array $scope
     * @return void
     */
    public function setup(DispatcherInterface $dispatcher, array $scope = array()) {
		$scope = $scope + array('module' => '*', 'controller' => '*');

		if ($scope['module'] != '*') {
			$scope['module'] = Inflector::underscore($scope['module']);
		}

		if ($scope['controller'] != '*') {
			$scope['controller'] = Inflector::underscore($scope['controller']);
		}

		$this->__mapping[$scope['module'] .'.'. $scope['controller']] = $dispatcher;
    }

}
