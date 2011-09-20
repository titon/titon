<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base;

use \titon\Titon;
use \titon\base\Base;
use \titon\base\BaseException;
use \titon\utility\Inflector;
use \titon\utility\Set;
use \Closure;

/**
 * The Prototype class is the base for all classes that need dependency or functionality from other classes.
 * It allows you to attach classes to the parent class, while encapsulating the attaching class in a Closure,
 * enabling the objects to only be instantiated when triggered; also known as, lazy loading.
 *
 * @package	titon.base
 * @uses	titon\Titon
 * @uses	titon\base\BaseException
 * @uses	titon\utility\Inflector
 * @uses	titon\utility\Set
 */
class Prototype extends Base {

	/**
	 * Classes and their options / namespaces to load for dependencies.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_classes = array();

	/**
	 * Classes that have been instantiated when called using getObject().
	 *
	 * @access protected
	 * @var array
	 */
	protected $_loaded = array();

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
	 * Magic method for Prototype::getObject().
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
	 * Magic method for Prototype::getObject().
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
	 * Magic method for Prototype::hasObject().
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
	 * Magic method for Prototype::detachObject().
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
	 * @return Prototype
	 * @chainable
	 */
	public function allowObject($classes) {
		if (!is_array($classes)) {
			$classes = array($classes);
		}

		foreach ($classes as $class) {
			unset($this->_restricted[$class]);
		}

		return $this;
	}

	/**
	 * Attaches the defined closure object to the $__objectMap, as well as saving its options to $_classes.
	 *
	 * @access public
	 * @param string|array $options
	 * @param Closure $closure
	 * @return Prototype
	 * @throws BaseException
	 * @chainable
	 */
	public function attachObject($options, Closure $closure = null) {
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
			throw new BaseException('You must define an alias to reference the attached object.');
		} else {
			$options['alias'] = Inflector::variable($options['alias']);
		}

		$this->_classes[$options['alias']] = $options;

		if ($closure !== null && $closure instanceof Closure) {
			$this->__objectMap[$options['alias']] = $closure;
		}

		return $this;
	}

	/**
	 * Remove an object permanently from the $_loaded, $_classes and $__objectMap properties.
	 *
	 * @access public
	 * @param string $class
	 * @return Prototype
	 * @chainable
	 */
	public function detachObject($class) {
		if (isset($this->_classes[$class])) {
			unset($this->_classes[$class], $this->_loaded[$class], $this->__objectMap[$class]);
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
	 * @throws BaseException
	 */
	public function getObject($class) {
		if (in_array($class, $this->_restricted)) {
			return;

		} else if (isset($this->_loaded[$class])) {
			return $this->_loaded[$class];

		} else if (!isset($this->_classes[$class])) {
			throw new BaseException(sprintf('No class configuration could be found for %s.', $class));
		}

		// Gather options
		$options = $this->_classes[$class];

		// Load the object
		if (isset($this->__objectMap[$class])) {
			$object = $this->__objectMap[$class]($this);

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
			throw new BaseException(sprintf('%s does not implement the %s interface.', get_class($object), $options['interface']));
		}

		$this->_loaded[$class] =& $object;

		return $this->_loaded[$class];
	}

	/**
	 * Checks to see if a class has been loaded, or is present in the object map.
	 *
	 * @access public
	 * @param string $class
	 * @return boolean
	 */
	public function hasObject($class) {
		return (isset($this->_loaded[$class]) || isset($this->__objectMap[$class]));
	}

	/**
	 * Parses the $_classes property and attaches any defined classes.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		if (!empty($this->_classes)) {
			foreach ($this->_classes as $class => $options) {
				if (is_string($options)) {
					$options = array('class' => $options);
				}

				if (empty($options['alias'])) {
					$options['alias'] = is_string($class) ? $class : Titon::loader()->baseClass($options['class']);
				}

				$this->attachObject($options);
			}
		}
	}

	/**
	 * Restrict a class from being used within the current scope, or until the class is allowed again.
	 *
	 * @access public
	 * @param string|array $classes
	 * @return Prototype
	 * @chainable
	 */
	public function restrictObject($classes) {
		if (!is_array($classes)) {
			$classes = array($classes);
		}

		foreach ($classes as $class){
			$this->_restricted[$class] = $class;
		}

		return $this;
	}

	/**
	 * Cycle through all loaded objects and trigger the defined hook method.
	 *
	 * @access public
	 * @param string $method
	 * @return void
	 */
	public function triggerObjects($method) {
		if (is_string($method) && !empty($this->_classes)) {
			foreach ($this->_classes as $class => $options) {
				if ($options['callback']) {
					$object = $this->getObject($options['alias']);

					if (method_exists($object, $method)) {
						$object->{$method}($this);
					}
				}
			}
		}
	}

}