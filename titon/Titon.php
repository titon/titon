<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2009-2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon;

use \titon\Exception;
use \titon\core\Application;
use \titon\core\Cache;
use \titon\core\Config;
use \titon\core\Debugger;
use \titon\core\Dispatch;
use \titon\core\Environment;
use \titon\core\Event;
use \titon\core\Loader;
use \titon\core\Registry;
use \titon\core\Router;

/**
 * The primary framework class contains all core classes that manipulate and power the application, or add quick convenience.
 *
 * @package	titon.system
 * @uses	titon\Titon
 */
class Titon {

	/**
	 * Current framework version.
	 *
	 * @access public
	 * @var string
	 * @static
	 */
	public static $version = '0.0001 ALPHA';

	/**
	 * Installed objects that can not be uninstalled.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__locked = array();

	/**
	 * Installed objects.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__memory = array();

	/**
	 * Allow the installed classes to be called as a method.
	 *
	 * @access public
	 * @param string $key
	 * @param array $args
	 * @return object
	 * @static
	 */
	public static function __callStatic($key, $args = array()) {
		return self::get($key);
	}

	/**
	 * Return the object if it exists.
	 *
	 * @access public
	 * @param string $key
	 * @return object
	 * @static
	 */
	public static function get($key) {
		if (!isset(self::$__memory[$key])) {
			throw new Exception(sprintf('Object %s has not been installed into Titon.', $key));
		}

		return self::$__memory[$key];
	}

	/**
	 * Initialize the Titon framework by loading all the core objects.
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public static function initialize() {
		self::install('loader', new Loader(), true);
		self::install('debugger', new Debugger(), true);
		self::install('app', new Application(), true);
		self::install('cache', new Cache(), true);
		self::install('config', new Config(), true);
		self::install('dispatch', new Dispatch(), true);
		self::install('event', new Event(), true);
		self::install('registry', new Registry(), true);
		self::install('router', new Router(), true);
		self::install('environment', new Environment(), true);
	}

	/**
	 * Install an object. Primarily used for core classes.
	 *
	 * @access public
	 * @param string $key
	 * @param object $object
	 * @param bool $lock
	 * @return void
	 * @static
	 */
	public static function install($key, $object, $lock = false) {
		self::$__memory[$key] = $object;

		if ($lock) {
			self::$__locked[] = $key;
		}
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
	 * Shutdown the framework.
	 *
	 * @access public
	 * @return void
	 */
	public static function shutdown() {
		exit();
	}

	/**
	 * Uninstall an object. Core classes cannot be uninstalled.
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public static function uninstall($key) {
		if (!in_array($key, self::$__locked)) {
			unset(self::$__memory[$key]);
		}
	}

}