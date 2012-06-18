<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\readers;

use titon\Titon;
use titon\base\Base;
use titon\libs\readers\Reader;
use titon\utility\Inflector;

/**
 * Abstract class that implements the extension detection for Readers.
 *
 * @package	titon.libs.readers
 * @abstract
 */
abstract class ReaderAbstract extends Base implements Reader {

	/**
	 * Formatted filename.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_filename;

	/**
	 * Path of the containing folder.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_path;

	/**
	 * Set the path when instantiating. Check if the file extension exists based on the constant.
	 *
	 * @access public
	 * @param $filename
	 */
	public function __construct($filename) {
		$this->_filename = Inflector::filename($filename, static::EXT, false);
	}

	/**
	 * Check to see if the file exists.
	 *
	 * @access public
	 * @return boolean
	 */
	public function fileExists() {
		return file_exists($this->getPath() . $this->getFilename());
	}

	/**
	 * Return the formatted filename.
	 *
	 * @access public
	 * @return string
	 */
	public function getFilename() {
		return $this->_filename;
	}

	/**
	 * Return the folder location.
	 *
	 * @access public
	 * @return string
	 */
	public function getPath() {
		return $this->_path;
	}

	/**
	 * Set the folder location of the file.
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 */
	public function setPath($path) {
		if (substr($path, -1) != '/') {
			$path .= '/';
		}

		$this->_path = Titon::loader()->ds($path);
	}
	
}