<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\storage\cache;

use titon\Titon;
use titon\libs\storage\StorageAbstract;
use titon\libs\storage\StorageException;

/**
 * A storage engine that uses the Wincache extension for a cache store; available on Windows platforms.
 * This engine can be installed using the Cache::setup() method. No configuration options are available for this engine.
 *
 * @package	titon.libs.storage.cache
 *
 * @link	http://php.net/manual/book.wincache.php
 */
class WincacheStorage extends StorageAbstract {

	/**
	 * Decrement a value within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @param int $step
	 * @return boolean
	 */
	public function decrement($key, $step = 1) {
		return wincache_ucache_dec($this->key($key), $step, true);
	}

	/**
	 * Empty the cache.
	 *
	 * @access public
	 * @return boolean
	 */
	public function flush() {
		return wincache_ucache_clear();
	}

	/**
	 * Get data from the cache if it exists.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		return $this->decode(wincache_ucache_get($this->key($key)));
	}

	/**
	 * Check if the item exists within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return wincache_ucache_exists($this->key($key));
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
		return wincache_ucache_inc($this->key($key), $step, true);
	}

	/**
	 * Validate that Wincache is installed.
	 *
	 * @access public
	 * @return void
	 * @throws StorageException
	 */
	public function initialize() {
		if (!Titon::load('wincache')) {
			throw new StorageException('Wincache extension does not exist.');
		}
	}

	/**
	 * Remove the item if it exists and return true, else return false.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		return wincache_ucache_delete($this->key($key));
	}

	/**
	 * Set data to the cache.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $expires
	 * @return boolean
	 */
	public function set($key, $value, $expires = null) {
		return wincache_ucache_set($this->key($key), $this->encode($value), $this->expires($expires) - time());
	}

}