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
use \titon\libs\storage\StorageException;

/**
 * A storage engine that uses the servers local filesystem to store its cached items.
 * This engine can be installed using the Cache::setup() method.
 * 
 *		new FileSystemStorage(array(
 *			'prefix' => 'sql_',
 *			'expires' => '+1 day'
 *		));
 * 
 * A sample configuration can be found above, and the following options are available: prefix, expires.
 *
 * @package	titon.libs.storage.cache
 * @uses	titon\libs\storage\StorageException
 */
class FileSystemStorage extends StorageAbstract {
	
	/**
	 * Decrement a value within the cache.
	 * 
	 * @access public
	 * @param string $key
	 * @param int $step
	 * @return boolean
	 */
	public function decrement($key, $step = 1) {
		if ($value = $this->get($key)) {
			return $this->set($key, ((int) $value - (int) $step));
		}
		
		return false;
	}
	
	/**
	 * Empty the cache.
	 * 
	 * @access public
	 * @return boolean
	 */
	public function flush() {
		$dir = dir($this->_path());

		while (($file = $dir->read()) !== false) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			
			$path = $this->_path() . $file;
			
			if (file_exists($path)) {
				unlink($path);
			}
		}
		
		$dir->close();
		
		clearstatcache();
	}
	
	/**
	 * Get data from the cache if it exists.
	 * 
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		if ($this->has($key)) {
			$value = file_get_contents($this->_path($key));
			$pipe = strpos($value, '|');
			$timestamp = substr($value, 0, $pipe);

			if ($timestamp >= time()) {
				return $this->unserialize(substr($value, ($pipe + 1), strlen($value)));
			} else {
				$this->remove($key);
			}
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
		return file_exists($this->_path($key));
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
		if ($value = $this->get($key)) {
			return $this->set($key, ((int) $value + (int) $step));
		}
		
		return false;
	}
	
	/**
	 * Always use serialization with file system caching. Verify the cache directories exist and are writable.
	 * 
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->configure('serialize', true);
		
		$path = $this->_path();
		
		// Does folder exist?
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
			
		// Is folder writable?
		} else if (!is_writable($path)) {
			chmod($path, 0777);
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
		if ($this->has($key)) {
			return unlink($this->_path($key));
		}
		
		clearstatcache();
		
		return false;
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
		$value = $this->expires($expires) ."|". $this->serialize($value);

		return file_put_contents($this->_path($key), $value, LOCK_EX);
	}
	
	/**
	 * Return the full path to the cache directory.
	 * 
	 * @access protected
	 * @param string $key
	 * @return string
	 */
	protected function _path($key = null) {
		$path = APP_TEMP .'cache'. DS . $this->config('storage') . DS;

		if ($key) {
			$path .= $this->key($key);
		}
		
		return $path;
	}
	
}