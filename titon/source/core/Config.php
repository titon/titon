<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

use \titon\source\core\readers\IniReader;
use \titon\source\core\readers\PhpReader;
use \titon\source\core\readers\XmlReader;
use \titon\source\core\readers\JsonReader;
use \titon\source\core\readers\YamlReader;
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
 * @uses	Inflector
 * @uses	Set
 * @uses	Debugger
 * @uses	Exception
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
		return Set::exists($this->__config, $key);
	}

	/**
	 * Grab a value from the current configuration.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key = null) {
		if ($key === null) {
			return $this->__config;
		} else {
			return Set::extract($this->__config, $key);
		}
	}

	/**
	 * Loads a user created file into the configuration class.
	 * Uses the defined reader to parse the file.
	 *
	 * @access public
	 * @param string $file
	 * @param string $ext
	 * @return void
	 */
	public function load($file, $ext = self::INI_READER) {
		$path = APP_CONFIG .'sets'. DS . Inflector::filename($file, $ext);

		if (is_file($path)) {
			switch ($ext) {
				case self::XML_READER:
					$reader = new XmlReader($path);
				break;
				case self::PHP_READER:
					$reader = new PhpReader($path);
				break;
				case self::YAML_READER:
					$reader = new YamlReader($path);
				break;
				case self::JSON_READER:
					$reader = new JsonReader($path);
				break;
				case self::INI_READER:
				default:
					$reader = new IniReader($path);
				break;
			}
			
			if (!isset($this->__config[$file])) {
				$this->__config[$file] = array();
			}

			$this->__config[$file] = $reader->toArray() + $this->__config[$file];
			
		} else {
			throw new Exception('Configuration file does not exist.');
		}
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
		if ($key === 'debug.level') {
			Debugger::errorReporting(((int)$value > 0));
		}

		$this->__config = Set::insert($this->__config, $key, $value);
	}

}
