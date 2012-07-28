<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base;

use titon\base\BaseException;
use \Closure;

/**
 * The Object type allows for the dynamic creation of classes during runtime, with support for properties and methods.
 * This class should not be used as a replacement for standard classes, but as a way to mock up objects when needed.
 *
 * @package	titon.base
 */
class Object {

	/**
	 * Mapping of all methods.
	 *
	 * @access public
	 * @var array
	 */
	protected $_methods;

	/**
	 * Mapping of all properties.
	 *
	 * @access public
	 * @var array
	 */
	protected $_properties;

	/**
	 * Initialize and store properties and methods.
	 *
	 * @access public
	 * @param array $params
	 */
	public function __construct(array $params = []) {
		if (!empty($params)) {
			foreach ($params as $key => $value) {
				if ($value instanceof Closure) {
					$this->addMethod($key, $value);

				} else if (!is_numeric($key)) {
					$this->addProperty($key, $value);
				}
			}
		}
	}

	/**
	 * Magic alias for getMethod().
	 *
	 * @access public
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public function __call($name, $args = []) {
		return $this->getMethod($name, $args);
	}

	/**
	 * Magic alias for getProperty().
	 *
	 * @access public
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {
		return $this->getProperty($name);
	}

	/**
	 * Magic alias for setProperty().
	 *
	 * @access public
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function __set($name, $value = null) {
		$this->setProperty($name, $value);
	}

	/**
	 * Magic alias for hasProperty().
	 *
	 * @access public
	 * @param string $name
	 * @return boolean
	 */
	public function __isset($name) {
		return $this->hasProperty($name);
	}

	/**
	 * Magic alias for removeProperty().
	 *
	 * @access public
	 * @param string $name
	 * @return titon\base\Object
	 * @chainable
	 */
	public function __unset($name) {
		$this->removeProperty($name);
	}

	/**
	 * Add a new method to the class using an anonymous function.
	 *
	 * @access public
	 * @param string $name
	 * @param Closure $method
	 * @return titon\base\Object
	 * @throws titon\base\BaseException
	 * @chainable
	 */
	public function addMethod($name, Closure $method) {
		if (is_numeric($name)) {
			throw new BaseException(sprintf('Method name %s is invalid.', $name));

		} else if (isset($this->_methods[$name])) {
			throw new BaseException(sprintf('Method %s already exists.', $name));
		}

		$this->_methods[$name] = Closure::bind($method, $this, __CLASS__);

		return $this;
	}

	/**
	 * Add a property to the class.
	 *
	 * @access public
	 * @param string $name
	 * @param mixed $value
	 * @return titon\base\Object
	 * @throws titon\base\BaseException
	 * @chainable
	 */
	public function addProperty($name, $value = null) {
		if (is_numeric($name)) {
			throw new BaseException(sprintf('Property name %s is invalid.', $name));

		} else if (isset($this->_properties[$name])) {
			throw new BaseException(sprintf('Property %s already exists.', $name));
		}

		$this->_properties[$name] = $value;

		return $this;
	}

	/**
	 * Execute the method and return the result.
	 *
	 * @access public
	 * @param string $name
	 * @param array $args
	 * @throws titon\base\BaseException
	 * @return mixed
	 */
	public function getMethod($name, $args = []) {
		if ($this->hasMethod($name)) {
			if (!is_array($args)) {
				$args = [$args];
			}

			return call_user_func_array($this->_methods[$name], $args);
		}

		throw new BaseException(sprintf('Method %s does not exist.', $name));
	}

	/**
	 * Get a property value.
	 *
	 * @access public
	 * @param string $name
	 * @return mixed
	 * @throws titon\base\BaseException
	 */
	public function getProperty($name) {
		if ($this->hasProperty($name)) {
			return $this->_properties[$name];
		}

		throw new BaseException(sprintf('Property %s does not exist.', $name));
	}

	/**
	 * Check if a method exists.
	 *
	 * @access public
	 * @param string $name
	 * @return boolean
	 */
	public function hasMethod($name) {
		return isset($this->_methods[$name]);
	}

	/**
	 * Check if a property exists.
	 *
	 * @access public
	 * @param string $name
	 * @return boolean
	 */
	public function hasProperty($name) {
		return isset($this->_properties[$name]);
	}

	/**
	 * Remove a method.
	 *
	 * @access public
	 * @param string $name
	 * @return titon\base\Object
	 * @chainable
	 */
	public function removeMethod($name) {
		unset($this->_methods[$name]);

		return $this;
	}

	/**
	 * Remove a property.
	 *
	 * @access public
	 * @param string $name
	 * @return titon\base\Object
	 * @chainable
	 */
	public function removeProperty($name) {
		unset($this->_properties[$name]);

		return $this;
	}

	/**
	 * Overwrite an existing method, or add a new one.
	 *
	 * @access public
	 * @param string $name
	 * @param Closure $method
	 * @return titon\base\Object
	 * @chainable
	 */
	public function setMethod($name, Closure $method) {
		if ($this->hasMethod($name)) {
			$this->_methods[$name] = Closure::bind($method, $this, __CLASS__);

		} else {
			$this->addMethod($name, $method);
		}

		return $this;
	}

	/**
	 * Set a properties value. If the property does not exist, add it.
	 *
	 * @access public
	 * @param string $name
	 * @param mixed $value
	 * @return titon\base\Object
	 * @chainable
	 */
	public function setProperty($name, $value) {
		if ($this->hasProperty($name)) {
			$this->_properties[$name] = $value;

		} else {
			$this->addProperty($name, $value);
		}

		return $this;
	}

}