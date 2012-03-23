<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\readers;

use \titon\base\Base;
use \titon\libs\readers\Reader;

/**
 * Abstract class that implements the extension detection for Readers.
 *
 * @package	titon.libs.readers
 * @abstract
 */
abstract class ReaderAbstract extends Base implements Reader {

	/**
	 * Path to file.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_path;

	/**
	 * Set the path when instantiating. Check if the file extension exists based on the constant.
	 *
	 * @access public
	 * @param $path
	 */
	public function __construct($path) {
		$ext = '.' . static::EXT;

		if (substr($path, -strlen($ext)) != $ext) {
			$path .= $ext;
		}

		$this->_path = APP_CONFIG . 'sets/' . $path;
	}

	/**
	 * Check to see if the file exists.
	 *
	 * @access public
	 * @return boolean
	 */
	public function fileExists() {
		return file_exists($this->getPath());
	}

	/**
	 * Returns the final path.
	 *
	 * @access public
	 * @return string
	 */
	public function getPath() {
		return $this->_path;
	}
	
}