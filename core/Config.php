<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use titon\Titon;
use titon\core\CoreException;
use titon\libs\readers\Reader;
use titon\utility\Inflector;
use titon\utility\Hash;

/**
 * Stores the current configuration options for the application.
 * Configuration can be loaded from multiple sources including environment, bootstrappings and internal system classes.
 * Various readers can be used to import specific configuration files.
 *
 * @package	titon.core
 * @uses	titon\core\CoreException
 * @uses	titon\utility\Inflector
 * @uses	titon\utility\Hash
 */
class Config {

	/**
	 * Current loaded configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [];

	/**
	 * Get the currently defined encoding for the application.
	 *
	 * @access public
	 * @return string
	 */
	public function encoding() {
		return $this->get('App.encoding') ?: 'UTF-8';
	}

	/**
	 * Grab a value from the current configuration.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key = null) {
		return Hash::get($this->_config, $key);
	}

	/**
	 * Checks to see if a key exists within the current configuration.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return Hash::has($this->_config, $key);
	}

	/**
	 * Loads a user created file into the configuration class.
	 * Uses the defined reader to parse the file.
	 *
	 * @access public
	 * @param string $key
	 * @param titon\libs\readers\Reader $reader
	 * @return titon\core\Config
	 * @throws titon\core\CoreException
	 * @chainable
	 */
	public function load($key, Reader $reader) {
		$path = APP_CONFIG . 'sets/' . Inflector::filename($key, $reader->getExtension(), false);

		$this->_config[$key] = $reader->read($path);

		return $this;
	}

	/**
	 * Grabs the defined project name.
	 *
	 * @access public
	 * @return string
	 */
	public function name() {
		return $this->get('App.name');
	}

	/**
	 * Get the currently defined salt for the application.
	 *
	 * @access public
	 * @return string
	 */
	public function salt() {
		return $this->get('App.salt');
	}

	/**
	 * Add values to the current loaded configuration.
	 * If debug is being set, apply the error reporting rules.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return titon\core\Config
	 * @chainable
	 */
	public function set($key, $value = null) {
		if ($key === 'Debug.level') {
			Titon::debugger()->enable((int) $value > 0);
		}

		$this->_config = Hash::set($this->_config, $key, $value);

		return $this;
	}

}
