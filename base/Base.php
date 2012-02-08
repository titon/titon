<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base;

use \titon\utility\Set;
use \Closure;

/**
 * Primary class for all framework classes to extend. All child classes will inherit the $_config property,
 * allowing any configuration settings to be automatically passed and set through the constructor.
 *
 * @package	titon.base
 */
class Base {

	/**
	 * An array of configuration settings for the current parent class.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array('initialize' => true);

	/**
	 * Merges the custom configuration with the defaults.
	 * Trigger initialize method if setting is true.
	 *
	 * @access public
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config = array()) {
		$this->configure($config);
		
		if (!isset($this->_config['initialize'])) {
			$this->_config['initialize'] = true;
		}

		if ($this->config('initialize')) {
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
		if ($this->config('initialize')) {
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
	 * Return the current configuration.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 * @final
	 */
	final public function config($key = null) {
		return Set::extract($this->_config, $key);
	}

	/**
	 * Update the configuration during runtime.
	 *
	 * @access public
	 * @param string|array $key
	 * @param mixed $value
	 * @return Base
	 * @chainable
	 * @final
	 */
	final public function configure($key, $value = null) {
		if (is_array($key)) {
			$this->_config = $key + $this->_config;
		} else {
			$this->_config = Set::insert($this->_config, $key, $value);
		}

		return $this;
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