<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\traits;

use \Closure;

/**
 * The Cacheable trait provides functionality to cache any data from the class layer.
 * All data is unique and represented by a generated cache key.
 *
 * @package	titon.libs.traits
 */
trait Cacheable {

	/**
	 * Cached items indexed by key.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_cache = [];

	/**
	 * Is cache on or off?
	 *
	 * @access private
	 * @var boolean
	 */
	private $__cacheEnabled = true;

	/**
	 * Dynamically read and write from the cache at once. If the cache exists with the key return it, else execute and save the result.
	 * If the value happens to be a closure, evaluate the closure and save the result.
	 *
	 * @access public
	 * @param array|string $key
	 * @param mixed|Closure $value
	 * @return mixed
	 */
	public function cache($key, $value) {
		$key = $this->createCacheKey($key);

		if ($cache = $this->getCache($key)) {
			return $cache;
		}

		if ($value instanceof Closure) {
			$callback = Closure::bind($value, $this, $this);
			$value = $callback();
		}

		if (!$this->__cacheEnabled) {
			return $value;
		}

		return $this->setCache($key, $value);
	}

	/**
	 * Generate a cache key. If an array is passed, drill down and form a key.
	 *
	 * @access public
	 * @param string|array $keys
	 * @return string
	 */
	public function createCacheKey($keys) {
		if (is_array($keys)) {
			$key = array_shift($keys);

			if ($keys) {
				foreach ($keys as $value) {
					if (is_array($value)) {
						$key .= '-' . md5(json_encode($value));
					} else if ($value) {
						$key .= '-' . $value;
					}
				}
			}
		} else {
			$key = $keys;
		}

		return (string) $key;
	}

	/**
	 * Empty the cache.
	 *
	 * @access public
	 * @return \titon\libs\traits\Cacheable
	 * @chainable
	 */
	public function flushCache() {
		$this->_cache = [];

		return $this;
	}

	/**
	 * Return a cached item if it exists, else return null.
	 *
	 * @access public
	 * @param string|array $key
	 * @return mixed
	 */
	public function getCache($key = null) {
		if (!$this->__cacheEnabled) {
			return null;
		}

		if ($key === null) {
			return $this->_cache;
		}

		$key = $this->createCacheKey($key);

		if ($this->hasCache($key)) {
			return $this->_cache[$key];
		}

		return null;
	}

	/**
	 * Check to see if the cache key exists.
	 *
	 * @access public
	 * @param string|array $key
	 * @return boolean
	 */
	public function hasCache($key) {
		return isset($this->_cache[$this->createCacheKey($key)]);
	}

	/**
	 * Remove an item from the cache. Return true if the item was removed.
	 *
	 * @access public
	 * @param string|array $key
	 * @return boolean
	 */
	public function removeCache($key) {
		$key = $this->createCacheKey($key);

		if ($this->hasCache($key)) {
			unset($this->_cache[$key]);

			return true;
		}

		return false;
	}

	/**
	 * Set a value to the cache with the defined key.
	 * This will overwrite any data with the same key.
	 * The value being saved will be returned.
	 *
	 * @access public
	 * @param string|array $key
	 * @param mixed $value
	 * @return mixed
	 */
	public function setCache($key, $value) {
		$this->_cache[$this->createCacheKey($key)] = $value;

		return $value;
	}

	/**
	 * Toggle cache on and off.
	 *
	 * @access public
	 * @param boolean $on
	 * @return \titon\libs\traits\Cacheable
	 * @chainable
	 */
	public function toggleCache($on = true) {
		$this->__cacheEnabled = (bool) $on;

		return $this;
	}

}