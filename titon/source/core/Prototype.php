<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

use \titon\log\Exception;
use \titon\utility\Inflector;
use \titon\utility\Set;
use \Closure;

/**
* The Prototype class is the base for all classes that need dependency or functionality from other classes.
* It allows you to attach classes to the parent class, while encapsulating the attaching class in a Closure,
* enabling the objects to only be instantiated when triggered; also known as, lazy loading.
*
* Additionally, all child classes will inherit the $_config property, allowing any configuration settings to be
* automatically passed and set through the constructor (which is done dynamically through the Registry and App classes).
*
* @package	titon.source.core
*/
class Prototype {

	/**
	 * Classes and their options / namespaces to load for dependencies.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_classes = array();

	/**
	 * An array of configuration settings for the current parent class.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array('initialized' => false);

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
	 * Merges the custom configuration with the defaults. Parses the $_classes and builds up the options,
	 * but does not instantiate the object until called. Finally executes construct().
	 *
	 * @access public
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config = array()) {
		if (!empty($config)) {
			$this->_config = $config + $this->_config;
		}

		if (!empty($this->_classes)) {
			foreach ($this->_classes as $class => $options) {
				if (is_string($options)) {
					$options = array('source' => $options);
				}

				if (empty($options['alias'])) {
					$options['alias'] = is_string($class) ? $class : $app->loader->baseClass($options['source']);
				}

				$this->attachObject($options);
			}
		}

		$this->construct();
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
	 * Serialize the classes and configuration.
	 *
	 * @access public
	 * @return array
	 */
	public function __sleep() {
		return array('_classes', '_config');
	}

	/**
	 * Reconstruct the class once unserialized.
	 *
	 * @access public
	 * @return void
	 */
	public function __wakeup() {
		$this->construct();
	}

	/**
	 * Magic method for Prototype::toString().
	 *
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * Allow an object to be usable if it has been restricted. Must supply the classname.
	 *
	 * @access public
	 * @param string|array $classes
	 * @return void
	 * @final
	 */
	final public function allowObject($classes) {
		if (!is_array($classes)) {
			$classes = array($classes);
		}

		foreach ($classes as $class) {
			unset($this->_restricted[$class]);
		}
	}

	/**
	 * Attaches the defined closure object to the $__objectMap, as well as saving its options to $_classes.
	 *
	 * @access public
	 * @param string|array $options
	 * @param Closure $object
	 * @return void
	 * @final
	 */
	final public function attachObject($options, Closure $object = null) {
		if (is_string($options)) {
			$options = array('alias' => $options);
		}

		$options = $options + array(
			'alias' => null,
			'source' => null,
			'persist' => false,
			'callback' => true
		);

		if (empty($options['alias'])) {
			throw new Exception('You must define an alias to reference the passed object.');
		} else {
			$options['alias'] = Inflector::variable($options['alias']);
		}

		$this->_classes[$options['alias']] = $options;
		
		if ($object !== null && $object instanceof Closure) {
			$this->__objectMap[$options['alias']] = $object;
		}
	}

	/**
	 * Update the configuration during runtime. Can also be configured using Registry::configure() where applicable.
	 *
	 * @see Registry::configure()
	 * @access public
	 * @param array $config
	 * @return void
	 * @final
	 */
	final public function configure(array $config = array()) {
		$this->_config = $config + $this->_config;
	}

	/**
	 * Defines all attachments or dependencies and is executed during __construct().
	 *
	 * @access public
	 * @return void
	 */
	public function construct() {
		return;
	}

	/**
	 * Remove an object permanently from the $_loaded, $_classes and $__objectMap properties.
	 *
	 * @access public
	 * @param string $class
	 * @param boolean $deleteMap
	 * @return boolean
	 * @final
	 */
	final public function detachObject($class, $deleteMap = true) {
		if (isset($this->_classes[$class])) {
			unset($this->_classes[$class], $this->_loaded[$class]);

			if ($deleteMap) {
				unset($this->__objectMap[$class]);
			}

			return true;
		}

		return false;
	}

	/**
	 * Return the current configuration (single or all) from the class.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 * @final
	 */
	final public function getConfig($key = null) {
		return Set::extract($this->_config, $key);
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

			$this->_classes[$class]['source'] = $app->loader->toNotation(get_class($object));

		} else if (!empty($options['source'])) {
			$namespace = $app->loader->toNamespace($options['source']);
			$object = new $namespace();
		}

		// Persist
		// @todo persist?
		if ($options['persist']) {
			$this->_loaded[$class] =& $app->registry->store($object);
		} else {
			$this->_loaded[$class] =& $object;
		}

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
	 * A dummy function for no operation.
	 *
	 * @access public
	 * @return void
	 */
	public function noop() {
		return;
	}

	/**
	 * Restrict a class from being used within the current scope, or until the class is allowed again.
	 *
	 * @access public
	 * @param string|array $classes
	 * @return void
	 * @final
	 */
	final public function restrictObject($classes) {
		if (!is_array($classes)) {
			$classes = array($classes);
		}

		foreach ($classes as $class){
			$this->_restricted[$class] = $class;
		}
	}

	/**
	 * Return the object as an array.
	 *
	 * @access public
	 * @return string
	 */
	public function toArray() {
		return Set::toArray($this);
	}

	/**
	 * Return the classname when called as a string.
	 *
	 * @access public
	 * @return string
	 */
	public function toString() {
		return get_class($this);
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
				if ($method == 'initialize' && $this->{$options['alias']}->getConfig('initialized')) {
					continue;
				}

				if ($options['callback'] === true) {
					if (method_exists($this->{$options['alias']}, $method)) {
						$this->{$options['alias']}->{$method}($this);

						if ($method == 'initialize') {
							$this->{$options['alias']}->configure(array('initialized' => true));
						}
					}
				}
			}
		}
	}

}