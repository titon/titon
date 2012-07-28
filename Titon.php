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
	public static $version = '0.5.0';

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
	 * @throws titon\Exception
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
		self::install('loader', new Loader(), true);
		self::install('debugger', new Debugger(), true);
		self::install('env', new Environment(), true);
		self::install('app', new Application(), true);
		self::install('cache', new Cache(), true);
		self::install('config', new Config(), true);
		self::install('registry', new Registry(), true);
		self::install('router', new Router(), true);
		self::install('g11n', new G11n(), true);
		self::install('event', new Event(), true);
		self::install('dispatch', new Dispatch(), true);
	}

	/**
	 * Install an object. Primarily used for core classes.
	 *
	 * @access public
	 * @param string $key
	 * @param object $object
	 * @param boolean $lock
	 * @return void
	 * @throws titon\Exception
	 * @static
	 */
	public static function install($key, $object, $lock = false) {
		$locked = self::$__locked + get_class_methods(__CLASS__);

		if (in_array($key, $locked)) {
			throw new Exception(sprintf('Object cannot be installed as the key %s is locked.', $key));
		}

		if ($lock) {
			self::$__locked[] = $key;
		}

		self::$__memory[$key] = $object;
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
	 * @return void
	 */
	public static function shutdown() {
		foreach (self::$__memory as $key => $object) {
			unset(self::$__memory[$key]);
		}

		exit();
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