<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\readers;

use \titon\base\Prototype;
use \titon\libs\readers\Reader;

/**
 * Abstract class that implements the extension detection for Readers.
 *
 * @package	titon.libs.readers
 * @abstract
 */
abstract class ReaderAbstract extends Prototype implements Reader {

	/**
	 * File type extension.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_extension;

	/**
	 * Return the file type extension for the reader.
	 *
	 * @access public
	 * @return string
	 * @final
	 */
	final public function extension() {
		return trim($this->_extension, '.');
	}
	
}