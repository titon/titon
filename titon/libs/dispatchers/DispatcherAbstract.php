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
use \titon\base\Base;
use \titon\libs\dispatchers\Dispatcher;
use \titon\libs\dispatchers\DispatcherException;
use \titon\utility\Inflector;

/**
 * The Dispatcher acts as the base for all child Dispatcher Modules. The Dispatcher should not be confused with Dispatch.
 * Dispatch determines the current request and then calls the Dispatcher to output the current request.
 * The Dispatcher has many default methods for locating and validating objects within the MVC paradigm.
 *
 * @package	titon.libs.dispatchers
 */
abstract class DispatcherAbstract extends Base implements Dispatcher {

    /**
     * Controller object.
     *
     * @access public
     * @var Controller
     */
    public $controller;

    /**
     * View object.
     *
     * @access public
     * @var View
     */
    public $view;

    /**
     * Store the configuration and load the Controller and View objects.
     *
     * @access public
     * @param array $config
     * @return void
     */
    public function initialize(array $config = array()) {
		// @temporary
		$this->view = new \titon\system\View();
		$this->controller = new \titon\system\Controller();
    }

    /**
     * Primary method to run the dispatcher and its process its logic.
     *
     * @access public
     * @return void
     */
    public function run() {
        throw new Exception('You must define your own run() method to dispatch the current request.');
    }

	/**
	 * Attempts to determine a Controller path and load the Controller. If found, returns the instance.
	 *
	 * @access public
	 * @return object|null
	 */
	final public function loadController() {
        $path = $this->routedPath(CONTROLLERS);

        if (file_exists($path)) {
            include_once $path;

            $class = App::toNamespace($path);

            if (class_exists($class)) {
                $Controller = new $class($this->_config, $this->View);

                if ($Controller instanceof \titon\system\Controller) {
                    return $Controller;
                } else {
					throw new Exception(sprintf('The Controller %s must extend \app\AppController.', $this->_config['controller']));
				}
            }
        }

		throw new Exception(sprintf('The Controller %s does not exist.', $this->_config['controller']));
	}

	/**
	 * Attempts to determine a View path and load the View. If found, returns the instance, else returns the AppView.
	 *
	 * @access public
	 * @return object|null
	 */
    final public function loadView() {
        $path = $this->routedPath(VIEWS);

        // Use created class if it exists
        if (file_exists($path)) {
            include_once $path;

            $class = App::toNamespace($path);

            if (class_exists($class)) {
                $View = new $class();
            }
            
        // Else use the AppView
        } else {
            $View = new \app\AppView();
        }

        if ($View instanceof \titon\system\View) {
            return $View;
        }

		throw new Exception(sprintf('The View %s must extend \app\AppView.', $this->_config['controller']));
    }

	/**
	 * Build out the path based on the passed router parameters.
	 *
	 * @access public
	 * @param string $base
	 * @return string
	 */
    final public function routedPath($base) {
        $path = $base;
        
        if (!empty($this->_config['container'])) {
            $path .= $this->_config['container'] . DS;
        }

        $path .= Inflector::filename($this->_config['controller']);

        return $path;
    }

}
