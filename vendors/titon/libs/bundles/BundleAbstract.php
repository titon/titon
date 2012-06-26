<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\bundles;

use titon\Titon;
use titon\base\Base;
use titon\libs\bundles\Bundle;
use titon\libs\bundles\BundleException;
use titon\libs\readers\Reader;
use titon\libs\traits\Cacheable;
use titon\utility\Inflector;

/**
 * Abstract class that handles the loading of Readers and file locations.
 * The bundle can then search for a resource by name by cycling through each location
 * and parsing out the files contents.
 *
 * @package	titon.libs.bundles
 * @abstract
 */
abstract class BundleAbstract extends Base implements Bundle {
	use Cacheable;

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
	 * Add a folder location to use during the lookup cycle.
	 *
	 * @access public
	 * @param array|string $locations
	 * @return titon\libs\bundles\Bundle
	 */
	public function addLocation($locations) {
		if (is_array($locations)) {
			foreach ($locations as $location) {
				$this->addLocation($location);
			}
		} else {
			foreach ($this->config->get() as $key => $value) {
				$locations = str_replace('{' . $key . '}', $value, $locations);
			}

			$this->_locations[] = Titon::loader()->ds($locations, true);
		}

		return $this;
	}

	/**
	 * Add a file reader to use for resource parsing.
	 *
	 * @access public
	 * @param titon\libs\readers\Reader $reader
	 * @return titon\libs\bundles\Bundle
	 */
	public function addReader(Reader $reader) {
		$this->_readers[$reader->getExtension()] = $reader;

		return $this;
	}

	/**
	 * Return all defined locations.
	 *
	 * @access public
	 * @return array
	 */
	public function getLocations() {
		return $this->_locations;
	}

	/**
	 * Return all loaded Readers.
	 *
	 * @access public
	 * @return array
	 */
	public function getReaders() {
		return $this->_readers;
	}

	/**
	 * Parse the contents of every file that matches the resource name.
	 * Begin by looping through all resource locations and all Readers.
	 * If a resource is found that matches the name and a loaded Reader extension,
	 * parse the file and merge its contents with the previous resource of the same name.
	 *
	 * @access public
	 * @param string $resource
	 * @return array
	 * @throws titon\libs\bundles\BundleException
	 */
	public function loadResource($resource) {
		if (empty($this->_readers)) {
			throw new BundleException('A Reader must be loaded to read Bundle resources.');

		} else if ($cache = $this->getCache(array(__METHOD__, $resource))) {
			return $cache;
		}

		$contents = array();

		foreach ($this->getLocations() as $location) {
			foreach ($this->getReaders() as $ext => $reader) {
				$path = $location . Inflector::filename($resource, $ext, false);

				if (file_exists($path)) {
					if ($data = $reader->read($path)) {
						$contents = array_merge($contents, $data);
					}
				}
			}
		}

		return $this->setCache(array(__METHOD__, $resource), $contents);
	}

}
