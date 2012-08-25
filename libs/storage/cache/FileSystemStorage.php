<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\storage\cache;

use titon\io\File;
use titon\io\Folder;
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
 */
class FileSystemStorage extends StorageAbstract {

	/**
	 * List of cache File objects.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_files;

	/**
	 * Folder object for the cache folder.
	 *
	 * @access protected
	 * @var \titon\io\Folder
	 */
	protected $_folder;

	/**
	 * File object for the groups mapping.
	 *
	 * @access protected
	 * @var \titon\io\File
	 */
	protected $_groups;

	/**
	 * File object for the expires mapping.
	 *
	 * @access protected
	 * @var \titon\io\File
	 */
	protected $_times;

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
			return $this->set($key, ($value - $step));
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
		if ($expires !== null) {
			$expires = $this->expires($expires);
		}

		$files = $this->_folder->read('files');

		foreach ($files as $file) {
			if ($expires) {
				if ($file->modifiedTime() >= $expires) {
					$file->delete();
				}
			} else {
				$file->delete();
			}
		}

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
			return $this->load($key)->read();

			/*$value = $this->load($key)->read();
			$pipe = mb_strpos($value, '|');
			$timestamp = mb_substr($value, 0, $pipe);

			if ($timestamp >= time()) {
				return $this->decode(mb_substr($value, ($pipe + 1), mb_strlen($value)));
			} else {
				$this->remove($key);
			}*/
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
		return file_exists($this->_folder->path() . $this->key($key) . '.cache');
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
			return $this->set($key, ($value + $step));
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

		$path = APP_TEMP . 'cache/' . $this->config->storage . '/';

		$this->_folder = new Folder($path, true);
		$this->_groups = new File($path . '.groups', true);
		$this->_times = new File($path . '.times', true);
	}

	/**
	 * Return the File object for the cache entry.
	 *
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function load($key = null) {
		if ($this->has($key)) {
			return $this->_files[$key];
		}

		$path = $this->_folder->path() . $this->key($key) . '.cache';

		$this->_files[$key] = new File($path, true);

		return $this->_files[$key];
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
			$this->load($key)->delete();

			unset($this->_files[$key]);

			return true;
		}

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
		return $this->load($key)->write($this->encode($value), 'w', true);
	}

}