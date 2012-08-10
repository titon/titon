<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\io;

use titon\Titon;
use titon\io\Folder;

/**
 * Provides an object interface for interacting with a file on the file system.
 * Encapsulates methods for opening, reading, writing, deleting, copying, etc.
 *
 * @package	titon.io
 */
class File extends Folder {

	/**
	 * Resource handle.
	 *
	 * @access protected
	 * @var resource
	 */
	protected $_handle;

	/**
	 * Current read / write mode.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_mode;

	/**
	 * Initialize the folder path. If the folder doesn't exist, create it.
	 *
	 * @access public
	 * @param string $path
	 * @param boolean $create
	 * @param int $mode
	 */
	public function __construct($path, $create = false, $mode = 0755) {
		if (file_exists($path) && !is_file($path)) {
			throw new IoException(sprintf('Invalid file path %s, folders are not allowed.', $path));
		}

		$this->_path = Titon::loader()->ds(realpath($path));

		if ($create) {
			$this->create($mode);
		}
	}

	/**
	 * Close the current file resource handler when object is destroyed.
	 *
	 * @access public
	 * @return boolean
	 */
	public function __destruct() {
		$this->close();
	}

	/**
	 * Append data to the end of a file.
	 *
	 * @access public
	 * @param string $data
	 * @return boolean
	 */
	public function append($data) {
		return $this->write($data, 'a');
	}

	/**
	 * Close the current file resource handler.
	 *
	 * @access public
	 * @return boolean
	 */
	public function close() {
		if (is_resource($this->_handle)) {
			$this->unlock();

			return fclose($this->_handle);
		}

		return false;
	}

	/**
	 * Copies the file to the target location and return a new File object.
	 *
	 * @access public
	 * @param string $target
	 * @param boolean $overwrite
	 * @return titon\io\File
	 * @throws titon\io\IoException
	 */
	public function copy($target, $overwrite = true) {
		if (!$this->exists()) {
			return false;
		}

		if (file_exists($target) && !$overwrite) {
			throw new IoException('Cannot copy file as the target already exists.');
		}

		if (copy($this->_path, $target)) {
			return new File($target);
		}

		return null;
	}

	/**
	 * Create the file if it doesn't exist.
	 *
	 * @access public
	 * @param int $mode
	 * @return boolean
	 */
	public function create($mode = 0755) {
		if (!$this->folder()->exists()) {
			$this->folder()->create();
		}

		if (!$this->exists() && $this->folder()->writable()) {
			if (touch($this->_path)) {
				if ($mode) {
					$this->chmod($mode);
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Remove the file if it exists.
	 *
	 * @access public
	 * @return boolean
	 */
	public function delete() {
		clearstatcache();

		if ($this->exists()) {
			$this->close();

			return unlink($this->_path);
		}

		return false;
	}

	/**
	 * Return the file extension.
	 *
	 * @access public
	 * @return string
	 */
	public function ext() {
		if ($ext = pathinfo($this->_path, PATHINFO_EXTENSION)) {
			return $ext;
		}

		return Titon::loader()->ext($this->_path);
	}

	/**
	 * Disable find().
	 *
	 * @access public
	 * @param string $pattern
	 * @return void
	 * @throws titon\io\IoException
	 * @final
	 */
	final public function find($pattern) {
		throw new IoException(sprintf('%s is disabled.', __METHOD__));
	}

	/**
	 * Lock a file for reading or writing.
	 *
	 * @access public
	 * @param int $mode
	 * @return boolean
	 */
	public function lock($mode = LOCK_SH) {
		if (is_resource($this->_handle)) {
			return flock($this->_handle, $mode);
		}

		return false;
	}

	/**
	 * Return an MD5 checksum of the file.
	 *
	 * @access public
	 * @param boolean $raw
	 * @return string
	 */
	public function md5($raw = false) {
		return md5_file($this->_path, $raw);
	}

	/**
	 * Return the mime type for the file.
	 *
	 * @access public
	 * @return string
	 */
	public function mimeType() {
		if (!$this->exists()) {
			return null;
		}

		$f = finfo_open(FILEINFO_MIME);

		list($type, $charset) = explode(';', finfo_file($f, $this->_path));

		finfo_close($f);

		return $type;
	}

	/**
	 * Open a file resource handler for reading and writing.
	 *
	 * @access public
	 * @param string $mode
	 * @return boolean
	 */
	public function open($mode) {
		if (is_resource($this->_handle)) {
			if ($mode === $this->_mode) {
				return true;
			} else {
				$this->close();
			}
		}

		clearstatcache();

		$this->_handle = fopen($this->_path, $mode);
		$this->_mode = $mode;

		return is_resource($this->_handle);
	}

	/**
	 * Prepend data to the beginning of a file.
	 *
	 * @access public
	 * @param string $data
	 * @return boolean
	 */
	public function prepend($data) {
		return $this->write($data, 'c');
	}

	/**
	 * Open a file for reading. If $length is provided, will only read up to that limit.
	 *
	 * @access public
	 * @param int $length
	 * @param string $mode
	 * @return string
	 */
	public function read($length = null, $mode = 'rb') {
		if (!$this->open($mode)) {
			return null;
		}

		if ($this->lock()) {
			$content = fread($this->_handle, $length ?: $this->size());

			$this->close();

			return $content;
		}

		return null;
	}

	/**
	 * Unlock a file for reading or writing.
	 *
	 * @access public
	 * @return boolean
	 */
	public function unlock() {
		if (is_resource($this->_handle)) {
			return flock($this->_handle, LOCK_UN);
		}

		return false;
	}

	/**
	 * Write data to a file (will erase any previous contents).
	 *
	 * @access public
	 * @param string $data
	 * @param string $mode
	 * @return boolean
	 */
	public function write($data, $mode = 'w') {
		if (!$this->open($mode)) {
			return false;
		}

		if ($this->lock(LOCK_EX)) {
			$result = fwrite($this->_handle, $data);

			$this->unlock();

			return (bool) $result;
		}

		return false;
	}

}