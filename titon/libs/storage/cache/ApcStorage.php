<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\storage\cache;

use \titon\libs\storage\StorageAbstract;
use \titon\log\Exception;

/**
 * A storage engine that uses the APC extension for a cache store; requires pecl/apc. 
 * This engine can be installed using the Cache::setup() method. No configuration options are available for this engine.
 *
 * @package	titon.libs.storage.cache
 * @uses	titon\log\Exception
 * 
 * @link	http://pecl.php.net/package/apc
 */
class ApcStorage extends StorageAbstract {
		
	/**
	 * Validate that APC is installed.
	 * 
	 * @access public
	 * @return void
	 */
	public function initialize() {
		if (!extension_loaded('apc')) {
			throw new Exception('APC extension does not exist.');
		}
		
		// Always use serialization with APC
		$this->configure('serialize', true);
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
		$value = apc_fetch($key);
		
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$value[$k] = $this->unserialize($v);
			}
		} else {
			$value = $this->unserialize($value);
		}
		
		return $value;
	}
	
	/**
	 * Check if the item exists within the cache.
	 * 
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return apc_exists($key);
	}
	
	/**
	 * Remove the item if it exists and return true, else return false.
	 * 
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		return apc_delete($key);
	}
	
	/**
	 * Set data to the cache. If serialize is true, the data will be serialized.
	 * 
	 * @access public
	 * @param string $key
	 * @param mixed $value 
	 * @param mixed $expires
	 * @return boolean
	 */
	public function set($key, $value = null, $expires = null) {
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->set($k, $v, $expires);
			}
			
			return true;
			
		} else {
			$expires = ($this->expires($expires) - time()) / 60;
			
			return apc_store($key, $this->serialize($value), $expires);
		}
	}
	
}