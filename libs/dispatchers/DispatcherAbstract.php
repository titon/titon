<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\dispatchers;

use \titon\Titon;
use \titon\base\Base;
use \titon\libs\controllers\Controller;
use \titon\libs\controllers\core\ErrorController;
use \titon\libs\dispatchers\Dispatcher;
use \titon\libs\dispatchers\DispatcherException;
use \titon\libs\traits\Prototype;
use \titon\utility\Inflector;

/**
 * The Dispatcher acts as the base for all child dispatchers. The Dispatcher should not be confused with Dispatch.
 * Dispatch determines the current request and then calls the Dispatcher to output the current request.
 * The Dispatcher has many default methods for locating and validating objects within the MVC paradigm.
 *
 * @package	titon.libs.dispatchers
 * @uses	titon\Titon
 * @uses	titon\libs\dispatchers\DispatcherException
 * @abstract
 */
abstract class DispatcherAbstract extends Base implements Dispatcher {
	use Prototype;

	/**
	 * Lazy load the controller object. Do not allow overrides.
	 *
	 * @access public
	 * @return void
	 * @final
	 */
	final public function initialize() {
		$this->attachObject(array(
			'alias' => 'controller',
			'interface' => '\titon\libs\controllers\Controller'
		), function($self) {
			return $self->loadController();
		});
	}

	/**
	 * Load the controller based on the routing params. If the controller does not exist, throw exceptions.
	 * 
	 * @access public
	 * @return Controller
	 * @throws DispatcherException 
	 */
	public function loadController() {
		$config = $this->config();
		$module = Titon::app()->modules($config['module']);
		$controller = $module['controllers'][$config['controller']];
		
		// Load controller
		$path = $module['path'] . 'controllers' . DS . $controller . '.php';

		if (file_exists($path)) {
			return Titon::registry()->factory($path, $config);
		} else {
			throw new DispatcherException(sprintf('%s could not be located in the file system.', $controller));
		}
		
		// Return error controller as fallback
		return new ErrorController($config);
	}

	/**
	 * Primary method to run the dispatcher and its process its logic.
	 *
	 * @access public
	 * @return void
	 * @throws DispatcherException
	 */
	public function run() {
		throw new DispatcherException('You must define your own run() method to dispatch the current request.');
	}

}
