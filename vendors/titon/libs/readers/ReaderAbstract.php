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
use titon\libs\traits\Memoizer;

/**
 * Abstract class that implements the extension and file detection for Readers.
 *
 * @package	titon.libs.readers
 * @abstract
 */
abstract class ReaderAbstract extends Base implements Reader {
	use Memoizer;

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
	 * @param string $filename
	 * @param string $path
	 */
	public function __construct($filename, $path = APP_CONFIG) {
		parent::__construct();

		if (substr($path, -1) !== '/') {
			$path = '/';
		}

		if (strpos($filename, '/') !== false) {
			$paths = explode('/', trim($filename, '/'));

			$filename = array_pop($paths);
			$path = $path . implode('/', $paths) . '/';
		}

		$this->_filename = Inflector::filename($filename, static::EXT, false);
		$this->_path = Titon::loader()->ds($path);
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
	 * Return the filename.
	 *
	 * @access public
	 * @return string
	 */
	public function getFilename() {
		return $this->_filename;
	}

	/**
	 * Return the full file path including filename and extension.
	 *
	 * @access public
	 * @return string
	 */
	public function getFullPath() {
		return $this->getPath() . $this->getFilename();
	}

	/**
	 * Return the file path.
	 *
	 * @access public
	 * @return string
	 */
	public function getPath() {
		return $this->_path;
	}

	/**
	 * Read the file after checking for existence.
	 *
	 * @access public
	 * @return array
	 * @throws titon\libs\readers\ReaderException
	 */
	public function readFile() {
		$ext = static::EXT;

		return $this->cacheMethod(__METHOD__, $ext, function($self) use ($ext) {
			if ($self->fileExists()) {
				$data = $self->parseFile();

				if (is_array($data)) {
					return $data;
				}
			}

			throw new ReaderException(sprintf('File reader failed to parse %s file %s.', $ext, $self->getFilename()));
		});
	}

}