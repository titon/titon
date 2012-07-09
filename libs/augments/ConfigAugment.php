<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\augments;

use titon\libs\augments\AugmentException;
use titon\utility\Hash;
use \ArrayAccess;
use \Iterator;

/**
 * An augment that supplies configuration options for primary classes.
 * The augment can take a optional secondary default configuration,
 * which can be used to autobox values anytime a config is written.
 *
 * @package	titon.libs.augments
 * @uses	titon\utility\Hash
 */
class ConfigAugment implements ArrayAccess, Iterator {

	/**
	 * Custom configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [];

	/**
	 * Default configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_defaults = [];

	/**
	 * Apply defaults and merge the custom configuration in.
	 *
	 * @param array $config
	 * @param array $defaults
	 */
	public function __construct(array $config, array $defaults = []) {
		$this->_defaults = $defaults;
		$this->set(array_merge($defaults, $config));
	}

	/**
	 * Magic method for get().
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key) {
		return $this->get($key);
	}

	/**
	 * Magic method for set().
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function __set($key, $value) {
		$this->set($key, $value);
	}

	/**
	 * Magic method for has().
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function __isset($key) {
		return $this->has($key);
	}

	/**
	 * Magic method for remove().
	 *
	 * @access public
	 * @param string $key
	 * @return void
	 */
	public function __unset($key) {
		$this->remove($key);
	}

	/**
	 * Magic method to return all configuration, or to set configuration.
	 *
	 * @access public
	 * @param mixed $config
	 * @return mixed
	 */
	public function __invoke($config = null) {
		if ($config) {
			return $this->set($config);
		} else {
			return $this->get();
		}
	}

	/**
	 * Return a configuration by key, or return all.
	 *
	 * @access public
	 * @param string|null $key
	 * @return mixed
	 * @throws titon\libs\augments\AugmentException
	 */
	public function get($key = null) {
		if (!$key) {
			return $this->_config;

		} else if (isset($this->_config[$key])) {
			return $this->_config[$key];

		} else if ($value = Hash::extract($this->_config, $key)) {
			return $value;
		}

		throw new AugmentException(sprintf('The config key %s does not exist.', $key));
	}

	/**
	 * Set a configuration. If a matching default exists, autobox the current value ot match.
	 *
	 * @access public
	 * @param string|array $key
	 * @param mixed $value
	 * @return titon\libs\augments\ConfigAugment
	 */
	public function set($key, $value = null) {
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->set($k, $v);
			}
		} else {
			if (($default = Hash::extract($this->_defaults, $key)) !== null) {
				if (is_float($default)) {
					$value = (float) $value;
				} else if (is_numeric($default)) {
					$value = (int) $value;
				} else if (is_bool($default)) {
					$value = (bool) $value;
				} else if (is_string($default)) {
					$value = (string) $value;
				}
			}

			$this->_config = Hash::insert($this->_config, $key, $value);
		}

		return $this;
	}

	/**
	 * Check if a configuration exists.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return isset($this->_config[$key]);
	}

	/**
	 * Remove a configuration by key.
	 *
	 * @access public
	 * @param string $key
	 * @return titon\libs\augments\ConfigAugment
	 */
	public function remove($key) {
		unset($this->_config[$key]);

		return $this;
	}

	/**
	 * Alias method for get().
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function offsetGet($key) {
		return $this->get($key);
	}

	/**
	 * Alias method for set().
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet($key, $value) {
		$this->set($key, $value);
	}

	/**
	 * Alias method for has().
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function offsetExists($key) {
		return $this->has($key);
	}

	/**
	 * Alias method for remove().
	 *
	 * @access public
	 * @param string $key
	 * @return void
	 */
	public function offsetUnset($key) {
		$this->remove($key);
	}

	/**
	 * Reset the loop.
	 *
	 * @access public
	 * @return void
	 */
	public function rewind() {
		reset($this->_config);
	}

	/**
	 * Return the current value in the loop.
	 *
	 * @access public
	 * @return mixed
	 */
	public function current() {
		return current($this->_config);
	}

	/**
	 * Reset the current key in the loop.
	 *
	 * @access public
	 * @return mixed
	 */
	public function key() {
		return key($this->_config);
	}

	/**
	 * Go to the next index.
	 *
	 * @access public
	 * @return mixed
	 */
	public function next() {
		return next($this->_config);
	}

	/**
	 * Check if the current index is valid.
	 *
	 * @access public
	 * @return boolean
	 */
	public function valid() {
		return ($this->current() !== false);
	}

}