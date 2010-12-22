<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

use \titon\source\log\Debugger;

/**
 * Stores the current configuration options for the application.
 * Configuration can be loaded from multiple sources including environment, bootstrappings and internal system classes.
 * Various readers can be used to import specific configuration files.
 *
 * @package		Titon
 * @subpackage	Core
 */
class Config {

	/**
	 * Types of readers available for configuration importing.
	 */
	const XML_READER = 'xml';
	const INI_READER = 'ini';
	const PHP_READER = 'php';
	const YAML_READER = 'yaml';
	const JSON_READER = 'json';

	/**
	 * Current loaded configuration.
	 *
	 * @access private
	 * @var array
	 */
	private $__config = array();

	/**
	 * Checks to see if a key exists within the current configuration.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function check($key) {
		return isset($this->__config[$key]);
	}

	/**
	 * Grab a value from the current configuration.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		return $this->__config[$key] ?: null;
	}

	/**
	 * Loads a user created file into the configuration class.
	 * Uses the defined reader to parse the file.
	 *
	 * @access public
	 * @param string $file
	 * @return void
	 */
	public function load($file, $reader = self::INI_READER) {
		// @todo - Use loader to determine path
		// @todo - Create readers to import data
	}

	/**
	 * Add values to the current loaded configuration.
	 * If debug is being set, apply the error reporting rules.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set($key, $value) {
		if ($key === 'debug') {
			Debugger::errorReporting(((int)$value > 0));
		}

		$this->__config[$key] = $value;
	}

}
