<?php
/**
 * Provides a way to hook into the application without having to edit the core files.
 * Hooks allow you to define methods or classes that are triggered at certain events within the dispatch cycle,
 * thus allowing you to alter or add to the existing request.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\system;

use \titon\core\Registry;
use \titon\log\Exception;
use \titon\router\Router;

/**
 * Hook Class
 *
 * @package		Titon
 * @subpackage	Titon.System
 */
class Hook {

    /**
     * Defined list of allowed events.
     *
     * @access private
     * @var array
     * @static
     */
    private static $__events = array('preDispatch', 'postDispatch', 'initialize', 'preProcess', 'postProcess', 'preRender', 'postRender');

    /**
     * Hooks with their respective module, class and namespace, that will be executed.
     *
     * @access private
     * @var array
     * @static
     */
    private static $__hooks = array();

    /**
     * Certain classes that have been restricted to only execute during a certain scope.
     *
     * @access private
     * @var array
     * @static
     */
    private static $__scopes = array();

    /**
     * Cycles through the hooks for the specified event, and executes the related method.
     * If a scope is defined, and the hook doesn't match the scope, it will be bypassed.
     *
     * @access public
     * @param string $event
     * @param object $Object    - Object passed by reference: Controller, Engine, etc
     * @return void
     * @static
     */
    public static function execute($event, $Object = null) {
        if (!empty(self::$__hooks[$event])) {
            $route = Router::current();

            foreach (self::$__hooks[$event] as &$hook) {
                if ($hook['executed'] === true) {
                    continue;
                }

                if (isset($hook['function'])) {
                    $slug = $hook['function'];
                } else {
                    $slug = $hook['namespace'];
                }

                // Check to see if the hook is restricted to a certain scope
                if (isset(self::$__scopes[$event][$slug])) {
                    $scope = self::$__scopes[$event][$slug];

                    foreach (array('container', 'controller', 'action') as $action) {
                        if (($scope[$action] != $route[$action]) || ($scope[$action] != '*')) {
                            continue;
                        }
                    }
                }

                if (!empty($hook['namespace'])) {
                    $obj = Registry::factory(array('namespace' => $hook['namespace']));

                    if (method_exists($obj, $hook['method'])) {
                        $obj->{$hook['method']}($Object);
                    }

                } else if (!empty($hook['function'])) {
                    if (function_exists($hook['function'])) {
                        $hook['function']($Object);
                    }
                }

                $hook['executed'] = true;
            }
        }
    }

    /**
     * Return all registered hooks, or event specific hooks.
     *
     * @access public
     * @param string $event
     * @return array
     */
    public static function listHooks($event = '') {
        return (empty(self::$__hooks[$event]) ? self::$__hooks : self::$__hooks[$event]);
    }

    /**
     * Return all restricted scopes, or event specific scopes.
     *
     * @access public
     * @param string $event
     * @return array
     */
    public static function listScopes($event = '') {
        return (empty(self::$__scopes[$event]) ? self::$__scopes : self::$__scopes[$event]);
    }

    /**
     * Register a Hook (predefined class) to be called at certain events.
     * Can drill down the hook to only execute during a certain scope (controller, action).
     *
     * @access public
     * @param string $class
     * @param array $scope
     * @return void
     * @static
     */
    public static function register($class, array $scope = array()) {
        $options = self::__prepare(array(
            'class' => $class,
            'module' => 'Hook'
        ));

        if (!empty($options['namespace'])) {
            foreach (self::$__events as $event) {

                $options['method'] = $event;
                self::$__hooks[$event][] = $options;

                if (!empty($scope)) {
                    self::$__scopes[$event][$options['namespace']] = $scope + array(
                        'container' => '*',
                        'controller' => '*',
                        'action' => '*'
                    );
                }
            }
        }
    }

    /**
     * Register a stand alone function to be called at certain events.
     * Can drill down the hook to only execute during a certain scope (controller, action).
     *
     * @access public
     * @param string $event
     * @param string $function
     * @param array $scope
     * @return void
     * @static
     */
    public static function registerFunction($event, $function, array $scope = array()) {
        if (!in_array($event, self::$__events)) {
            throw new Exception('The '. $event .' event is not a supported hook event.');
        }

        self::$__hooks[$event][] = array(
            'function' => (string) $function,
            'executed' => false
        );

        if (!empty($scope)) {
            self::$__scopes[$event][$function] = $scope + array(
                'container' => '*',
                'controller' => '*',
                'action' => '*'
            );
        }
    }

    /**
     * Register a class and method to be called at certain events.
     * Can drill down the hook to only execute during a certain scope (controller, action).
     *
     * @access public
     * @param string $event
     * @param array $options
     * @param array $scope
     * @return void
     * @static
     */
    public static function registerMethod($event, array $options, array $scope = array()) {
        $options = self::__prepare($options);

        if (!in_array($event, self::$__events)) {
            throw new Exception('The '. $event .' event is not a supported hook event.');
        }

        self::$__hooks[$event][] = $options;

        if (!empty($scope)) {
            self::$__scopes[$event][$options['namespace']] = $scope + array(
                'container' => '*',
                'controller' => '*',
                'action' => '*'
            );
        }
    }

    /**
     * Remove a certain class/method hook and scope from the registered list.
     *
     * @access public
     * @param string $event
     * @param array $options
     * @return void
     * @static
     */
    public static function remove($event, array $options = array()) {
        if (!empty(self::$__hooks[$event])) {
            $key = null;
            $slug = $options['namespace'];
            
            if (empty($options['method'])) {
                $options['method'] = $event;
            }

            foreach (self::$__hooks[$event] as $index => $hook) {
                if (($options['module'] == $hook['module']) && ($options['class'] == $hook['class']) && ($options['method'] == $hook['method'])) {
                    $key = $index;
                    break;
                }
            }

            unset(self::$__hooks[$event][$key]);
            unset(self::$__scopes[$event][$slug]);
        }
    }

    /**
     * Remove a certain function hook and scope from the registered list.
     *
     * @access public
     * @param string $event
     * @param string $function
     * @return void
     * @static
     */
    public static function removeFunction($event, $function) {
        if (!empty(self::$__hooks[$event])) {
            $key = null;

            foreach (self::$__hooks[$event] as $index => $hook) {
                if ((isset($hook['function'])) && ($function == $hook['function'])) {
                    $key = $index;
                    break;
                }
            }

            unset(self::$__hooks[$event][$key]);
            unset(self::$__scopes[$event][$function]);
        }
    }

    /**
     * Prepare the hook with all the required credentials.
     *
     * @access private
     * @param array $options
     * @return array
     * @static
     */
    private static function __prepare($options) {
        $defaults = array('class' => '', 'module' => '', 'method' => '', 'namespace' => '', 'executed' => false);
        $options = $options + $defaults;

        if (empty($options['namespace'])) {
            throw new Exception('Namespace is required to register hooks.');
        }

        if (empty($options['method']) && !empty($options['function'])) {
            $options['method'] = $options['function'];
            unset($options['function']);
        }

        return $options;
    }

}