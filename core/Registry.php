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
 * The Registry acts a central hub where any part of the application can access a single instance of a stored object.
 * It registers all objects into the class to store in memory and be re-useable later at any given time.
 *
 * @package	titon.core
 */
class Registry {

	/**
	 * Configuration settings that are automatically loaded into classes upon instantiation.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_configs = [];

	/**
	 * Objects that have been registered into memory. The array index is represented by the namespace convention,
	 * where as the array value would be the matching instantiated object.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_registered = [];

	/**
	 * Return a configuration set if it has been set.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function config($key) {
		return isset($this->_configs[$key]) ? $this->_configs[$key] : null;
	}

	/**
	 * Defines an array of configuration that should be loaded into a class when its instantiated.
	 *
	 * @access public
	 * @param string $key
	 * @param array $config
	 * @return \titon\core\Registry
	 * @chainable
	 */
	public function configure($key, array $config = []) {
		if (isset($this->_configs[$key])) {
			$this->_configs[$key] = $config + $this->_configs[$key];
		} else {
			$this->_configs[$key] = $config;
		}

		return $this;
	}

	/**
	 * Register a file into memory and return an instantiated object.
	 *
	 * @access public
	 * @param string $key
	 * @param array $config
	 * @param boolean $store
	 * @return object
	 */
	public function &factory($key, array $config = [], $store = true) {
		if ($this->has($key)) {
			return $this->get($key);
		}

		if ($defaults = $this->config($key)) {
			$config = $config + $defaults;
		}

		$namespace = Titon::loader()->toNamespace($key);
		$object = new $namespace($config);

		if ($store) {
			$this->set($object, $key);
		}

		return $object;
	}

	/**
	 * Flush the registry by removing all stored objects.
	 *
	 * @access public
	 * @return \titon\core\Registry
	 * @chainable
	 */
	public function flush() {
		$this->_configs = [];

		if ($this->_registered) {
			foreach ($this->_registered as $key => $object) {
				unset($this->_registered[$key]);
			}
		}

		return $this;
	}

	/**
	 * Return the object assigned to the given key.
	 *
	 * @access public
	 * @param string $key
	 * @return object
	 * @throws \titon\core\CoreException
	 */
	public function &get($key) {
		if ($this->has($key)) {
			return $this->_registered[$key];
		}

		throw new CoreException(sprintf('Object %s does not exist in the registry.', $key));
	}

	/**
	 * Checks to see if an object has been registered (instantiated).
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return (isset($this->_registered[$key]) && is_object($this->_registered[$key]));
	}

	/**
	 * Returns an array of all objects that have been registered; returns the keys and not the objects.
	 *
	 * @access public
	 * @return array
	 */
	public function listing() {
		return array_keys($this->_registered);
	}

	/**
	 * Remove an object from registry.
	 * Returns true if object exists and was deleted, else returns false.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		if ($this->has($key)) {
			unset($this->_registered[$key]);
			return true;
		}

		return false;
	}

	/**
	 * Store an object into registry.
	 *
	 * @access public
	 * @param object $object
	 * @param string|null $key
	 * @return object
	 * @throws \titon\core\CoreException
	 */
	public function set($object, $key = null) {
		if (!is_object($object)) {
			throw new CoreException('The object to register must be instantiated.');
		}

		if (!$key) {
			$key = get_class($object);
		}

		$this->_registered[$key] = $object;

		return $object;
	}

}
