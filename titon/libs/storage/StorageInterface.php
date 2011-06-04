<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\storage;

/**
 * Primary interface for all storage containers.
 *
 * @package	titon.libs.storage
 */
interface StorageInterface {
	
	/**
	 * Empty the cache.
	 * 
	 * @access public
	 * @return boolean
	 */
	public function flush();
	
	/**
	 * Get data from the cache if it exists. If serialize is true, the data will be unserialized.
	 * 
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key);
	
	/**
	 * Check if the item exists within the cache.
	 * 
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key);
	
	/**
	 * Remove the item if it exists and return true, else return false.
	 * 
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key);
	
	/**
	 * Set data to the cache. If serialize is true, the data will be serialized.
	 * 
	 * @access public
	 * @param string $key
	 * @param mixed $value 
	 * @return boolean
	 */
	public function set($key, $value);
	
}