<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

use \titon\source\Titon;
use \titon\source\library\listeners\ListenerInterface;

/**
 * Provides a way to register functionality to listen and execute within the application without having to edit the core files.
 * Events allow you to define methods or classes that are triggered at certain events within the dispatch cycle,
 * thus allowing you to alter or add to the existing request.
 *
 * @package	titon.source.core
 * @uses	titon\source\Titon
 */
class Event {

	/**
	 * Defined list of allowed events.
	 *
	 * @access private
	 * @var array
	 */
	private $__events = array('preDispatch', 'postDispatch', 'preProcess', 'postProcess', 'preRender', 'postRender');

	/**
	 * Listeners that will be executed.
	 *
	 * @access private
	 * @var array
	 */
	private $__listeners = array();

	/**
	 * Listener object mapping.
	 *
	 * @access private
	 * @var array
	 */
	private $__objectMap = array();

	/**
	 * Cycles through the listenerss for the specified event, and executes the related method.
	 * If a scope is defined, and the listener doesn't match the scope, it will be bypassed.
	 *
	 * @access public
	 * @param string $event
	 * @param object $object
	 * @return void
	 */
	public function execute($event, $object = null) {
		if (!empty($this->__listeners[$event])) {
			$route = Titon::router()->current();

			foreach ($this->__listeners[$event] as &$listener) {
				if ($listener['executed'] === true) {
					continue;
				}

				foreach (array('module', 'controller', 'action') as $action) {
					if (($listener['scope'][$action] !== $route[$action]) || ($listener['scope'][$action] !== '*')) {
						continue;
					}
				}

				if (isset($this->__objectMap[$listener['source']])) {
					$obj = $this->__objectMap[$listener['source']];
					$obj->{$event}($object);
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
	public function listeners($event = null) {
		return isset($this->__listeners[$event]) ? $this->__listeners[$event] : $this->__listeners;
	}

	/**
	 * Register an event listener (predefined class) to be called at certain events.
	 * Can drill down the event to only execute during a certain scope.
	 *
	 * @access public
	 * @param ListenerInterface $listener
	 * @param array $scope
	 * @return this
	 * @chainable
	 */
	public function register(ListenerInterface $listener, array $scope = array()) {
		$class = get_class($listener);
		$this->__objectMap[$class] = $listener;

		foreach ($this->__events as $event) {
			$this->__listeners[$event][$class] = array(
				'executed' => false,
				'listener' => $class,
				'scope' => $scope + array(
					'module' => '*',
					'controller' => '*',
					'action' => '*'
				)
			);
		}

		return $this;
	}

	/**
	 * Remove a certain event and scope from the registered list.
	 *
	 * @access public
	 * @param string $class
	 * @param string $event
	 * @return this
	 * @chainable
	 */
	public function remove($class, $event = null) {
		if (!$event) {
			foreach ($this->__listeners as $event => $listeners) {
				unset($this->__listeners[$event][$class]);
			}
		} else {
			unset($this->__listeners[$event][$class]);
		}

		return $this;
	}

}