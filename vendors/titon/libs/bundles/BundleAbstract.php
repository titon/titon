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

/**
 * @todo
 *
 * @package	titon.libs.bundles
 * @abstract
 */
abstract class BundleAbstract extends Base implements Bundle {
	// use Memoizer;

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'folder' => '',
		'ext' => 'php'
	);

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
			return array_map('basename', glob($self->path() . '*'));
		});
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
	 * Return the final resource bundle path.
	 *
	 * @access public
	 * @return string
	 */
	public function path() {
		return $this->_path;
	}

	/**
	 * Attempt to find the resource bundle within the resource locations.
	 *
	 * @access public
	 * @return void
	 * @throws \titon\libs\bundles\g11n\BundleException
	 */
	public function initialize() {
		$folder = $this->config('folder');

		if (empty($folder)) {
			$folder = '[empty]';
		} else {
			foreach ($this->locations() as $location) {
				$path = $location . $folder . '/';

				if (file_exists($path)) {
					$this->_path = $path;
					return;
				}
			}
		}

		throw new BundleException(sprintf('Resource bundle %s could not be located.', $folder));
	}

	/**
	 * Load the file from the resource bundle if it exists and cache the data.
	 * If the file does not exist, return an empty array.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 * @throws \titon\libs\bundles\g11n\BundleException
	 */
	public function load($key) {
		throw new BundleException(sprintf('You must define the load() method within your %s Bundle.', get_class($this)));
	}

}
