<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\bundles;

use titon\base\Base;
use titon\libs\bundles\Bundle;
use titon\libs\bundles\BundleException;
use titon\libs\readers\Reader;
use titon\libs\traits\Memoizer;
use \DirectoryIterator;

/**
 * @todo
 *
 * @package	titon.libs.bundles
 * @abstract
 */
abstract class BundleAbstract extends Base implements Bundle {
	use Memoizer;

	/**
	 * Resource locations.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_locations = array();

	/**
	 * File readers.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_readers = array();

	/**
	 * Add a file reader to use.
	 *
	 * @access public
	 * @param titon\libs\readers\Reader $reader
	 * @return titon\libs\bundles\Bundle
	 */
	public function addReader(Reader $reader) {
		$this->_readers[$reader->getExtension()] = $reader;

		return $this;
	}

	public function getLocations() {
		return $this->_locations;
	}

	public function getReaders() {
		return $this->_readers;
	}

	public function loadResource($resource) {
		if (empty($this->_readers)) {
			throw new BundleException('A reader must be loaded to read bundle resources.');
		}

		return $this->cacheMethod(__FUNCTION__, $resource, function($self) use ($resource) {
			$contents = array();

			foreach ($self->getLocations() as $location) {
				foreach ($self->getReaders() as $reader) {
					$reader->setPath($location)->setFilename($resource);

					if ($reader->fileExists()) {
						$contents = array_merge($contents, $reader->readFile());
					}
				}
			}

			return $contents;
		});
	}

	/**
	 * Set the folder locations to use for cycling through.
	 *
	 * @access public
	 * @param array $locations
	 * @return titon\libs\bundles\Bundle
	 */
	public function setLocations(array $locations) {
		$config = $this->config->get();

		foreach ($locations as $location) {
			foreach ($config as $key => $value) {
				$location = str_replace('{' . $key . '}', $value, $location);
			}

			$this->_locations[] = $location;
		}

		return $this;
	}

}
