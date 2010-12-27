<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

use \titon\source\core\Config;
use \titon\source\core\Environment;
use \titon\source\core\Loader;
use \titon\source\core\Registry;
use \titon\source\core\Router;
use \titon\source\log\Debugger;
use \titon\source\log\Exception;

/**
 * The application class contains all core classes that manipulate and power the application, or add quick convenience.
 * It also manages the location and installation of controllers and modules, to speed up the lookup process of its sub-classes.
 *
 * @package	titon.source.core
 * @uses	Config
 * @uses	Environment
 * @uses	Loader
 * @uses	Registry
 * @uses	Router
 * @uses	Debugger
 * @uses	Exception
 */
class Application {

	/**
	 * Core config class.
	 *
	 * @see Config
	 * @access public
	 * @var object
	 */
	public $config;

	/**
	 * Core environment class.
	 *
	 * @see Environment
	 * @access public
	 * @var object
	 */
	public $environment;

	/**
	 * Core event class.
	 *
	 * @see Event
	 * @access public
	 * @var object
	 */
	public $event;

	/**
	 * Core loader class.
	 *
	 * @see Loader
	 * @access public
	 * @var object
	 */
	public $loader;

	/**
	 * Core registry class.
	 *
	 * @see Registry
	 * @access public
	 * @var object
	 */
	public $registry;

	/**
	 * Core router class.
	 *
	 * @see Router
	 * @access public
	 * @var object
	 */
	public $router;

	/**
	 * List of added modules.
	 *
	 * @access public
	 * @var array
	 */
	private $__modules = array();

	/**
	 * Default routed module.
	 *
	 * @access public
	 * @var string
	 */
	private $__defaultModule = 'core';

	/**
	 * Initialize all the core classes.
	 *
	 * @access private
	 * @return void
	 */
	public function __construct() {
		$this->config = new Config();
		$this->environment = new Environment();
		$this->event = new Event();
		$this->loader = new Loader();
		$this->registry = new Registry();
		$this->router = new Router();

		// Initialize static classes
		Debugger::initialize();

		$this->loadBootstraps();
	}

	/**
	 * Add a controller to a module.
	 *
	 * @access public
	 * @param string $module
	 * @param string $controller
	 * @return void
	 */
	public function addController($module, $controller) {
		$this->__modules[$module]['controllers'][] = $controller;
	}

	/**
	 * Add a module to the application for fast lookup.
	 *
	 * @access public
	 * @param string $module
	 * @param array $controllers
	 * @return void
	 */
	public function addModule($module, array $controllers = array()) {
		$this->__modules[$module] = array(
			'index' => $module,
			'controllers' => $controllers
		);
	}

	/**
	 * Get the currently defined charset for the application.
	 *
	 * @access public
	 * @return string
	 */
	public function charset() {
		return $this->config->get('app.encoding') ?: 'UTF-8';
	}

	/**
	 * Return all controllers, or a modules controllers.
	 *
	 * @access public
	 * @param string $module
	 * @return array
	 */
	public function getControllers($module = null) {
		if (isset($this->__modules[$module])) {
			return $this->__modules[$module]['controllers'];
		}

		$controllers = array();

		if (!empty($this->__modules)) {
			foreach ($this->__modules as $module => $data) {
				$controllers[$module] = $data['controllers'];
			}
		}

		return $controllers;
	}

	/**
	 * Return a list of added modules.
	 *
	 * @access public
	 * @param boolean $list
	 * @return array
	 */
	public function getModules($list = true) {
		return ($list) ? array_keys($this->__modules) : $this->__modules;
	}

	/**
	 * Include the module bootstrap files.
	 *
	 * @access public
	 * @return void
	 */
	public function loadBootstraps() {
		foreach (scandir(APP_MODULES) as $module) {
			if (is_file(APP_MODULES . $module . DS .'Bootstrap.php')) {
				include_once APP_MODULES . $module . DS .'Bootstrap.php';
			}
		}
	}

	/**
	 * Grabs the defined project name.
	 *
	 * @access public
	 * @return string
	 */
	public function name() {
		return $this->config->get('app.name') ?: '';
	}

	/**
	 * Get the currently defined salt for the application.
	 *
	 * @access public
	 * @return string
	 */
	public function salt() {
		return $this->config->get('app.salt') ?: '';
	}

	/**
	 * Set the default routing module. Defaults to core.
	 *
	 * @access public
	 * @param string $module
	 * @return void
	 */
	public function setDefaultModule($module) {
		$module = mb_strtolower($module);

		if (isset($this->__modules[$module])) {
			$this->__defaultModule = $module;
		} else {
			throw new Exception(sprintf('Can not set default module as %s does not exist.', $module));
		}
	}

	/**
	 * Set which controller should be the module index.
	 *
	 * @access public
	 * @param string $module
	 * @param string $index
	 * @return void
	 */
	public function setModuleIndex($module, $index) {
		if (!empty($index)) {
			$this->__modules[$module]['index'] = $index;
		}
	}

}