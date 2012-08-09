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
use titon\io\File;
use \RegexIterator;
use \DirectoryIterator;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use \Exception;

/**
 * Provides an object interface for interacting with a folder on the file system.
 *
 * @package	titon.io
 */
class Folder {

	/**
	 * Parent folder.
	 *
	 * @access protected
	 * @var titon\io\Folder
	 */
	protected $_folder;

	/**
	 * Current path.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_path;

	/**
	 * Initialize the folder path. If the folder doesn't exist, create it.
	 *
	 * @access public
	 * @param string $path
	 * @param boolean $create
	 * @param int $mode
	 */
	public function __construct($path, $create = true, $mode = 0755) {
		if (file_exists($path) && !is_dir($path)) {
			throw new IoException(sprintf('Invalid folder path %s, files are not allowed.', $path));
		}

		$this->_path = Titon::loader()->ds(realpath($path));

		if ($create) {
			$this->create($mode);
		}
	}

	/**
	 * Return the last access time.
	 *
	 * @access public
	 * @return int|null
	 */
	public function accessTime() {
		if ($this->exists()) {
			return fileatime($this->_path);
		}

		return null;
	}

	/**
	 * Return the last inode change time.
	 *
	 * @access public
	 * @return int|null
	 */
	public function changeTime() {
		if ($this->exists()) {
			return filectime($this->_path);
		}

		return null;
	}

	/**
	 * Change the group of the file.
	 *
	 * @access public
	 * @param string $group
	 * @param boolean $recursive
	 * @return boolean
	 */
	public function chgrp($group, $recursive = false) {
		if ($this->exists()) {
			if ($recursive && $this instanceof Folder) {
				$contents = $this->read();

				if ($contents['all']) {
					foreach ($contents['all'] as $file) {
						$file->chgrp($group, $recursive);
					}
				}
			}

			return chgrp($this->_path, $group);
		}

		return false;
	}

	/**
	 * Change the permissions mode of the file.
	 *
	 * @access public
	 * @param int $mode
	 * @param boolean $recursive
	 * @return boolean
	 */
	public function chmod($mode, $recursive = false) {
		if ($this->exists()) {
			if ($recursive && $this instanceof Folder) {
				$contents = $this->read();

				if ($contents['all']) {
					foreach ($contents['all'] as $file) {
						$file->chmod($mode, $recursive);
					}
				}
			}

			return chmod($this->_path, $mode);
		}

		return false;
	}

	/**
	 * Change the owner of the file.
	 *
	 * @access public
	 * @param string $user
	 * @param boolean $recursive
	 * @return boolean
	 */
	public function chown($user, $recursive = false) {
		if ($this->exists()) {
			if ($recursive && $this instanceof Folder) {
				$contents = $this->read();

				if ($contents['all']) {
					foreach ($contents['all'] as $file) {
						$file->chown($user, $recursive);
					}
				}
			}

			return chown($this->_path, $user);
		}

		return false;
	}

	/**
	 * Create the file if it doesn't exist.
	 *
	 * @access public
	 * @param int $mode
	 * @return boolean
	 */
	public function create($mode = 0755) {
		if (!$this->exists()) {
			return mkdir($this->_path, $mode, true);
		}

		return false;
	}

	/**
	 * Remove the folder if it exists. Delete any contents recursively before hand.
	 *
	 * @access public
	 * @return boolean
	 */
	public function delete() {
		clearstatcache();

		if ($this->exists()) {
			try {
				$directory = new RecursiveDirectoryIterator($this->_path, RecursiveDirectoryIterator::CURRENT_AS_SELF);
				$iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);
			} catch (Exception $e) {
				return false;
			}

			foreach ($iterator as $file) {
				if ($file->isDot()) {
					continue;

				} else if ($file->isDir()) {
					@rmdir($file->getPathname());

				} else {
					@unlink($file->getPathname());
				}
			}

			return rmdir($this->_path);
		}

