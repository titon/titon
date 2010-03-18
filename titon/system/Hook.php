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

use \titon\core\App;
use \titon\log\Exception;
use \titon\router\Router;
use \titon\modules\hooks\HookInterface;
use \Closure;

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
    private static $__events = array('preDispatch', 'postDispatch', 'preProcess', 'postProcess', 'preRender', 'postRender');

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
	 * Hook objects that have been registered through a Closure.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__objectMap = array();

    /**
     * Cycles through the hooks for the specified event, and executes the related method.
     * If a scope is defined, and the hook doesn't match the scope, it will be bypassed.
     *
     * @access public
     * @param string $event
     * @param object $Object    - Object passed by reference: Controller, View, etc
     * @return void
     * @static
     */
    public static function execute($event, $Object = null) {
        if (!empty(self::$__hooks[$event])) {
            $route = Router::current();

            foreach (self::$__hooks[$event] as $slug => &$hook) {
                if ($hook['executed'] === true) {
                    continue;
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

                if (isset(self::$__objectMap[$slug])) {
                    $obj = self::$__objectMap[$slug];

					if ($obj instanceof Closure) {
						$obj($Object);
					} else if (method_exists($obj, $event)) {
                        $obj->{$event}($Object);
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
        return empty(self::$__hooks[$event]) ? self::$__hooks : self::$__hooks[$event];
    }

    /**
     * Return all restricted scopes, or event specific scopes.
     *
     * @access public
     * @param string $event
     * @return array
     */
    public static function listScopes($event = '') {
        return empty(self::$__scopes[$event]) ? self::$__scopes : self::$__scopes[$event];
    }

    /**
     * Register a Hook (predefined class) to be called at certain events.
     * Can drill down the hook to only execute during a certain scope (controller, action).
     *
     * @access public
	 * @param HookInterface $Hook
     * @param array $scope
     * @return void
     * @static
     */
    public static function register(HookInterface $Hook, array $scope = array()) {
        if ($Hook) {
			$class = App::toDotNotation(get_class($Hook));
			self::$__objectMap[$class] = $Hook;

            foreach (self::$__events as $event) {
                self::$__hooks[$event][$class] = array('executed' => false);

                if (!empty($scope)) {
                    self::$__scopes[$event][$class] = $scope + array(
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
	 * @param Closure $Function
     * @param array $events
     * @param array $scope
     * @return void
     * @static
     */
    public static function registerFunction(Closure $Function, array $events = array(), array $scope = array()) {
		if ($Function) {
			if (empty($events)) {
				$events = self::$__events;
			}

			$slug = time();
			self::$__objectMap[$slug] = $Function;

			foreach ($events as $event) {
				self::$__hooks[$event][$slug] = array('executed' => false);

				if (!empty($scope)) {
					self::$__scopes[$event][$slug] = $scope + array(
						'container' => '*',
						'controller' => '*',
						'action' => '*'
					);
				}
			}
		}
    }

    /**
     * Remove a certain hook and scope from the registered list.
     *
     * @access public
     * @param string $event
     * @param string $slug
     * @return void
     * @static
     */
    public static function remove($event, $slug) {
        if (isset(self::$__events[$event])) {
            unset(self::$__hooks[$event][$slug]);
            unset(self::$__scopes[$event][$slug]);
        }
    }

}