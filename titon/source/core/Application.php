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
use \titon\source\log\Debugger;
use \titon\source\log\Exception;

/**
 * The class manages the location and installation of controllers and modules,
 * to speed up the lookup process of its sub-classes.
 *
 * @package	titon.source.core
 * @uses	titon\source\Titon
 * @uses	titon\source\log\Debugger
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
	private $__defaultModule = 'core';

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
		$this->__modules[$module]['controllers'][] = $controller;

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
		$this->__modules[$module] = array(
			'index' => $module,
			'controllers' => $controllers
		);

		return $this;
	}

	/**
	 * Get the currently defined charset for the application.
	 *
	 * @access public
	 * @return string
	 */
	public function charset() {
		return Titon::config()->get('app.encoding') ?: 'UTF-8';
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
		return Titon::config()->get('app.name') ?: '';
	}

	/**
	 * Get the currently defined salt for the application.
	 *
	 * @access public
	 * @return string
	 */
	public function salt() {
		return Titon::config()->get('app.salt') ?: '';
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
		$module = strtolower($module);

		if (isset($this->__modules[$module])) {
			$this->__defaultModule = $module;
		} else {
			throw new Exception(sprintf('Can not set default module as %s does not exist.', $module));
		}

		return $this;
	}

	/**
	 * Set which controller should be the module index.
	 *
	 * @access public
	 * @param string $module
	 * @param string $index
	 * @return this
	 * @chainable
	 */
	public function setModuleIndex($module, $index) {
		if (!empty($index)) {
			$this->__modules[$module]['index'] = $index;
		}

		return $this;
	}

}