		return false;
	}

	/**
	 * Is the file executable.
	 *
	 * @access public
	 * @return boolean
	 */
	public function executable() {
		return is_executable($this->_path);
	}

	/**
	 * Check if the file exists.
	 *
	 * @access public
	 * @return boolean
	 */
	public function exists() {
		return file_exists($this->_path);
	}

	/**
	 * Find all files and folders within the current folder that match a specific pattern.
	 *
	 * @access public
	 * @param string $pattern
	 * @return array
	 */
	public function find($pattern = '*') {
		if ($this->exists()) {
			try {
				$iterator = new RegexIterator(new DirectoryIterator($this->_path), $pattern, RegexIterator::GET_MATCH);
			} catch (Exception $e) {
				return null;
			}

			$contents = [];

			foreach ($iterator as $file) {
				if ($file->isDot()) {
					continue;

				} else if ($file->isDir()) {
					$contents[] = new Folder($file->getPathname());

				} else if ($file->isFile()) {
					$contents[] = new File($file->getPathname());
				}
			}

			return $contents;
		}

		return null;
	}

	/**
	 * Return the parent folder as a Folder object.
	 *
	 * @access public
	 * @return titon\io\Folder
	 */
	public function folder() {
		if (!$this->_folder) {
			$folder = dirname($this->_path);

			if ($folder !== '.' && $folder !== '/') {
				$this->_folder = new Folder($folder);
			}
		}

		return $this->_folder;
	}

	/**
	 * Return the group name for the file.
	 *
	 * @access public
	 * @return int
	 */
	public function group() {
		if ($this->exists()) {
			return filegroup($this->_path);
		}

		return null;
	}

	/**
	 * Return true if the current path is absolute.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isAbsolute() {
		return ($this->_path[0] === '/' || $this->isWindows());
	}

	/**
	 * Return true if the current path is relative.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isRelative() {
		return !$this->isAbsolute();
	}

	/**
	 * Return true if the current path is a Windows path.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isWindows() {
		return (preg_match('/^[A-Z]:/i', $this->_path) || substr($this->_path, 0, 2) == '//');
	}

	/**
	 * Return the last modified time.
	 *
	 * @access public
	 * @return int|null
	 */
	public function modifiedTime() {
		if ($this->exists()) {
			return filemtime($this->_path);
		}

		return null;
	}

	/**
	 * Return the file name.
	 *
	 * @access public
	 * @return string
	 */
	public function name() {
		return basename($this->_path);
	}

	/**
	 * Return the owner name for the file.
	 *
	 * @access public
	 * @return int
	 */
	public function owner() {
		if ($this->exists()) {
			return fileowner($this->_path);
		}

		return null;
	}

	/**
	 * Return the permissions for the file.
	 *
	 * @access public
	 * @return int
	 */
	public function permissions() {
		if ($this->exists()) {
			return substr(sprintf('%o', fileperms($this->_path)), -4);
		}

		return null;
	}

	/**
	 * Return the current path (print working directory).
	 *
	 * @access public
	 * @return string
	 */
	public function pwd() {
		return $this->_path;
	}

	/**
	 * Scan the folder and return an array of File and Folder objects.
	 *
	 * @access public
	 * @return array
	 */
	public function read() {
		if ($this->exists()) {
			try {
				$iterator = new DirectoryIterator($this->_path);
			} catch (Exception $e) {
				return null;
			}

			$files = [];
			$folders = [];
			$all = [];
			$count = 0;

			foreach ($iterator as $file) {
				if ($file->isDot()) {
					continue;

				} else if ($file->isDir()) {
					$object = new Folder($file->getPathname());
					$folders[] = $object;

				} else if ($file->isFile()) {
					$object = new File($file->getPathname());
					$files[] = $object;
				}

				if (isset($object)) {
					$all[] = $object;
					$count++;
				}
			}

			unset($iterator);

			return [
				'all' => $all,
				'folders' => $folders,
				'files' => $files,
				'count' => $count
			];
		}

		return null;
	}

	/**
	 * Is the file readable.
	 *
	 * @access public
	 * @return boolean
	 */
	public function readable() {
		return is_readable($this->_path);
	}

	/**
	 * Return the current file size.
	 *
	 * @access public
	 * @return int
	 */
	public function size() {
		if ($this->exists()) {
			return filesize($this->_path);
		}

		return null;
	}

	/**
	 * Is the file writable.
	 *
	 * @access public
	 * @return boolean
	 */
	public function writable() {
		return is_writable($this->_path);
	}

}