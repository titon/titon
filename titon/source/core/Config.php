<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

use \titon\source\core\readers\ReaderInterface;
use \titon\source\log\Debugger;
use \titon\source\log\Exception;
use \titon\source\utility\Inflector;
use \titon\source\utility\Set;

/**
 * Stores the current configuration options for the application.
 * Configuration can be loaded from multiple sources including environment, bootstrappings and internal system classes.
 * Various readers can be used to import specific configuration files.
 *
 * @package	titon.source.core
 * @uses	titon\source\log\Debugger
 * @uses	titon\source\log\Exception
 * @uses	titon\source\utility\Inflector
 * @uses	titon\source\utility\Set
 */
class Config {

	/**
	 * Current loaded configuration.
	 *
	 * @access private
	 * @var array
	 */
	private $__config = array();

	/**
	 * Get the currently defined encoding for the application.
	 *
	 * @access public
	 * @return string
	 */
	public function encoding() {
		return $this->get('app.encoding') ?: 'UTF-8';
	}

	/**
	 * Grab a value from the current configuration.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key = null) {
		return Set::extract($this->__config, $key);
	}

	/**
	 * Checks to see if a key exists within the current configuration.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return Set::exists($this->__config, $key);
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
			
			if (!isset($this->__config[$file])) {
				$this->__config[$file] = array();
			}

			$this->__config[$file] = $reader->toArray() + $this->__config[$file];
			
		} else {
			throw new Exception(sprintf('Configuration file %s does not exist.', $file));
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
		return $this->get('app.name');
	}

	/**
	 * Get the currently defined salt for the application.
	 *
	 * @access public
	 * @return string
	 */
	public function salt() {
		return $this->get('app.salt');
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
		if ($key === 'debug.level') {
			Debugger::errorReporting(((int)$value > 0));
		}

		$this->__config = Set::insert($this->__config, $key, $value);

		return $this;
	}

}
