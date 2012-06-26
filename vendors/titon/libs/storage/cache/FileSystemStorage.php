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
 * A storage engine that uses the servers local filesystem to store its cached items.
 * This engine can be installed using the Cache::setup() method.
 *
 * {{{
 *		new FileSystemStorage([
 *			'prefix' => 'sql_',
 *			'expires' => '+1 day'
 *		]);
 * }}}
 *
 * A sample configuration can be found above, and the following options are available: prefix, expires.
 *
 * @package	titon.libs.storage.cache
 * @uses	titon\libs\storage\StorageException
 */
class FileSystemStorage extends StorageAbstract {

	/**
	 * Is the cache folder ready for writing?
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_ready = false;

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
	 * Empty the cache. An optional expiration time can be passed to delete older files only.
	 *
	 * @access public
	 * @param mixed $expires
	 * @return boolean
	 */
	public function flush($expires = null) {
		$dir = dir($this->_getPath());

		if ($expires !== null) {
			$expires = $this->expires($expires);
		}

		while (($file = $dir->read()) !== false) {
			if ($file === '.' || $file === '..') {
				continue;
			}

			$path = $this->_getPath() . $file;

			if (file_exists($path)) {
				if ($expires) {
					if (filemtime($path) >= $expires) {
						unlink($path);
					}
				} else {
					unlink($path);
				}
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
			$value = file_get_contents($this->_getPath($key));
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
		return file_exists($this->_getPath($key));
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
	 * Always use serialization with file system caching.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
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
		if ($this->has($key)) {
			return unlink($this->_getPath($key));
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
		$value = $this->expires($expires) . '|' . $this->serialize($value);

		return file_put_contents($this->_getPath($key), $value, LOCK_EX);
	}

	/**
	 * Return the full path to the cache directory.
	 *
	 * @access protected
	 * @param string $key
	 * @return string
	 */
	protected function _getPath($key = null) {
		if (!$this->_ready) {
			$this->_checkPath();
		}

		$path = APP_TEMP . 'cache/' . $this->config->storage . '/';

		if ($key) {
			$path .= $this->key($key) . '.cache';
		}

		return $path;
	}

	/**
	 * Verify the cache directories exist and are writable.
	 *
	 * @access protected
	 * @return void
	 */
	protected function _checkPath() {
		$path = $this->_getPath();

		// Does folder exist?
		if (!file_exists($path)) {
			mkdir($path, 0777, true);

		// Is folder writable?
		} else if (!is_writable($path)) {
			chmod($path, 0777);
		}

		$this->_ready = true;
	}

}