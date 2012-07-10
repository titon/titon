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
use titon\libs\storage\StorageException;

/**
 * A storage engine that uses the Xcache extension for a cache store.
 * This engine can be installed using the Cache::setup() method.
 *
 *		new XcacheStorage(array(
 *			'username' => 'admin',
 *			'password' => md5('pass')
 *		));
 *
 * A sample configuration can be found above, and the following options are available:
 * serialize, compress, username/password (for flush()), expires.
 *
 * @package	titon.libs.storage.cache
 * @link	http://xcache.lighttpd.net/
 */
class XcacheStorage extends StorageAbstract {

	/**
	 * Decrement a value within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @param int $step
	 * @return boolean
	 */
	public function decrement($key, $step = 1) {
		return xcache_dec($this->key($key), (int) $step);
	}

	/**
	 * Empty the cache.
	 *
	 * @access public
	 * @return boolean
	 */
	public function flush() {
		$backup = [
			'PHP_AUTH_USER' => !empty($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '',
			'PHP_AUTH_PW' => !empty($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '',
		];

		$auth = [
			'PHP_AUTH_USER' => $this->config->username,
			'PHP_AUTH_PW' => $this->config->password
		];

		// Set auth
		$_SERVER = $auth + $_SERVER;

		// Clear cache
		$count = xcache_count(XC_TYPE_VAR);

		for ($i = 0; $i < $count; $i++) {
			xcache_clear_cache(XC_TYPE_VAR, $i);
		}

		// Reset auth
		$_SERVER = $backup + $_SERVER;

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
		return $this->unserialize(xcache_get($this->key($key)));
	}

	/**
	 * Check if the item exists within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return xcache_isset($this->key($key));
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
		return xcache_inc($this->key($key), (int) $step);
	}

	/**
	 * Validate that APC is installed.
	 *
	 * @access public
	 * @return void
	 * @throws StorageException
	 */
	public function initialize() {
		if (!extension_loaded('xcache')) {
			throw new StorageException('Xcache extension does not exist.');
		}

		if ($this->config->compress) {
			ini_set('xcache.optimizer', true);
		}

		ini_set('xcache.admin.user', $this->config->username);
		ini_set('xcache.admin.pass', $this->config->password);
	}

	/**
	 * Remove the item if it exists and return true, else return false.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		return xcache_unset($this->key($key));
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
		$expires = ($this->expires($expires) - time()) / 60;

		return xcache_set($this->key($key), $this->serialize($value), $expires);
	}

}