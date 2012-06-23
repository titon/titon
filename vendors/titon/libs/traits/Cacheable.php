<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\traits;

/**
 * The Cacheable trait provides static methods which provide functionality to cache any
 * data from the class layer. All data is unique and represented by a generated cache key.
 *
 * @package	titon.libs.traits
 */
trait Cacheable {

	/**
	 * Cached items indexed by key.
	 *
	 * @access public
	 * @var array
	 */
	public static $_cache = array();

	/**
	 * Generate a cache key. If an array is passed, drill down and form a key.
	 *
	 * @access public
	 * @param string|array $keys
	 * @return string
	 * @static
	 */
	public static function createCacheKey($keys) {
		if (is_array($keys)) {
			$key = array_shift($keys);

			if (!empty($keys)) {
				foreach ($keys as $value) {
					if (is_array($value)) {
						$key .= '-' . md5(json_encode($value));
					} else if ($value) {
						$key .= '-' . $value;
					}
				}
			}
		} else {
			$key = $keys;
		}

		return (string) $key;
	}

	/**
	 * Return a cached item if it exists, else return null.
	 *
	 * @access public
	 * @param string|array $key
	 * @return mixed
	 */
	public static function getCache($key) {
		$key = self::createCacheKey($key);

		if (isset(self::$_cache[$key])) {
			return self::$_cache[$key];
		}

		return null;
	}

	/**
	 * Set a value to the cache with the defined key.
	 * This will overwrite any data with the same key.
	 * The value being saved will be returned.
	 *
	 * @access public
	 * @param string|array $key
	 * @param mixed $value
	 * @return mixed
	 */
	public static function setCache($key, $value) {
		self::$_cache[self::createCacheKey($key)] = $value;

		return $value;
	}

	/**
	 * Remove an item from the cache. Return true if the item was removed.
	 *
	 * @access public
	 * @param string|array $key
	 * @return boolean
	 */
	public static function removeCache($key) {
		$key = self::createCacheKey($key);

		if (isset(self::$_cache[$key])) {
			unset(self::$_cache[$key]);

			return true;
		}

		return false;
	}

}