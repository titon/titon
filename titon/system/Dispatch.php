<?php
/**
 * The Dispatch is the outermost script (Front Controller) of the application and handles the HTTP request and response cycle.
 * Once it receives the HTTP request, it determines the correct Dispatcher module to use, parses the current Route,
 * traverses the Controller and Container path, and then finally dispatches and outputs the HTTP response.
 *
 * You may use the setup() method to define the use of different Dispatchers for specific Controller and
 * Container scopes. The setup() method should be used within the applications setup center.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\system;

use \titon\core\App;
use \titon\core\Registry;
use \titon\log\Error;
use \titon\log\Exception;
use \titon\router\Router;
use \titon\utility\Inflector;

/**
 * Dispatch Class
 *
 * @package		Titon
 * @subpackage	Titon.System
 */
class Dispatch {

    /**
     * Mapped scopes to custom Dispatchers.
     *
     * @access private
     * @var array
     * @static
     */
    private static $__mapping = array();

    /**
     * Initialize dispatch and detects if a custom dispatcher should be used within the current scope.
     * If no scope is defined, the default dispatcher will be instantiated.
     *
     * @access public
     * @return void
     * @static
     */
    public static function initialize() {
        $params = Router::current();
        $dispatch = null;

        if (!empty(self::$__mapping)) {
            
            // Specific controller and container
            if (isset(self::$__mapping[$params['container'] .'.'. $params['controller']])) {
                $dispatch = self::$__mapping[$params['container'] .'.'. $params['controller']];

            // All controllers within a specific container
            } else if (isset(self::$__mapping[$params['container'] .'.*'])) {
                $dispatch = self::$__mapping[$params['container'] .'.*'];

            // Specific controller within any container
            } else if (isset(self::$__mapping['*.'. $params['controller']])) {
                $dispatch = self::$__mapping['*.'. $params['controller']];

            // Apply to all controllers and containers
            } else if (isset(self::$__mapping['*.*'])) {
                $dispatch = self::$__mapping['*.*'];
            }
        }

        if ($dispatch) {
            $Dispatcher = $dispatch($params);
        } else {
            $Dispatcher = new \titon\modules\dispatchers\front\Front($params);
        }

        if ($Dispatcher instanceof \titon\modules\dispatchers\DispatcherInterface) {
            $Dispatcher->run();
            exit();
        }

        throw new Exception(sprintf('%s Dispatcher must implement the \titon\modules\dispatchers\DispatcherInterface', $dispatch));
    }

    /**
     * Method to apply custom dispatchers to specific container or controller scopes.
     *
     * @access public
     * @param array $config
     * @param Closure $dispatcher
     * @return void
     * @static
     */
    public static function setup(array $config, Closure $dispatcher) {
        if (!empty($config)) {
            foreach ($config as $scope) {
                $scope = $scope + array('container' => '*', 'controller' => '*');

                if ($scope['container'] != '*') {
                    $scope['container'] = Inflector::underscore($scope['container']);
                }

                if ($scope['controller'] != '*') {
                    $scope['controller'] = Inflector::underscore($scope['controller']);
                }

                self::$__mapping[$scope['container'] .'.'. $scope['controller']] = $dispatcher;
            }
        }
    }

}
