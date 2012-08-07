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
use titon\libs\listeners\Listener;
use \Closure;

/**
 * Provides a way to register functionality to listen and execute within the application without having to edit the core files.
 * Events allow you to define methods or classes that are triggered at certain events within the dispatch cycle,
 * thus allowing you to alter or add to the existing request.
 *
 * @package	titon.core
 */
class Event {

	/**
	 * Defined list of allowed events.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_events = [
		'titon.startup', 'titon.shutdown',
		'dispatch.preDispatch', 'dispatch.postDispatch',
		'controller.preProcess', 'controller.postProcess',
		'view.preRender', 'view.postRender'
	];

	/**
	 * Listeners that will be executed.
	 *
	 * @access private
	 * @var array
	 */
	protected $_listeners = [];

	/**
	 * Register a basic callback using a Closure. This callback can be restricted in scope and a per event basis.
	 * Can drill down the event to only execute during a certain scope.
	 *
	 * @access public
	 * @param Closure $callback
	 * @param array $events
	 * @param array $scope
	 * @return titon\core\Event
	 * @chainable
	 */
	public function addCallback(Closure $callback, array $events = [], array $scope = []) {
		if (empty($events)) {
			$events = $this->_events;
		}

		$this->_listeners[] = [
			'object' => $callback,
			'executed' => [],
			'events' => $events,
			'scope' => $scope + [
				'module' => '*',
				'controller' => '*',
				'action' => '*'
			]
		];

		return $this;
	}

	/**
	 * Register an event listener (predefined class) to be called at certain events.
	 * Can drill down the event to only execute during a certain scope.
	 *
	 * @access public
	 * @param titon\libs\listeners\Listener $listener
	 * @param array $scope
	 * @return titon\core\Event
	 * @chainable
	 */
	public function addListener(Listener $listener, array $scope = []) {
		$this->_listeners[] = [
			'object' => $listener,
			'executed' => [],
			'events' => [],
			'scope' => $scope + [
				'module' => '*',
				'controller' => '*',
				'action' => '*'
			]
		];

		return $this;
	}

	/**
	 * Return all assigned events.
	 *
	 * @access public
	 * @return array
	 */
	public function getEvents() {
		return $this->_events;
	}

	/**
	 * Return all registered listeners.
	 *
	 * @access public
	 * @return array
	 */
	public function getListeners() {
		return $this->_listeners;
	}

	/**
	 * Cycles through the listeners for the specified event, and executes the related method.
	 * If a scope is defined, and the listener doesn't match the scope, it will be bypassed.
	 *
	 * @access public
	 * @param string $event
	 * @param object $object
	 * @return void
	 */
	public function notify($event, $object = null) {
		$route = Titon::router()->current();

		if (!$route || !$this->_listeners) {
			return;
		}

		foreach ($this->_listeners as &$listener) {
			if (isset($listener['executed'][$event])) {
				continue;
			}

			foreach (['module', 'controller', 'action'] as $action) {
				if ($listener['scope'][$action] !== $route->param($action) || $listener['scope'][$action] !== '*') {
					continue;
				}
			}

			$obj = $listener['object'];
			$method = $event;

			if (mb_strpos($event, '.') !== false) {
				list($scope, $method) = explode('.', $event);
			}

			if ($obj instanceof Closure && in_array($event, $listener['events'])) {
				$obj($event, $object);

			} else if (method_exists($obj, $method)) {
				$obj->{$method}($object);

			} else {
				continue;
			}

			$listener['executed'][$event] = true;
		}
	}

	/**
	 * Add custom events to the system.
	 *
	 * @access public
	 * @param array $events
	 * @return titon\core\Event
	 * @chainable
	 */
	public function setup(array $events) {
		$this->_events = $events + $this->_events;

		return $this;
	}

}