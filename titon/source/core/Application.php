<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

use \titon\source\log\Exception;

/**
 * This class manages the location and installation of controllers and modules,
 * to speed up the lookup process of its sub-classes.
 *
 * @package	titon.source.core
 * @uses	titon\source\log\Exception
 */
class Application {

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
	private $__default = 'core';

	/**
	 * Add a controller to a module.
	 *
	 * @access public
	 * @param string $module
	 * @param string $controller
	 * @return this
	 * @chainable
	 */
	public function addController($module, $controller) {
		$this->__modules[$module][] = $controller;

		return $this;
	}

	/**
	 * Add a module to the application for fast lookup.
	 *
	 * @access public
	 * @param string $module
	 * @param array $controllers
	 * @return this
	 * @chainable
	 */
	public function addModule($module, array $controllers = array()) {
		$this->__modules[$module] = $controllers;

		return $this;
	}

	/**
	 * Return all controllers, or a modules controllers.
	 *
	 * @access public
	 * @param string $module
	 * @return array
	 */
	public function getControllers($module = null) {
		return isset($this->__modules[$module]) ? $this->__modules[$module] : $this->__modules;
	}

	/**
	 * Return the default module.
	 *
	 * @access public
	 * @return string
	 */
	public function getDefaultModule() {
		return $this->__default;
	}

	/**
	 * Return a list of added modules.
	 *
	 * @access public
	 * @return array
	 */
	public function getModules() {
		return array_keys($this->__modules);
	}

	/**
	 * Bootstrap the application.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		foreach (scandir(APP_MODULES) as $module) {
			if (is_file(APP_MODULES . $module . DS .'Bootstrap.php')) {
				include_once APP_MODULES . $module . DS .'Bootstrap.php';
			}
		}
	}

	/**
	 * Set the default routing module. Defaults to core.
	 *
	 * @access public
	 * @param string $module
	 * @return this
	 * @chainable
	 */
	public function setDefaultModule($module) {
		if (isset($this->__modules[$module])) {
			$this->__default = $module;
		} else {
			throw new Exception(sprintf('Can not set default module as %s does not exist.', $module));
		}

		return $this;
	}

}