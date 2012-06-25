<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\traits;

use titon\Titon;
use titon\libs\traits\TraitException;
use titon\utility\Inflector;
use \Closure;

/**
 * Attachable is an inheritable trait for all classes that need dependency or functionality from other classes.
 * It allows you to attach classes to the parent class, while encapsulating the attaching class in a Closure,
 * enabling the objects to only be instantiated when triggered; also known as lazy loading.
 *
 * @package	titon.libs.traits
 * @uses	titon\Titon
 * @uses	titon\libs\traits\TraitException
 * @uses	titon\utility\Inflector
 */
trait Attachable {

	/**
	 * Classes and their options / namespaces to load for dependencies.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_classes = array();

	/**
	 * Classes that have been instantiated when called with getObject().
	 *
	 * @access protected
	 * @var array
	 */
	protected $_attached = array();

	/**
	 * Classes that have been loaded, but are unable to be used within the current scope.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_restricted = array();

	/**
	 * Object map that relates a Closure object to a defined class, to allow for easy lazy-loading.
	 *
	 * @access private
	 * @var array
	 */
	private $__objectMap = array();

	/**
	 * Magic method for getObject().
	 *
	 * @access public
	 * @param string $class
	 * @return object
	 * @final
	 */
	final public function __get($class) {
		return $this->getObject($class);
	}

	/**
	 * Magic method for getObject().
	 *
	 * @access public
	 * @param string $class
	 * @param Closure $closure
	 * @return void
	 * @final
	 */
	final public function __set($class, $closure) {
		$this->attachObject($class, $closure);
	}

	/**
	 * Magic method for hasObject().
	 *
	 * @access public
	 * @param string $class
	 * @return boolean
	 * @final
	 */
	final public function __isset($class) {
		return $this->hasObject($class);
	}

	/**
	 * Magic method for detachObject().
	 *
	 * @access public
	 * @param string $class
	 * @return void
	 * @final
	 */
	final public function __unset($class) {
		$this->detachObject($class);
	}

	/**
	 * Allow an object to be usable if it has been restricted. Must supply the classname.
	 *
	 * @access public
	 * @param string|array $classes
	 * @return titon\libs\traits\Attachable
	 * @chainable
	 */
	public function allowObject($classes) {
		foreach ((array) $classes as $class) {
			unset($this->_restricted[$class]);
		}

		return $this;
	}

	/**
	 * Attaches the defined closure object to the $__objectMap, as well as saving its options to $_classes.
	 *
	 * @access public
	 * @param array|string $options
	 * @param Closure $callback
	 * @return titon\libs\traits\Attachable
	 * @throws titon\libs\traits\TraitException
	 * @chainable
	 */
	public function attachObject($options, Closure $callback = null) {
		if (is_string($options)) {
			$options = array('alias' => $options);
		}

		$options = $options + array(
			'alias' => null,
			'class' => null,
			'register' => true,
			'callback' => true,
			'interface' => null
		);

		if (empty($options['alias'])) {
			throw new TraitException('You must define an alias to reference the attached object.');
		} else {
			$options['alias'] = Inflector::variable($options['alias']);
		}

		$this->_classes[$options['alias']] = $options;

		if ($callback !== null && $callback instanceof Closure) {
			$this->__objectMap[$options['alias']] = Closure::bind($callback, $this, __CLASS__);
		}

		return $this;
	}

	/**
	 * Remove an object permanently from the $_attached, $_classes and $__objectMap properties.
	 *
	 * @access public
	 * @param string $class
	 * @return titon\libs\traits\Attachable
	 * @chainable
	 */
	public function detachObject($class) {
		if (isset($this->_classes[$class])) {
			unset($this->_classes[$class], $this->_attached[$class], $this->__objectMap[$class]);
		}

		return $this;
	}

	/**
	 * Primary method to detect if the object being called can be returned; based on restrictions and instantiation.
	 * If an object is not instantiated, it will create it based off the Closure (if applicable) or the options namespace.
	 *
	 * @access public
	 * @param string $class
	 * @return object|null
	 * @throws titon\libs\traits\TraitException
	 */
	public function getObject($class) {
		if (in_array($class, $this->_restricted)) {
			return null;

		} else if (isset($this->_attached[$class])) {
			return $this->_attached[$class];

		} else if (!isset($this->_classes[$class])) {
			throw new TraitException(sprintf('No class configuration could be found for %s.', $class));
		}

		// Gather options
		$options = $this->_classes[$class];

		// Load the object
		if (isset($this->__objectMap[$class])) {
			$object = $this->__objectMap[$class]();

			$this->_classes[$class]['class'] = get_class($object);

		// Create manually
		} else {
			// Persist in registry
			if ($options['register']) {
				$object = Titon::registry()->factory($options['class']);
			} else {
				$object = new $options['class']();
			}
		}

		if ($options['interface'] && !($object instanceof $options['interface'])) {
			throw new TraitException(sprintf('%s does not implement the %s interface.', get_class($object), $options['interface']));
		}

		$this->_attached[$class] =& $object;

		return $this->_attached[$class];
	}

	/**
	 * Checks to see if a class has been loaded, or is present in the object map.
	 *
	 * @access public
	 * @param string $class
	 * @return boolean
	 */
	public function hasObject($class) {
		return (isset($this->_attached[$class]) || isset($this->__objectMap[$class]));
	}

	/**
	 * Cycle through all loaded objects and trigger the defined hook method.
	 *
	 * @access public
	 * @param string $method
	 * @return titon\libs\traits\Attachable
	 * @chainable
	 */
	public function notifyObjects($method) {
		if (!empty($this->_classes)) {
			foreach ($this->_classes as $options) {
				if ($options['callback']) {
					$object = $this->getObject($options['alias']);

					if (method_exists($object, $method)) {
						$object->{$method}($this);
					}
				}
			}
		}

		return $this;
	}

	/**
	 * Restrict a class from being used within the current scope, or until the class is allowed again.
	 *
	 * @access public
	 * @param string|array $classes
	 * @return titon\libs\traits\Attachable
	 * @chainable
	 */
	public function restrictObject($classes) {
		foreach ((array) $classes as $class){
			$this->_restricted[$class] = $class;
		}

		return $this;
	}

}