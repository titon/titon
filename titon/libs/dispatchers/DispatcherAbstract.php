<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\dispatchers;

use \titon\Titon;
use \titon\base\Prototype;
use \titon\libs\controllers\Controller;
use \titon\libs\dispatchers\Dispatcher;
use \titon\libs\dispatchers\DispatcherException;
use \titon\libs\views\View;
use \titon\utility\Inflector;

/**
 * The Dispatcher acts as the base for all child dispatchers. The Dispatcher should not be confused with Dispatch.
 * Dispatch determines the current request and then calls the Dispatcher to output the current request.
 * The Dispatcher has many default methods for locating and validating objects within the MVC paradigm.
 *
 * @package	titon.libs.dispatchers
 * @uses	titon\libs\dispatchers\DispatcherException
 * @abstract
 */
abstract class DispatcherAbstract extends Prototype implements Dispatcher {

	/**
	 * Lazy load the view and controller objects.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('view', function($self) {
			return $self->loadView();
		});
		
		$this->attachObject('controller', function($self) {
			$controller = $self->loadController();
			$controller->setView($self->getObject('view'));
			
			return $controller;
		});
	}
	
	/**
	 * Return the system View as this is not a modular class.
	 * 
	 * @access public
	 * @return View
	 */
	public function loadView() {
		return Titon::registry()->factory('titon\libs\views\View');
	}
	
	/**
	 * Load the controller based on the routing params. If the controller does not exist, throw exceptions.
	 * 
	 * @todo - In production, it should throw up some sort of 404 error page.
	 * 
	 * @access public
	 * @return Controller 
	 */
	public function loadController() {
		$config = $this->config();
		$path = APP_MODULES . $config['module'] . DS .'controllers'. DS . Inflector::filename($config['controller'] .'Controller');

		if (file_exists($path)) {
			return Titon::registry()->factory($path, $config);
			
		} else if (Titon::environment()->isDefault()) {
			throw new DispatcherException(sprintf('Controller %s could not be found in the %s module.', $config['controller'], $config['module']));
		}
		
		return new Controller();
	}

	/**
	 * Primary method to run the dispatcher and its process its logic.
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		throw new DispatcherException('You must define your own run() method to dispatch the current request.');
	}

}
