<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */
 
namespace titon\libs\bundles;

use \titon\base\Base;
use \titon\libs\bundles\Bundle;
use \titon\libs\bundles\BundleException;
use \titon\utility\Inflector;

/**
 * @todo
 *
 * @package	titon.libs.bundles
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
	 * List of all filenames within the resource bundle.
	 *
	 * @access public
	 * @return array
	 */
	public function files() {
		return $this->cacheMethod(__FUNCTION__, null, function($self) {
			return array_map('basename', glob($self->path() . '*.' . $self->config('ext')));
		});
	}

	/**
	 * Attempt to find the resource bundle within the resource locations.
	 *
	 * @access public
	 * @return void
	 * @throws \titon\libs\bundles\BundleException
	 */
	public function initialize() {
		$config = $this->config();

		$locations = array_map(function($value) use ($config) {
			foreach ($config as $key => $val) {
				$value = str_replace('{' . $key . '}', $val, $value);
			}

			return $value;
		}, $this->locations());

		foreach ($locations as $location) {
			if (file_exists($location)) {
				$this->_path = $location;
				return;
			}
		}

		// Remove so that we can throw a reasonable exception
		unset($config['ext'], $config['initialize']);

		throw new BundleException(sprintf('Resource bundle %s could not be located.', implode(':', $config)));
	}

	/**
	 * Load the file from the resource bundle if it exists and cache the data.
	 * If the file does not exist, return an empty array.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function load($key) {
		if (isset($this->_config[$key])) {
			return $this->_config[$key];
		}

		$filename = Inflector::filename($key, $this->config('ext'), false);

		if (in_array($filename, $this->files())) {
			$data = $this->parse($this->path() . $filename);
		} else {
			$data = array();
		}

		$this->configure($key, $data);

		return $data;
	}

	/**
	 * List of locations to find the resource bundle in.
	 *
	 * @access public
	 * @return array
	 */
	public function locations() {
		return $this->_locations;
	}

	/**
	 * Parse the file at the given path and return the result.
	 *
	 * @access public
	 * @param $path
	 * @return array
	 * @throws \titon\libs\bundles\BundleException
	 */
	public function parse($path) {
		throw new BundleException(sprintf('You must define the parse() method within your %s.', get_class($this)));
	}

	/**
	 * Return the final resource bundle path.
	 *
	 * @access public
	 * @return string
	 */
	public function path() {
		return $this->_path;
	}

}
