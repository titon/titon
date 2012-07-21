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
use titon\core\CoreException;

/**
 * This class manages the location and installation of controllers and modules,
 * to speed up the lookup process of its sub-classes.
 *
 * @package	titon.core
 */
class Application {

	/**
	 * List of controllers per module.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_controllers = [];

	/**
	 * List of modules.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_modules = [];

	/**
	 * Return all controllers.
	 *
	 * @access public
	 * @return array
	 */
	public function getControllers() {
		return $this->_controllers;
	}

	/**
	 * Return a single module.
	 *
	 * @access public
	 * @param string $module
	 * @return array
	 * @throws titon\core\CoreException
	 */
	public function getModule($module) {
		if (isset($this->_modules[$module])) {
			return $this->_modules[$module];
		}

		throw new CoreException(sprintf('Could not locate %s module.', $module));
	}

	/**
	 * Return all modules
	 *
	 * @access public
	 * @return array
	 */
	public function getModules() {
		return $this->_modules;
	}

	/**
	 * Bootstrap the application after Titon has startup.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		// Set encoding
		mb_internal_encoding(Titon::config()->encoding());

		// Bootstrap modules
		foreach ($this->getModules() as $module) {
			$path = $module['path'] . 'bootstrap.php';

			if (file_exists($path)) {
				include_once $path;
			}
		}
	}

	/**
	 * Add a module to the application for fast lookup.
	 *
	 * @access public
	 * @param string $module
	 * @param string $path
	 * @param array $controllers
	 * @return titon\core\Application
	 * @throws titon\core\CoreException
	 * @chainable
	 */
	public function setup($module, $path, array $controllers) {
		if (empty($path)) {
			throw new CoreException(sprintf('The path for the %s module is required.', $module));

		} else if (!file_exists($path)) {
			throw new CoreException(sprintf('Module directory does not exist: %s', $path));
		}

		$this->_modules[$module] = [
			'name' => $module,
			'path' => Titon::loader()->ds($path, true),
			'controllers' => $controllers
		];

		$this->_controllers[$module] = $controllers;

		return $this;
	}

}