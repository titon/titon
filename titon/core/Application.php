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
	 * @access protected
	 * @var array
	 */
	protected $_modules = array();

	/**
	 * Return all controllers, or a modules controllers.
	 *
	 * @access public
	 * @param string $module
	 * @return array
	 */
	public function controllers($module = null) {
		return isset($this->_modules[$module]) ? $this->_modules[$module] : $this->_modules;
	}

	/**
	 * Bootstrap the application.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		foreach (scandir(APP_MODULES) as $module) {
			if (is_file(APP_MODULES . $module . DS .'bootstrap.php')) {
				include_once APP_MODULES . $module . DS .'bootstrap.php';
			}
		}
	}

	/**
	 * Return a list of added modules.
	 *
	 * @access public
	 * @return array
	 */
	public function modules() {
		return array_keys($this->_modules);
	}

	/**
	 * Add a module (and controllers) to the application for fast lookup.
	 *
	 * @access public
	 * @param string $module
	 * @param string|array $controllers
	 * @return this
	 * @chainable
	 */
	public function setup($module, $controllers = array()) {
		$module = (string)$module;
		
		if (!isset($this->_modules[$module])) {
			$this->_modules[$module] = array();
		}

		if (!empty($controllers)) {
			if (!is_array($controllers)) {
				$controllers = array($controllers);
			}

			foreach ($controllers as $controller) {
				if (!empty($controller)) {
					$this->_modules[$module][] = (string)$controller;
				}
			}

			$this->_modules[$module] = array_unique($this->_modules[$module]);
		}

		return $this;
	}

}