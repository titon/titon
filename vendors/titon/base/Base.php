<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base;

use titon\Titon;
use titon\base\BaseException;
use titon\libs\augments\ConfigAugment;
use \ReflectionClass;
use \ReflectionMethod;

/**
 * Primary class for all framework classes to extend. All child classes will inherit the $_config property,
 * allowing any configuration settings to be automatically passed and set through the constructor.
 *
 * @package	titon.base
 */
class Base {

	/**
	 * The configuration object.
	 *
	 * @access public
	 * @var titon\libs\augments\ConfigAugment
	 */
	public $config;

	/**
	 * An array of configuration settings for the current class.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array();

	/**
	 * Merges the custom configuration with the defaults.
	 * Trigger initialize method if setting is true.
	 *
	 * @access public
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		$this->config = new ConfigAugment($config, $this->_config + array('initialize' => true));

		if ($this->config->initialize) {
			$this->initialize();
		}
	}

	/**
	 * Serialize the configuration.
	 *
	 * @access public
	 * @return array
	 */
	public function __sleep() {
		return array('_config');
	}

	/**
	 * Reconstruct the class once unserialized.
	 *
	 * @access public
	 * @return void
	 */
	public function __wakeup() {
		if ($this->config->initialize) {
			$this->initialize();
		}
	}

	/**
	 * Magic method for toString().
	 *
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * Primary initialize method that is triggered during instantiation.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		return;
	}

	/**
	 * Return all relevant meta data regarding the current class. This includes file path, namespace package,
	 * class name, constants, methods and properties (with visibility) and much more.
	 *
	 * @access public
	 * @return array
	 */
	public function meta() {
		$callback = function($self) {
			$class = get_class($self);
			$reflection = new ReflectionClass($self);

			return array(
				'filePath' => Titon::loader()->toPath($class),
				'className' => $class,
				'shortClassName' => $reflection->getShortName(),
				'namespace' => $reflection->getNamespaceName(),
				'publicMethods' => $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
				'protectedMethods' => $reflection->getMethods(ReflectionMethod::IS_PROTECTED),
				'privateMethods' => $reflection->getMethods(ReflectionMethod::IS_PRIVATE),
				'staticMethods' => $reflection->getMethods(ReflectionMethod::IS_STATIC),
				'publicProperties' => $reflection->getProperties(ReflectionMethod::IS_PUBLIC),
				'protectedProperties' => $reflection->getProperties(ReflectionMethod::IS_PROTECTED),
				'privateProperties' => $reflection->getProperties(ReflectionMethod::IS_PRIVATE),
				'staticProperties' => $reflection->getProperties(ReflectionMethod::IS_STATIC),
				'constants' => $reflection->getConstants(),
				'interfaces' => $reflection->getInterfaceNames(),
				'traits' => $reflection->getTraitNames(),
				'parent' => $reflection->getParentClass()
			);
		};

		// Use Memoizer if available
		if (method_exists($this, 'cacheMethod')) {
			return $this->cacheMethod(__FUNCTION__, null, $callback);
		}

		return $callback($this);
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
	 * Return the classname when called as a string.
	 *
	 * @access public
	 * @return string
	 */
	public function toString() {
		return get_class($this);
	}

}