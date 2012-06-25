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
use titon\libs\traits\Memoizeable;

/**
 * Abstract class that implements the extension and file detection for Readers.
 *
 * @package	titon.libs.readers
 * @abstract
 */
abstract class ReaderAbstract extends Base implements Reader {
	use Memoizeable;

	/**
	 * Path to the current file to read.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_path;

	/**
	 * Set the path during construction.
	 *
	 * @access public
	 * @param string $path
	 */
	public function __construct($path = null) {
		$this->_path = $path;
	}

	/**
	 * Return the file extension for the reader.
	 *
	 * @access protected
	 * @return mixed
	 */
	public function getExtension() {
		return static::EXT;
	}

	/**
	 * Read the file after checking for existence.
	 *
	 * @access public
	 * @param string $path
	 * @return array
	 * @throws titon\libs\readers\ReaderException
	 */
	public function read($path = null) {
		if ($path) {
			$this->_path = $path;
		} else {
			$path = $this->_path;
		}

		return $this->cacheMethod(array(__METHOD__, $path), function() use ($path) {
			$ext = $this->getExtension();

			if (substr($path, -strlen($ext)) !== $ext) {
				throw new ReaderException(sprintf('Reader will only parse %s files.', $ext));
			}

			if (file_exists($path)) {
				$data = $this->parse();

				if (is_array($data)) {
					return $data;
				}
			}

			throw new ReaderException(sprintf('File reader failed to parse %s.', basename($path)));
		});
	}

}