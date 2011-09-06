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
	 * Default setup.
	 * 
	 * @access private
	 * @var array
	 */
	private $__defaults = array(
		'path' => '',
		'controllers' => array()
	);

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
	 * Return the module.
	 * 
	 * @access public
	 * @param string $module
	 * @return array
	 */
	public function module($module) {
		return $this->_modules[$module];
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
	 * Add a module to the application for fast lookup.
	 *
	 * @access public
	 * @param string $module
	 * @param array $setup
	 * @return Application
	 * @chainable
	 */
	public function setup($module, array $setup = array()) {
		if (empty($setup['path'])) {
			throw new CoreException(sprintf('The path for the %s module is required.', $module));
		}
		
		$setup = $setup + $this->__defaults;
		$setup['name'] = $module;
		
		$this->_modules[$module] = $setup;
		$this->_controllers[$module] = $setup['controllers'];
		
		return $this;
	}

}