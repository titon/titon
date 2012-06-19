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
use titon\utility\Inflector;

/**
 * @todo
 *
 * @package	titon.libs.bundles
 * @uses	titon\utility\Inflector
 * @abstract
 */
abstract class BundleAbstract extends Base implements Bundle {
	// use Memoizer;

	/**
	 * Resource locations.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_locations = array();

	/**
	 * Resource bundle path.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_path;

	/**
	 * Attempt to find the resource bundle within the resource locations.
	 *
	 * @access public
	 * @param array $locations
	 * @return void
	 * @throws titon\libs\bundles\BundleException
	 */
	public function findFile($filename) {
		foreach ($this->getLocations() as $location) {
			if ($this->hasFile($location, $filename)) {

			}
		}

		throw new BundleException(sprintf('Could not locate %s within any ', implode(':', $config)));
	}

	/**
	 * Return data based on a key. If data does not exist, load it from the file that matches the key.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function get($key) {
		if (isset($this->_config[$key])) {
			return $this->_config[$key];
		}

		$filename = Inflector::filename($key, static::EXT, false);

		$this->_config[$key] = $this->loadFile($filename);

		return $this->_config[$key];
	}

	/**
	 * List of all filenames within the resource bundle.
	 *
	 * @access public
	 * @return array
	 */
	public function getFiles() {
		$ext = static::EXT;

		return $this->cacheMethod(__FUNCTION__, null, function($self) use ($ext) {
			return array_map('basename', glob($self->getPath() . '*.' . $ext));
		});
	}

	/**
	 * List of locations to find the resource bundle in.
	 *
	 * @access public
	 * @return array
	 */
	public function getLocations() {
		return $this->_locations;
	}

	/**
	 * Return the final resource bundle path.
	 *
	 * @access public
	 * @return string
	 */
	public function getPath() {
		return $this->_path;
	}

	/**
	 * Check if the file exists within the bundle.
	 *
	 * @access public
	 * @param string $filename
	 * @return boolean
	 */
	public function hasFile($filename) {
		return in_array($filename, $this->getFiles());
	}

	/**
	 * Load the file from the resource bundle and parse its contents.
	 * If file does not exist, return an empty array.
	 *
	 * @access public
	 * @param string $filename
	 * @return array
	 */
	public function loadFile($filename) {
		if ($this->hasFile($filename)) {
			$data = $this->parseFile($filename);
		} else {
			$data = array();
		}

		return $data;
	}

	/**
	 * Parse the file at the given path and return the result.
	 *
	 * @access public
	 * @param string $filename
	 * @return array
	 * @throws titon\libs\bundles\BundleException
	 */
	public function parseFile($filename) {
		throw new BundleException(sprintf('You must define the parseFile() method within your %s.', get_class($this)));
	}

	/**
	 * Set the folder locations to use for cycling through.
	 *
	 * @access public
	 * @param array $locations
	 * @return void
	 */
	public function setLocations(array $locations) {
		$config = $this->config->get();

		$this->_locations = array_map(function($value) use ($config) {
			foreach ($config as $key => $val) {
				$value = str_replace('{' . $key . '}', $val, $value);
			}

			return $value;
		}, $locations);
	}

}
