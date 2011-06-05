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
use \titon\libs\storage\StorageInterface;

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
	 * Set data to the defined storage engine.
	 * 
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @param string $storage
	 * @return boolean
	 */
	public function set($key, $value, $storage = 'default') {
		return $this->storage($storage)->set($key, $value);
	}
	
	/**
	 * Add a new storage engine to the cache system.
	 * 
	 * @access public
	 * @param string $name
	 * @param StorageInterface $storage 
	 * @return void
	 */
	public function setup($name, StorageInterface $storage) {
		$this->_storage[$name] = $storage;
	}
	
	/**
	 * Retrieve the storage engine if it exists.
	 * 
	 * @access public
	 * @param string $name
	 * @return StorageInterface
	 */
	public function storage($name) {
		if (isset($this->_storage[$name])) {
			return $this->_storage[$name];
		}
		
		throw new CoreException(sprintf('Cache storage engine %s does not exist.', $name));
	}
	
}