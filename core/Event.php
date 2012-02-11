<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use \titon\Titon;
use \titon\libs\listeners\Listener;
use \Closure;

/**
 * Provides a way to register functionality to listen and execute within the application without having to edit the core files.
 * Events allow you to define methods or classes that are triggered at certain events within the dispatch cycle,
 * thus allowing you to alter or add to the existing request.
 *
 * @package	titon.core
 * @uses	titon\Titon
 */
class Event {

	/**
	 * Defined list of allowed events.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_events = array('startup', 'shutdown', 'preDispatch', 'postDispatch', 'preProcess', 'postProcess', 'preRender', 'postRender');

	/**
	 * Listeners that will be executed.
	 *
	 * @access private
	 * @var array
	 */
	protected $_listeners = array();

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
		if (empty($this->_listeners)) {
			return;
		}

		$route = Titon::router()->current();

		foreach ($this->_listeners as &$listener) {
			if ($listener['executed']) {
				continue;
			}

			foreach (array('module', 'controller', 'action') as $action) {
				if ($listener['scope'][$action] !== $route->param($action) || $listener['scope'][$action] !== '*') {
					continue;
				}
			}

			$obj = $listener['object'];
			
			if ($obj instanceof Closure) {
				if (empty($listener['events']) || in_array($event, $listener['events'])) {
					$obj($event, $object);
				}
				
			} else if (method_exists($event, $obj)) {
				$obj->{$event}($object);
			}

			$listener['executed'] = true;
		}
	}

	/**
	 * Return all registered listeners.
	 *
	 * @access public
	 * @return array
	 */
	public function listeners() {
		return $this->_listeners;
	}

	/**
	 * Register an event listener (predefined class) to be called at certain events.
	 * Can drill down the event to only execute during a certain scope.
	 *
	 * @access public
	 * @param Listener $listener
	 * @param array $scope
	 * @return Event
	 * @chainable
	 */
	public function registerListener(Listener $listener, array $scope = array()) {
		$this->_listeners[] = array(
			'object' => $listener,
			'executed' => false,
			'events' => array(),
			'scope' => $scope + array(
				'module' => '*',
				'controller' => '*',
				'action' => '*'
			)
		);

		return $this;
	}

	/**
	 * Register a basic callback using a Closure. This callback can be restricted in scope and a per event basis.
	 * Can drill down the event to only execute during a certain scope.
	 *
	 * @access public
	 * @param Closure $callback
	 * @param array $events
	 * @param array $scope
	 * @return Event
	 * @chainable
	 */
	public function registerCallback(Closure $callback, array $events = array(), array $scope = array()) {
		$this->_listeners[] = array(
			'object' => $callback,
			'executed' => false,
			'events' => $events,
			'scope' => $scope + array(
				'module' => '*',
				'controller' => '*',
				'action' => '*'
			)
		);

		return $this;
	}

	/**
	 * Add custom events to the system.
	 * 
	 * @access public
	 * @param array $events
	 * @return Event 
	 * @chainable
	 */
	public function setup(array $events) {
		$this->_events = $events + $this->_events;
		
		return $this;
	}

}