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
	 * Get data from the cache if it exists. If serialize is true, the data will be unserialized.
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
	 * Set data to the cache. If serialize is true, the data will be serialized.
	 * 
	 * @access public
	 * @param string|array $key
	 * @param mixed $value 
	 * @param mixed $expires
	 * @return boolean
	 */
	public function set($key, $value = null, $expires = null) {
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->set($k, $v);
			}
		} else {
			$this->_cache[$key] = $this->serialize($value);
		}
		
		return true;
	}
	
}