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
 * A storage engine that uses the APC extension for a cache store; requires pecl/apc.
 * This engine can be installed using the Cache::setup() method. No configuration options are available for this engine.
 *
 * @package	titon.libs.storage.cache
 *
 * @link	http://pecl.php.net/package/apc
 */
class ApcStorage extends StorageAbstract {

	/**
	 * Decrement a value within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @param int $step
	 * @return boolean
	 */
	public function decrement($key, $step = 1) {
		return apc_dec($this->key($key), $step);
	}

	/**
	 * Empty the cache.
	 *
	 * @access public
	 * @return boolean
	 */
	public function flush() {
		return apc_clear_cache('user');
	}

	/**
	 * Get data from the cache if it exists.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		return $this->decode(apc_fetch($this->key($key)));
	}

	/**
	 * Check if the item exists within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return apc_exists($this->key($key));
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
		return apc_inc($this->key($key), $step);
	}

	/**
	 * Validate that APC is installed.
	 *
	 * @access public
	 * @return void
	 * @throws \titon\libs\storage\StorageException
	 */
	public function initialize() {
		if (!Titon::load('apc')) {
			throw new StorageException('APC extension does not exist.');
		}

		// Always use serialization with APC
		$this->config->serialize = true;
	}

	/**
	 * Remove the item if it exists and return true, else return false.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		return apc_delete($this->key($key));
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
		return apc_store($this->key($key), $this->encode($value), $this->expires($expires) - time());
	}

}