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
