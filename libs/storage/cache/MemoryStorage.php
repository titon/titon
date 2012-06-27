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
	 * Decrement a value within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @param int $step
	 * @return boolean
	 */
	public function decrement($key, $step = 1) {
		if ($data = $this->getCache($key)) {
			$this->setCache($key, ($data - (int) $step));
		} else {
			$this->setCache($key, (0 - (int) $step));
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
		return $this->flushCache();
	}

	/**
	 * Get data from the cache if it exists.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		if ($value = $this->getCache($key)) {
			return $this->unserialize($value);
		}

		return null;
	}

	/**
	 * Check if the item exists within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return (bool) $this->getCache($key);
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
		if ($data = $this->getCache($key)) {
			$this->setCache($key, ($data + (int) $step));
		} else {
			$this->setCache($key, (0 + (int) $step));
		}

		return $this;
	}

	/**
	 * Remove the item if it exists and return true, else return false.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		return $this->removeCache($key);
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
		$this->setCache($key, $this->serialize($value));

		return $this;
	}

}