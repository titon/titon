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
use titon\io\File;
use titon\libs\readers\Reader;
use titon\libs\traits\Cacheable;

/**
 * Abstract class that implements the extension and file detection for Readers.
 *
 * @package	titon.libs.readers
 * @abstract
 */
abstract class ReaderAbstract extends File implements Reader {
	use Cacheable;

	/**
	 * Set the path during construction.
	 *
	 * @access public
	 * @param string $path
	 */
	public function __construct($path = null) {
		if ($path) {
			$this->_path = Titon::loader()->ds(realpath($path));;
		}
	}

	/**
	 * Return the supported file extension for the reader.
	 *
	 * @access public
	 * @return string
	 */
	public function reader() {
		return static::EXT;
	}

	/**
	 * Load the contents of a file after checking for existence.
	 *
	 * @access public
	 * @param string $path
	 * @return array
	 * @throws \titon\libs\readers\ReaderException
	 */
	public function load($path = null) {
		if ($path) {
			$this->_path = Titon::loader()->ds($path);
		} else {
			$path = $this->_path;
		}

		if (!$path) {
			throw new ReaderException(sprintf('Please provide a file path for %s.', get_class($this)));
		}

		return $this->cache([__METHOD__, $path], function() {
			if ($this->ext() !== $this->reader()) {
				throw new ReaderException(sprintf('Reader will only parse %s files.', $this->reader()));
			}

			if ($this->exists()) {
				$data = $this->parse();

				if (is_array($data)) {
					return $data;
				}
			}

			throw new ReaderException(sprintf('File reader failed to parse %s.', $this->name()));
		});
	}

}