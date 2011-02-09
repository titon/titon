<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\system;

use \titon\source\Titon;
use \titon\source\system\Object;
use \titon\source\log\Exception;
use \titon\source\utility\Inflector;
use \titon\source\utility\Set;
use \Closure;

/**
 * The Prototype class is the base for all classes that need dependency or functionality from other classes.
 * It allows you to attach classes to the parent class, while encapsulating the attaching class in a Closure,
 * enabling the objects to only be instantiated when triggered; also known as, lazy loading.
 *
 * @package	titon.source.system
 * @uses	titon\source\Titon
 * @uses	titon\source\log\Exception
 * @uses	titon\source\utility\Inflector
 * @uses	titon\source\utility\Set
 */
class Prototype extends Object {

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
	 * Parses the $_classes and attaches any defined classes.
	 *
	 * @access public
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config = array()) {
		if (!empty($this->_classes)) {
			foreach ($this->_classes as $class => $options) {
				if (is_string($options)) {
					$options = array('source' => $options);
				}

				if (empty($options['alias'])) {
					$options['alias'] = is_string($class) ? $class : Titon::loader()->baseClass($options['source']);
				}

				$this->attachObject($options);
			}
		}

		parent::__construct($config);
	}

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
	 * @return boolean
	 * @final
	 */
	final public function __unset($class) {
		return $this->detachObject($class);
	}

	/**
	 * Allow an object to be usable if it has been restricted. Must supply the classname.
	 *
	 * @access public
	 * @param string|array $classes
	 * @return this
	 * @chainable
	 * @final
	 */
	final public function allowObject($classes) {
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
	 * @param Closure $object
	 * @return this
	 * @chainable
	 * @final
	 */
	final public function attachObject($options, Closure $object = null) {
		if (is_string($options)) {
			$options = array('alias' => $options);
		}

		$options = $options + array(
			'alias' => null,
			'source' => null,
			'persist' => true,
			'callback' => true
		);

		if (empty($options['alias'])) {
			throw new Exception('You must define an alias to reference the attached object.');
		} else {
			$options['alias'] = Inflector::variable($options['alias']);
		}

		$this->_classes[$options['alias']] = $options;
		
		if ($object !== null && $object instanceof Closure) {
			$this->__objectMap[$options['alias']] = $object;
		}

		return $this;
	}

	/**
	 * Remove an object permanently from the $_loaded, $_classes and $__objectMap properties.
	 *
	 * @access public
	 * @param string $class
	 * @param boolean $deleteMap
	 * @return this
	 * @chainable
	 * @final
	 */
	final public function detachObject($class, $deleteMap = true) {
		if (isset($this->_classes[$class])) {
			unset($this->_classes[$class], $this->_loaded[$class]);

			if ($deleteMap) {
				unset($this->__objectMap[$class]);
			}
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
	 * @final
	 */
	final public function getObject($class) {
		if (in_array($class, $this->_restricted)) {
			return;

		} else if (isset($this->_loaded[$class])) {
			return $this->_loaded[$class];

		} else if (!isset($this->_classes[$class])) {
			throw new Exception(sprintf('No class configuration could be found for %s.', $class));
		}

		// Gather options
		$options = $this->_classes[$class];

		// Load the object
		if (isset($this->__objectMap[$class])) {
			$object = $this->__objectMap[$class]();

			$this->_classes[$class]['source'] = get_class($object);

		// Create manually
		} else {
			// Persist in registry
			if ($options['persist']) {
				$object = Titon::registry()->factory($options['source']);
			} else {
				$object = new $options['source']();
			}
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
	 * @final
	 */
	final public function hasObject($class) {
		return (isset($this->_loaded[$class]) || isset($this->__objectMap[$class]));
	}

	/**
	 * Restrict a class from being used within the current scope, or until the class is allowed again.
	 *
	 * @access public
	 * @param string|array $classes
	 * @return this
	 * @chainable
	 * @final
	 */
	final public function restrictObject($classes) {
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
	 * @final
	 */
	final public function triggerCallback($method) {
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