<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\library\readers;

use \titon\source\system\Object;
use \titon\source\library\readers\ReaderInterface;

/**
 * Interface for all Config Readers.
 *
 * @package titon.source.core.readers
 */
abstract class ReaderAbstract extends Object implements ReaderInterface {

	/**
	 * File type extension.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_extension;

	/**
	 * Path to the configuration file.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_path;

	/**
	 * Return the file type extension for the reader.
	 *
	 * @access public
	 * @return string
	 * @final
	 */
	final public function extension() {
		return $this->_extension;
	}

	/**
	 * Set the path to the file.
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 * @final
	 */
	final public function setPath($path) {
		$this->_path = $path;
	}

	/**
	 * The reader must return the loaded config file as an array.
	 *
	 * @access public
	 * @return array
	 */
	public function toArray() {
		return $this->_config;
	}

}