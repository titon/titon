<?php
/**
 * The Registry acts a central hub where any part of the application can access a single instance of a stored object.
 * It registers all objects and instances into the class to store in memory and be re-useable later at any given time.
 * Is also packaged to handle registry on a per module basis using the architecture setup.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\core;

use \titon\core\App;
use \titon\log\Exception;
use \titon\utility\Set;

/**
 * Registry Class
 *
 * @package		Titon
 * @subpackage	Titon.Core
 */
class Registry {

    /**
     * Return the listing as literal objects. Argument setting for listObjects().
     *
     * @var boolean
     */
    const LIST_OBJECTS = 1;

    /**
     * Return the listing by class names. Argument setting for listObjects().
     *
     * @var boolean
     */
    const LIST_NAMES = 2;

    /**
     * Configuration settings that are automatically loaded into classes upon instantiation.
     *
     * @access private
     * @var array
     * @static
     */
    private static $__config = array();

	/**
	 * Class names of all registered objects; grouped by their dot path or namespace architecture.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__mapping = array();

	/**
	 * Objects that have been registered into memory. The array index is represented by the namespace convention,
     * where as the array value would be the matching instantiated object.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__registered = array();

	/**
	 * Disable the class to enforce static methods.
	 *
	 * @access private
	 * @return void
	 */
	private function __construct() { }
	
	/**
	 * Checks to see if an object has been registered (instantiated).
	 *
	 * @access public
	 * @param string $slug
	 * @return boolean
	 * @static
	 */
	public static function check($slug) {
		return (isset(static::$__registered[$slug]) && is_object(static::$__registered[$slug]));
	}

    /**
     * Defines an array of configuration that should be loaded into a class when its instantiated.
     *
     * @access public
     * @param string $slug
     * @param array $config
     * @return void
     * @static
     */
    public static function configure($slug, array $config = array()) {
        if (isset(static::$__config[$slug])) {
            static::$__config[$slug] = $config + static::$__config[$namespace];
        } else {
            static::$__config[$slug] = $config;
        }
    }
	
	/**
	 * Delete an object from registry and remove its name from the mapping.
	 * Returns true if object exists and was deleted, else returns false.
	 *
	 * @access public
	 * @param string $slug
	 * @return boolean
	 * @static
	 */
	public static function delete($slug) {
		if (isset(static::$__registered[$slug])) {
			unset(static::$__registered[$slug]);

			static::$__mapping = Set::remove(static::$__mapping, $slug);
			return true;
		}
		
		return false;
	}

	/**
	 * Register a file into memory and return an instantiated object, based on a dot notated path.
	 *
	 * @access public
	 * @param string $slug
	 * @param array $config
	 * @return object
	 * @static
	 */
	public static function factory($slug, array $config = array()) {
        if (isset(static::$__registered[$slug])) {
            return static::$__registered[$slug];
        }

        $namespace = App::toNamespace($slug);

        if (!class_exists($namespace)) {
            App::import($namespace);
        }

        if (class_exists($namespace)) {
            if (isset(static::$__config[$slug])) {
                $config = $config + static::$__config[$slug];
            }

            return static::store(new $namespace($config));
        } else {
            throw new Exception(sprintf('Class "%s" could not be instantiated into the registry.', $slug));
        }
	}

	/**
	 * Flush the registry by removing all stored objects.
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public static function flush() {
		static::$__registered = array();
		static::$__mapping = array();
        static::$__config = array();
	}
	
	/**
	 * Returns an array of all objects that have been registered, valued by their dot notation.
	 *
	 * @access public
	 * @param boolean $showObject
	 * @return array
	 * @static
	 */
	public static function listObjects($mode = self::LIST_NAMES) {
		if (empty(static::$__registered)) {
			return static::$__registered;
		} else {
            switch ($mode) {
                case self::LIST_OBJECTS:    return static::$__registered; break;
                case self::LIST_NAMES:      return array_keys(static::$__registered); break;
            }
		}
	}

	/**
	 * Return a full detailed list of all registered objects organized by their slug.
	 * Can filter down the architecture by supplying a dot path notation.
	 *
	 * @access public
	 * @param string $path
	 * @return array
	 * @static
	 */
	public static function listMapping($path = null) {
        return empty($path) ? static::$__mapping : Set::extract(static::$__mapping, (string)$path);
	}

	/**
	 * Manually store an object into registry, and store the object name to the $__mapping array.
	 * The property $__registered contains the object where as $__mapping is just the class name.
	 *
	 * @access public
	 * @param object $object
     * @param string $slug
	 * @return object
	 * @static
	 */
	public static function store($object, $slug = null) {
		if (!is_object($object)) {
			throw new Exception('The object passed must be instantiated.');
		}

		$namespace = get_class($object);
        $className = App::baseClass($namespace);

        if (!$slug) {
            $slug = App::toDotNotation($namespace);
        }

		static::$__registered[$slug] = $object;
		static::$__mapping = Set::insert(static::$__mapping, $slug, $className);

		return $object;
	}
	
}
