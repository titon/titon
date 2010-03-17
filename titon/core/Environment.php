<?php
/**
 * A hub that allows you to store different environment settings, which can be detected and initialized on runtime.
 * Furthermore it stores the current settings for the environment ($_ENV and $_SERVER).
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\core;

use \titon\core\Config;

/**
 * Environment Class
 *
 * @package		Titon
 * @subpackage	Titon.Core
 */
class Environment {

	/**
	 * Sets the default environment; defaults to development.
	 *
	 * @access private
	 * @var string
	 * @static
	 */
	private static $__default = 'development';

	/**
	 * Holds the list of possible environments and configuration.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__environments = array(
		'development' => array(
			'Hosts'                 => array('localhost', '127.0.0.1'),
			'App.name'              => 'Titon',
			'App.salt'              => '',
			'App.encoding'          => 'UTF-8',
			'Debug.level'           => 2,
			'Debug.email'           => '',
			'Cache.enabled'         => false,
			'Cache.expires'         => '+1 hour',
			'Locale.current'        => 'en_US',
			'Locale.default'        => 'en_US',
			'Locale.timezone'       => 'America/Los_Angeles', // http://us.php.net/manual/en/timezones.php
            'Locale.offset'         => '-8'
		)
	);

	/**
	 * Relate hostnames to environment configurations.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__hostMapping = array(
		'localhost' => 'development',
		'127.0.0.1' => 'development'
	);

	/**
	 * Disable the class to enforce static methods.
	 *
	 * @access private
	 * @return void
	 */
	private function __construct() { }
	
	/**
	 * Return the current environment name, based on hostname.
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function detect() {
        return isset(static::$__hostMapping[$_SERVER['HTTP_HOST']]) ? static::$__hostMapping[$_SERVER['HTTP_HOST']] : static::getDefault();
	}
    
	/**
	 * Get the default environment.
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function getDefault() {
		return static::$__default;
	}
	
	/**
	 * Initialize the environment by applying the configuration.
	 * Load the environment variables into the class; strtolower() all keys first.
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public static function initialize() {
		$current = static::$__environments[static::detect()];
		
		foreach ($current as $key => $value) {
			Config::set($key, $value);
		}
	}
	
	/**
	 * Set the default environment, must exist in the $__environments.
	 *
	 * @access public
	 * @param string $name
	 * @return void
	 * @static
	 */
	public static function setDefault($name) {
		if (isset(static::$__environments[$name])) {
			static::$__default = $name;
		}	
	}
	
	/**
	 * Add an environment and its settings to the application.
	 *
	 * @access public
	 * @param string $name
	 * @param array $options
	 * @return void
	 * @static
	 */
	public static function setup($name, array $options = array()) {
		if (isset(static::$__environments[$name])) {
			static::$__environments[$name] = $options + static::$__environments[$name];
		} else {
			static::$__environments[$name] = $options;
		}

		if (!empty(static::$__environments[$name]['Hosts'])) {
			foreach (static::$__environments[$name]['Hosts'] as $host) {
				static::$__hostMapping[$host] = $name;
			}
		}
	}

}
