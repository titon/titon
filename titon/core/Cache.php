<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use \titon\core\CoreException;
use \titon\libs\storage\Storage;

/**
 * Provides a very basic interface for caching individual sets of data. Multiple storage engines can be setup 
 * to support different caching mechanisms: Memcache, APC, XCache, Memory, FileSystem.
 *
 * @package	titon.core
 * @uses	titon\core\CoreException
 */
class Cache {
	
	/**
	 * Storage engines.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_storage = array();
	
	/**
	 * Decrement a value within the cache.
	 * 
	 * @access public
	 * @param string $key
	 * @param int $step
	 * @param string $storage
	 * @return boolean
	 */
	public function decrement($key, $step = 1, $storage = 'default') {
		return $this->storage($storage)->decrement($key, $step);
	}
		
	/**
	 * Empty the cache.
	 * 
	 * @access public
	 * @param string $storage
	 * @return boolean
	 */
	public function flush($storage = null) {
		if ($storage) {
			return $this->storage($storage)->flush();
		} else {
			foreach ($this->_storage as $storage) {
				$storage->flush();
			}
		}
		
		return true;
	}

	/**
	 * Get data from the storage engine defined by the key.
	 * 
	 * @access public
	 * @param string $key
	 * @param string $storage
	 * @return mixed
	 */
	public function get($key, $storage = 'default') {
		return $this->storage($storage)->get($key);
	}
	
	/**
	 * Check to see if the cached item is within storage.
	 * 
	 * @access public
	 * @param string $key
	 * @param string $storage
	 * @return boolean
	 */
	public function has($key, $storage = 'default') {
		return $this->storage($storage)->has($key);
	}
	
	/**
	 * Increment a value within the cache.
	 * 
	 * @access public
	 * @param string $key
	 * @param int $step
	 * @param string $storage
	 * @return boolean
	 */
	public function increment($key, $step = 1, $storage = 'default') {
		return $this->storage($storage)->increment($key, $step);
	}
	
	/**
	 * Remove the item if it exists and return true, else return false.
	 * 
	 * @access public
	 * @param string $key
	 * @param string $storage
	 * @return boolean
	 */
	public function remove($key, $storage = 'default') {
		return $this->storage($storage)->remove($key);
	}
	
	/**
	 * Set data to the defined storage engine.
	 * 
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $expires
	 * @param string $storage
	 * @return boolean
	 */
	public function set($key, $value, $expires = null, $storage = 'default') {
		return $this->storage($storage)->set($key, $value, $expires);
	}
	
	/**
	 * Add a new storage engine to the cache system.
	 * 
	 * @access public
	 * @param string $name
	 * @param Storage $storage 
	 * @return Cache
	 * @chainable
	 */
	public function setup($name, Storage $storage) {
		$storage->configure('storage', $name)->initialize();
		
		$this->_storage[$name] = $storage;
		
		return $this;
	}
	
	/**
	 * Retrieve the storage engine if it exists.
	 * 
	 * @access public
	 * @param string $name
	 * @return Storage
	 */
	public function storage($name) {
		if (isset($this->_storage[$name])) {
			return $this->_storage[$name];
		}
		
		throw new CoreException(sprintf('Cache storage engine %s does not exist.', $name));
	}
	
}