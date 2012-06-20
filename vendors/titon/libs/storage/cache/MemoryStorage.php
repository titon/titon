<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\storage\cache;

use titon\libs\storage\StorageAbstract;

/**
 * A lightweight caching engine that stores data in memory for the duration of the HTTP request.
 *
 * @package	titon.libs.storage.cache
 */
class MemoryStorage extends StorageAbstract {

	/**
	 * A container for all the cached items.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_cache = array();

	/**
	 * Decrement a value within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @param int $step
	 * @return boolean
	 */
	public function decrement($key, $step = 1) {
		if ($this->has($key)) {
			$this->set($key, ($this->get($key) - (int) $step));
		} else {
			$this->set($key, (0 - (int) $step));
		}

		return true;
	}

	/**
	 * Empty the cache.
	 *
	 * @access public
	 * @return boolean
	 */
	public function flush() {
		$this->_cache = array();

		return true;
	}

	/**
	 * Get data from the cache if it exists.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		$value = $this->has($key) ? $this->_cache[$key] : null;

		return $this->unserialize($value);
	}

	/**
	 * Check if the item exists within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return isset($this->_cache[$key]);
	}

	/**
	 * Increment a value within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @param int $step
	 * @return boolean
	 */
	public function increment($key, $step = 1) {
		if ($this->has($key)) {
			$this->set($key, ($this->get($key) + (int) $step));
		} else {
			$this->set($key, (0 + (int) $step));
		}

		return true;
	}

	/**
	 * Remove the item if it exists and return true, else return false.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		if ($this->has($key)) {
			unset($this->_cache[$key]);

			return true;
		}

		return false;
	}

	/**
	 * Set data to the cache.
	 *
	 * @access public
	 * @param string|array $key
	 * @param mixed $value
	 * @param mixed $expires
	 * @return boolean
	 */
	public function set($key, $value, $expires = null) {
		$this->_cache[$key] = $this->serialize($value);

		return true;
	}

}