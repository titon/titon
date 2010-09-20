<?php
/**
 * Stores the current configuration options for the application.
 * Can be custom config, Titon specific config, or environmental config.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\core;

use \titon\log\Debugger;
use \titon\log\Exception;
use \titon\utility\Inflector;
use \titon\utility\Set;

/**
 * Configuration Class
 *
 * @package		Titon
 * @subpackage	Titon.Core
 */
class Config {

    /**
     * Generate a config file as an array. Argument setting for generate().
     *
     * @var string
     */
    const GENERATE_ARRAY = 1;

    /**
     * Generate a config file as an object. Argument setting for generate().
     *
     * @var string
     */
    const GENERATE_OBJECT = 2;

	/**
	 * Current loaded configuration.
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	private static $__config = array();

	/**
	 * Disable the class to enforce static methods.
	 *
	 * @access private
	 * @return void
	 */
	private function __construct() { }

	/**
	 * Checks to see if a key exists within the current configuration.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 * @static
	 */
	public static function check($key) {
		if ($key == 'debug') {
			$key = 'Debug.level';
		}
		
		return Set::exists(static::$__config, (string)$key);
	}

	/**
	 * Loads and parses a user created ini file into a set (object, array), then returns the set.
	 *
	 * @access public
	 * @param string $file
	 * @param string $format
	 * @return object|array
	 * @static
	 */
	public static function generate($file, $format = self::GENERATE_ARRAY) {
		$path = CONFIG .'sets'. DS . Inflector::filename($file, 'ini');
		
		if (file_exists($path)) {
			$config = parse_ini_file($path, true, INI_SCANNER_NORMAL);
			$config = Set::expand($config);
			
			if ($format === self::GENERATE_OBJECT) {
				$config = Set::toObject($config);
			}
			
			return $config;
		}
		
		return null;
	}

	/**
	 * Grab a value from the current configuration.
	 *
	 * @access public
	 * @param string $key
	 * @return string|null
	 * @static
	 */
	public static function get($key) {
		if ($key == 'debug') {
			$key = 'Debug.level';
		}
		
		return Set::extract(static::$__config, (string)$key);
	}

	/**
	 * Loads a user create ini file into the configuration class. Will not overwrite core settings.
	 *
	 * @access public
	 * @param string $file
	 * @return void
	 * @static
	 */
	public static function load($file) {
		if ($config = static::generate($file)) {
			static::$__config = Set::merge($config, static::$__config);
		}
	}

	/**
	 * Apply settings to the current loaded configuration.
	 * If debug is being set, apply the error reporting rules.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 * @static
	 */
	public static function set($key, $value) {
		if ($key === 'debug') {
			$key = 'Debug.level';

            if ($value == 0) {
                Debugger::errorReporting(Debugger::ERRORS_OFF);
            } else {
                Debugger::errorReporting(Debugger::ERRORS_ON);
            }
		}
		
		static::$__config = Set::insert(static::$__config, (string)$key, $value);
	}
	
}
