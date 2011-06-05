<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use \titon\core\CoreException;
use \titon\libs\readers\ReaderInterface;
use \titon\log\Debugger;
use \titon\utility\Inflector;
use \titon\utility\Set;

/**
 * Stores the current configuration options for the application.
 * Configuration can be loaded from multiple sources including environment, bootstrappings and internal system classes.
 * Various readers can be used to import specific configuration files.
 *
 * @package	titon.core
 * @uses	titon\core\CoreException
 * @uses	titon\log\Debugger
 * @uses	titon\utility\Inflector
 * @uses	titon\utility\Set
 */
class Config {

	/**
	 * Current loaded configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array();

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
		return Set::extract($this->_config, $key);
	}

	/**
	 * Checks to see if a key exists within the current configuration.
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function has($key) {
		return Set::exists($this->_config, $key);
	}

	/**
	 * Loads a user created file into the configuration class.
	 * Uses the defined reader to parse the file.
	 *
	 * @access public
	 * @param string $file
	 * @param ReaderInterface $reader
	 * @return this
	 * @chainable
	 */
	public function load($file, ReaderInterface $reader) {
		$file = Inflector::filename($file, $reader->extension());
		$path = APP_CONFIG .'sets'. DS . $file;

		if (is_file($path)) {
			$reader->setPath($path);
			$reader->read();

			if (!isset($this->_config[$file])) {
				$this->_config[$file] = array();
			}

			$this->_config[$file] = $reader->toArray() + $this->_config[$file];

		} else {
			throw new CoreException(sprintf('Configuration file %s does not exist.', $file));
		}

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
	 * @return this
	 * @chainable
	 */
	public function set($key, $value) {
		if ($key === 'Debug.level') {
			Debugger::enable(((int)$value > 0));
		}

		$this->_config = Set::insert($this->_config, $key, $value);

		return $this;
	}

}
