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
 * The Registry acts a central hub where any part of the application can access a single instance of a stored object.
 * It registers all objects into the class to store in memory and be re-useable later at any given time.
 *
 * @package	titon.source.core
 * @uses	Exception
 */
class Registry {

	/**
	 * Configuration settings that are automatically loaded into classes upon instantiation.
	 *
	 * @access private
	 * @var array
	 */
	private $__configs = array();

	/**
	 * Objects that have been registered into memory. The array index is represented by the namespace convention,
	 * where as the array value would be the matching instantiated object.
	 *
	 * @access private
	 * @var array
	 */
	private $__registered = array();

	/**
	 * Checks to see if an object has been registered (instantiated).
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function check($key) {
		return (isset($this->__registered[$key]) && is_object($this->__registered[$key]));
	}

	/**
	 * Defines an array of configuration that should be loaded into a class when its instantiated.
	 *
	 * @access public
	 * @param string $key
	 * @param array $config
	 * @return this
	 * @chainable
	 */
	public function configure($key, array $config = array()) {
		if (isset($this->__configs[$key])) {
			$this->__configs[$key] = $config + $this->__configs[$key];
		} else {
			$this->__configs[$key] = $config;
		}

		return $this;
	}

	/**
	 * Delete an object from registry.
	 * Returns true if object exists and was deleted, else returns false.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function delete($key) {
		if (isset($this->__registered[$key])) {
			unset($this->__registered[$key]);
			return true;
		}

		return false;
	}

	/**
	 * Register a file into memory and return an instantiated object.
	 *
	 * @access public
	 * @param string $key
	 * @param array $config
	 * @return object
	 */
	public function factory($key, array $config = array()) {
		if (isset($this->__registered[$key])) {
			return $this->__registered[$key];
		}

		$namespace = $app->loader->toNamespace($key);

		if (!class_exists($namespace)) {
			$app->loader->import($key);
		}

		if (class_exists($namespace)) {
			if (isset($this->__configs[$key])) {
				$config = $config + $this->__configs[$key];
			}

			return $this->store(new $namespace($config));
		}

		throw new Exception(sprintf('Class %s could not be instantiated into the registry.', $key));
	}

	/**
	 * Flush the registry by removing all stored objects.
	 *
	 * @access public
	 * @return this
	 * @chainable
	 */
	public function flush() {
		$this->__configs = array();

		foreach ($this->__registered as $key => $object) {
			unset($this->__registered[$key]);
		}

		return $this;
	}

	/**
	 * Returns an array of all objects that have been registered; returns the keys and not the objects.
	 *
	 * @access public
	 * @return array
	 */
	public function listing() {
		return array_keys($this->__registered);
	}

	/**
	 * Store an object into registry.
	 *
	 * @access public
	 * @param object $object
	 * @param string $key
	 * @return object
	 */
	public function store($object, $key = null) {
		if (!is_object($object)) {
			throw new Exception('The object passed must be instantiated.');
		}

		if (!$key) {
			$key = get_class($object);
		}

		$this->__registered[$key] = $object;

		return $object;
	}

}
