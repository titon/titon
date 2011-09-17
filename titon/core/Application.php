<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use \titon\Titon;

/**
 * This class manages the location and installation of controllers and modules,
 * to speed up the lookup process of its sub-classes.
 *
 * @package	titon.core
 * @uses	titon\Titon
 */
class Application {
	
	/**
	 * List of controllers per module.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_controllers = array();

	/**
	 * List of modules.
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
		return isset($this->_controllers[$module]) ? $this->_controllers[$module] : $this->_controllers;
	}

	/**
	 * Bootstrap the application.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		foreach (scandir(APP_MODULES) as $module) {
			$path = APP_MODULES . $module . DS . 'bootstrap.php';
			
			if (file_exists($path)) {
				include_once $path;
			}
		}
	}
	
	/**
	 * Return a list of modules or a single module.
	 *
	 * @access public
	 * @param string $module
	 * @return array
	 */
	public function modules($module = null) {
		return isset($this->_modules[$module]) ? $this->_modules[$module] : $this->_modules;
	}

	/**
	 * Add a module to the application for fast lookup.
	 *
	 * @access public
	 * @param string $module
	 * @param string $path
	 * @param array $controllers
	 * @return Application
	 * @throws CoreException
	 * @chainable
	 */
	public function setup($module, $path, array $controllers) {
		if (empty($path)) {
			throw new CoreException(sprintf('The path for the %s module is required.', $module));
		}
		
		$path = Titon::loader()->ds($path);
		
		if (substr($path, -1) != DS) {
			$path .= DS;
		}
		
		$this->_modules[$module] = array(
			'name' => $module,
			'path' => $path,
			'controllers' => $controllers
		);
		
		$this->_controllers[$module] = $controllers;
		
		return $this;
	}

}