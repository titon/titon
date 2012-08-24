<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2009-2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon;

use titon\Exception;
use titon\core\Application;
use titon\core\Cache;
use titon\core\Config;
use titon\core\Debugger;
use titon\core\Dispatch;
use titon\core\Environment;
use titon\core\Event;
use titon\core\G11n;
use titon\core\Loader;
use titon\core\Registry;
use titon\core\Router;

/**
 * The primary framework class contains all core classes that manipulate and power the application, or add quick convenience.
 *
 * @package	titon.system
 */
class Titon {

	/**
	 * Current framework version.
	 *
	 * @access public
	 * @var string
	 * @static
	 */
	public static $version = '0.6.0';

	/**
	 * Installed objects that can not be uninstalled.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__locked = [];

	/**
	 * Installed objects.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__memory = [];

	/**
	 * Allow the installed classes to be called as a method.
	 *
	 * @access public
	 * @param string $key
	 * @param array $args
	 * @return object
	 * @static
	 */
	public static function __callStatic($key, $args = []) {
		return self::get($key);
	}

	/**
	 * Return the object if it exists.
	 *
	 * @access public
	 * @param string $key
	 * @return object
	 * @throws \titon\Exception
	 * @static
	 */
	public static function get($key) {
		if (isset(self::$__memory[$key])) {
			return self::$__memory[$key];
		}

		throw new Exception(sprintf('Object %s has not been installed into Titon.', $key));
	}

	/**
	 * Initialize the Titon framework by loading all the core objects.
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public static function initialize() {
		date_default_timezone_set('UTC'); // Always use UTC

		self::install('loader', new Loader(), true);
		self::install('debugger', new Debugger(), true);	// Requires Loader
		self::install('config', new Config(), true);		// Requires Debugger
		self::install('env', new Environment(), true);
		self::install('app', new Application(), true); 		// Requires Config, Loader
		self::install('registry', new Registry(), true); 	// Requires Loader
		self::install('g11n', new G11n(), true); 			// Requires Registry
		self::install('router', new Router(), true); 		// Requires G11n
		self::install('event', new Event(), true); 			// Requires Router
		self::install('dispatch', new Dispatch(), true); 	// Requires Router, Environment; Dispatchers require Event
		self::install('cache', new Cache(), true);
	}

	/**
	 * Install an object. Primarily used for core classes.
	 *
	 * @access public
	 * @param string $key
	 * @param object $object
	 * @param boolean $lock
	 * @return void
	 * @throws \titon\Exception
	 * @static
	 */
	public static function install($key, $object, $lock = false) {
		$locked = self::$__locked + get_class_methods(__CLASS__);

		if (in_array($key, $locked)) {
			throw new Exception(sprintf('Object cannot be installed as the key %s is locked.', $key));
		}

		if ($lock) {
			self::$__locked[$key] = $key;
		}

		self::$__memory[$key] = $object;
	}

	/**
	 * Return true if a PHP extension has been loaded. If it hasn't, attempt to load it.
	 *
	 * @access public
	 * @param string $extension
	 * @return boolean
	 * @static
	 */
	public static function load($extension) {
		if (extension_loaded($extension)) {
			return true;
		}

		if (PHP_SHLIB_SUFFIX === 'dll') {
			$extension = 'php_' . $extension;
		}

		$extension .= '.' . PHP_SHLIB_SUFFIX;

		if (function_exists('dl') && dl($extension)) {
			return true;
		}

		return false;
	}

	/**
	 * Run the framework.
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public static function run() {
		self::startup();
		self::event()->notify('titon.startup');
		self::dispatch()->run();
		self::event()->notify('titon.shutdown');
		self::shutdown();
	}

	/**
	 * Startup the framework.
	 *
	 * @access public
	 * @return void
	 */
	public static function startup() {
		foreach (self::$__memory as &$object) {
			if (method_exists($object, 'initialize')) {
				$object->initialize();
			}
		}
	}

	/**
	 * Shutdown the framework and unset all core classes to trigger __destruct().
	 *
	 * @access public
	 * @param boolean $exit
	 * @return void
	 */
	public static function shutdown($exit = true) {
		foreach (self::$__memory as $key => $object) {
			unset(self::$__memory[$key], self::$__locked[$key]);
		}

		if ($exit) {
			exit();
		}
	}

	/**
	 * Uninstall an object. Core classes cannot be uninstalled.
	 *
	 * @access public
	 * @param string $key
	 * @return void
	 * @static
	 */
	public static function uninstall($key) {
		if (!in_array($key, self::$__locked)) {
			unset(self::$__memory[$key]);
		}
	}

}