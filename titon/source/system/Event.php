<?php
/**
 * Provides a way to register functionality to listen and execute within the application without having to edit the core files.
 * Events allow you to define methods or classes that are triggered at certain events within the dispatch cycle,
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
use \titon\modules\events\EventListenerInterface;
use \Closure;

/**
 * Event Class
 *
 * @package     Titon
 * @subpackage	Titon.System
 */
class Event {

    /**
     * Defined list of allowed events.
     *
     * @access private
     * @var array
     * @static
     */
    private static $__events = array('preDispatch', 'postDispatch', 'preProcess', 'postProcess', 'preRender', 'postRender');

    /**
     * Events that will be executed.
     *
     * @access private
     * @var array
     * @static
     */
    private static $__listeners = array();

    /**
     * Certain classes that have been restricted to only execute during a certain scope.
     *
     * @access private
     * @var array
     * @static
     */
    private static $__scopes = array();

    /**
     * Events objects that have been registered.
     *
     * @access private
     * @var array
     * @static
     */
    private static $__objectMap = array();

    /**
     * Cycles through the listenerss for the specified event, and executes the related method.
     * If a scope is defined, and the listener doesn't match the scope, it will be bypassed.
     *
     * @access public
     * @param string $event
     * @param object $Object    - Object passed by reference: Controller, View, etc
     * @return void
     * @static
     */
    public static function execute($event, $Object = null) {
        if (!empty(self::$__listeners[$event])) {
            $route = Router::current();

            foreach (self::$__listeners[$event] as $slug => &$listener) {
                if ($listener['executed'] === true) {
                    continue;
                }

                // Check to see if the event is restricted to a certain scope
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
					$obj->{$event}($Object);
                }

                $listener['executed'] = true;
            }
        }
    }

    /**
     * Return all registered listeners.
     *
     * @access public
     * @param string $event
     * @return array
     */
    public static function listListeners($event = '') {
        return empty(self::$__listeners[$event]) ? self::$__listeners : self::$__listeners[$event];
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
     * Register an EventListener (predefined class) to be called at certain events.
     * Can drill down the event to only execute during a certain scope (controller, action).
     *
     * @access public
	 * @param EventListenerInterface $listener
     * @param array $scope
     * @return void
     * @static
     */
    public static function register(EventListenerInterface $listener, array $scope = array()) {
		$class = App::toDotNotation(get_class($listener));
		self::$__objectMap[$class] = $listener;

		foreach (self::$__events as $event) {
			self::$__listeners[$event][$class] = array('executed' => false);

			if (!empty($scope)) {
				self::$__scopes[$event][$class] = $scope + array(
					'container' => '*',
					'controller' => '*',
					'action' => '*'
				);
			}
		}
    }
	
    /**
     * Remove a certain event and scope from the registered list.
     *
     * @access public
     * @param string $event
     * @param string $slug
     * @return void
     * @static
     */
    public static function remove($event, $slug) {
        if (isset(self::$__events[$event])) {
            unset(self::$__listeners[$event][$slug]);
            unset(self::$__scopes[$event][$slug]);
        }
    }

}