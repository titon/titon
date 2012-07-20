<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\utility;

use titon\utility\UtilityException;
use \Closure;

/**
 * Manipulates, manages and processes multiple types of result sets: objects and arrays.
 *
 * @package	titon.utility
 */
class Hash {

	/**
	 * Determines the total depth of a multi-dimensional array or object.
	 * Has two methods of determining depth: based on recursive depth, or based on tab indentation (faster).
	 *
	 * @access public
	 * @param array|object $set
	 * @param boolean $recursive
	 * @return int
	 * @throws titon\utility\UtilityException
	 * @static
	 */
	public static function depth($set, $recursive = false) {
		if (is_object($set)) {
			$set = self::toArray($set);

		} else if (!is_array($set)) {
			throw new UtilityException('Value passed must be an array.');
		}

		if (empty($set)) {
			return 0;
		}

		$depth = 1;

		// Depth based on indentation
		if ($recursive === false) {
			$array = print_r($set, true);
			$lines = explode("\n", $array);

			foreach ($lines as $line) {
				$indentation = (mb_strlen($line) - mb_strlen(ltrim($line))) / 4;

				if ($indentation > $depth) {
					$depth = $indentation;
				}
			}

			return ceil(($depth - 1) / 2) + 1;

		// Depth based on recursion
		} else {
			foreach ($set as $value) {
				if (is_array($value)) {
					$count = self::depth($value) + 1;

					if ($count > $depth) {
						$depth = $count;
					}
				}
			}

			return $depth;
		}
	}

	/**
	 * Calls a function for each key-value pair in the set.
	 * If recursive is true, will apply the callback to nested arrays as well.
	 *
	 * @access public
	 * @param array $set
	 * @param Closure $callback
	 * @param boolean $recursive
	 * @return array
	 * @static
	 */
	public static function each($set, Closure $callback, $recursive = true) {
		foreach ((array) $set as $key => $value) {
			if (is_array($value) && $recursive) {
				$set[$key] = self::each($value, $callback, $recursive);
			} else {
				$set[$key] = $callback($value, $key);
			}
		}

		return $set;
	}

	/**
	 * Returns true if every element in the array satisfies the provided testing function.
	 *
	 * @access public
	 * @param array $set
	 * @param Closure $callback
	 * @return boolean
	 * @static
	 */
	public static function every($set, Closure $callback) {
		if (!empty($set)) {
			foreach ((array) $set as $key => $value) {
				if (!$callback($value, $key)) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Expand an array to a fully workable multi-dimensional array, where the values key is a dot notated path.
	 *
	 * @access public
	 * @param array $set
	 * @return array
	 * @static
	 */
	public static function expand($set) {
		$data = [];

		foreach ((array) $set as $key => $value) {
			$data = self::insert($data, $key, $value);
		}

		return $data;
	}

	/**
	 * Extract the value of an array, depending on the paths given, represented by Key.Key.Key notation.
	 * Can extract multiple values by passing an array of paths as the second argument.
	 *
	 * @access public
	 * @param array $set
	 * @param string $path
	 * @return mixed
	 * @static
	 */
	public static function extract($set, $path) {
		if (!is_array($set) || empty($set)) {
			return null;
		}

		$search =& $set;
		$paths = explode('.', (string) $path);
		$total = count($paths);

		while ($total > 0) {
			$key = $paths[0];

			// Within the last path
			if ($total === 1) {
				return array_key_exists($key, $search) ? $search[$key] : null;

			// Break out of non-existent paths early
			} else if (!array_key_exists($key, $search) || !is_array($search[$key])) {
				return null;
			}

			$search =& $search[$key];
			array_shift($paths);
			$total--;
		}

		unset($search);

		return null;
	}

	/**
	 * Filter out all keys within an array that have an empty value, excluding 0 (string and numeric).
	 * If $recursive is set to true, will remove all empty values within all sub-arrays.
	 *
	 * @access public
	 * @param array $set
	 * @param boolean $recursive
	 * @return mixed|array
	 * @static
	 */
	public static function filter($set, $recursive = true) {
		$set = (array) $set;

		if ($recursive) {
			foreach ($set as $key => $value) {
				if (is_array($value)) {
					$set[$key] = self::filter($value, $recursive);
				}
			}
		}

		return array_filter($set, function($var) {
			return ($var === 0 || $var === '0' || !empty($var));
		});
	}

	/**
	 * Flatten a multi-dimensional array by returning the values with their keys representing their previous pathing.
	 *
	 * @access public
	 * @param array $set
	 * @param string $path
	 * @return array
	 * @static
	 */
	public static function flatten($set, $path = null) {
		if (!empty($path)) {
			$path = $path . '.';
		}

		$data = [];

		foreach ((array) $set as $key => $value) {
			if (is_array($value)) {
				if (empty($value)) {
					$data[$path . $key] = null;
				} else {
					$data += self::flatten($value, $path . $key);
				}
			} else {
				$data[$path . $key] = $value;
			}
		}

		return $data;
	}

	/**
	 * Flip the array by replacing all array keys with their value, if the value is a string and the key is numeric.
	 * If the value is empty/false/null and $truncate is true, that key will be removed.
	 *
	 * @access public
	 * @param array $set
	 * @param boolean $recursive
	 * @param boolean $truncate
	 * @return array
	 * @static
	 */
	public static function flip($set, $recursive = true, $truncate = true) {
		if (!is_array($set)) {
			return $set;
		}

		$data = [];

		foreach ($set as $key => $value) {
			$empty = ($value === '' || $value === false || $value === null);

			if (is_array($value)) {
				if ($recursive) {
					$data[$key] = self::flip($value, $truncate);
				}

			} else if (is_int($key) && !$empty) {
				$data[$value] = '';

			} else {
				if ($truncate === true && !$empty) {
					$data[$value] = $key;
				}
			}
		}

		return $data;
	}

	/**
	 * Get a value from the set. If they path doesn't exist, return null, or if the path is empty, return the whole set.
	 *
	 * @access public
	 * @param array $set
	 * @param string $path
	 * @return mixed
	 */
	public static function get($set, $path = null) {
		if (empty($path)) {
			return $set;
		}

		return self::extract($set, $path);
	}

	/**
	 * Checks to see if a key/value pair exists within an array, determined by the given path.
	 *
	 * @access public
	 * @param array $set
	 * @param string $path
	 * @return array
	 * @static
	 */
	public static function has($set, $path) {
		if (!is_array($set) || empty($path)) {
			return false;
		}

		$search =& $set;
		$paths = explode('.', (string) $path);
		$total = count($paths);

		while ($total > 0) {
			$key = $paths[0];

			// Within the last path
			if ($total === 1) {
				return array_key_exists($key, $search);

				// Break out of non-existent paths early
			} else if (!array_key_exists($key, $search) || !is_array($search[$key])) {
				return false;
			}

			$search =& $search[$key];
			array_shift($paths);
			$total--;
		}

		unset($search);

		return false;
	}

	/**
	 * Includes the specified key-value pair in the set if the key doesn't already exist.
	 *
	 * @access public
	 * @param array $set
	 * @param string $path
	 * @param mixed $value
	 * @return array
	 */
	public static function inject($set, $path, $value) {
		if (self::has($set, $path)) {
			return $set;
		}

		return self::insert($set, $path, $value);
	}

	/**
	 * Inserts a value into the array set based on the given path.
	 *
	 * @access public
	 * @param array $set
	 * @param string $path
	 * @param mixed $value
	 * @return array
	 * @static
	 */
	public static function insert($set, $path, $value) {
		if (!is_array($set) || empty($path)) {
			return $set;
		}

		$search =& $set;
		$paths = explode('.', $path);
		$total = count($paths);

		while ($total > 0) {
			$key = $paths[0];

			// Within the last path
			if ($total === 1) {
				$search[$key] = $value;

			// Break out of non-existent paths early
			} else if (!array_key_exists($key, $search) || !is_array($search[$key])) {
				$search[$key] = [];
			}

			$search =& $search[$key];
			array_shift($paths);
			$total--;
		}

		unset($search);

		return $set;
	}

	/**
	 * Checks to see if all values in the array are strings, returns false if not.
	 * If $strict is true, method will fail if there are values that are numerical strings, but are not cast as integers.
	 *
	 * @access public
	 * @param array $set
	 * @param boolean $strict
	 * @return boolean
	 * @static
	 */
	public static function isAlpha($set, $strict = true) {
		return self::every($set, function($value, $key) use ($strict) {
			if (!is_string($value)) {
				return false;
			}

			if ($strict) {
				if (is_string($value) && is_numeric($value)) {
					return false;
				}
			}

			return true;
		});
	}

	/**
	 * Checks to see if all values in the array are numeric, returns false if not.
	 *
	 * @access public
	 * @param array $set
	 * @return boolean
	 * @static
	 */
	public static function isNumeric($set) {
		return self::every($set, function($value, $key) {
			return is_numeric($value);
		});
	}

	/**
	 * Returns the key of the specified value. Will recursively search if the first pass doesn't match.
	 *
	 * @access public
	 * @param array $set
	 * @param mixed $match
	 * @return mixed
	 */
	public static function keyOf($set, $match) {
		$return = null;
		$isArray = [];

		foreach ((array) $set as $key => $value) {
			if ($value === $match) {
				$return = $key;
			}

			if (is_array($value)) {
				$isArray[] = $key;
			}
		}

		if (empty($return) && !empty($isArray)) {
			foreach ($isArray as $key) {
				if ($value = self::keyOf($set[$key], $match)) {
					$return = $key . '.' . $value;
				}
			}
		}

		return $return;
	}

	/**
	 * Works in a similar fashion to array_map() but can be used recursively as well as supply arguments for the function callback.
	 * Additionally, the $function argument can be a string or array containing the class and method name.
	 *
	 * @access public
	 * @param array $set
	 * @param string|Closure $function
	 * @param array $args
	 * @return array
	 * @static
	 */
	public static function map($set, $function, $args = []) {
		foreach ((array) $set as $key => $value) {
			if (is_array($value)) {
				$set[$key] = self::map($value, $function, $args);

			} else {
				$temp = $args;
				array_unshift($temp, $value);

				$set[$key] = call_user_func_array($function, $temp);
			}
		}

		return $set;
	}

	/**
	 * Compares to see if the first array set matches the second set exactly.
	 *
	 * @access public
	 * @param array $set1
	 * @param array $set2
	 * @return boolean
	 * @static
	 */
	public static function matches($set1, $set2) {
		return ((array) $set1 === (array) $set2);
	}

	/**
	 * Merge is a combination of array_merge() and array_merge_recursive(). However, when merging two keys with the same key,
	 * the previous value will be overwritten instead of being added into an array. The later array takes precedence when merging.
	 *
	 * @access public
	 * @return array
	 * @static
	 */
	public static function merge() {
		$sets = func_get_args();
		$data = [];

		if (!empty($sets)) {
			foreach ($sets as $set) {
				foreach ((array) $set as $key => $value) {
					if (isset($data[$key])) {
						if (is_array($value) && is_array($data[$key])) {
							$data[$key] = self::merge($data[$key], $value);

						} else if (is_int($key)) {
							array_push($data, $value);

						} else {
							$data[$key] = $value;
						}
					} else {
						$data[$key] = $value;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Works similar to merge(), except that it will only overwrite/merge values if the keys exist in the previous array.
	 *
	 * @access public
	 * @param array $set1 - The base array
	 * @param array $set2 - The array to overwrite the base array
	 * @return null|array
	 * @static
	 */
	public static function overwrite($set1, $set2) {
		if (!is_array($set1) || !is_array($set2)) {
			return null;
		}

		$overwrite = array_intersect_key($set2, $set1);

		if (!empty($overwrite)) {
			foreach ($overwrite as $key => $value) {
				if (is_array($value)) {
					$set1[$key] = self::overwrite($set1[$key], $value);
				} else {
					$set1[$key] = $value;
				}
			}
		}

		return $set1;
	}

	/**
	 * Pluck a value out of each child-array and return an array of the plucked values.
	 *
	 * @access public
	 * @param array $set
	 * @param string $path
	 * @return array
	 * @static
	 */
	public static function pluck($set, $path) {
		$data = [];

		foreach ((array) $set as $array) {
			if ($value = self::extract($array, $path)) {
				$data[] = $value;
			}
		}

		return $data;
	}

	/**
	 * Generate an array with a range of numbers. Can apply a step interval to increase/decrease with larger increments.
	 *
	 * @access public
	 * @param int $start
	 * @param int $stop
	 * @param int $step
	 * @param boolean $index
	 * @return array
	 * @static
	 */
	public static function range($start, $stop, $step = 1, $index = true) {
		$array = [];

		if ($stop > $start) {
			for ($i = $start; $i <= $stop; $i += $step) {
				if ($index) {
					$array[$i] = $i;
				} else {
					$array[] = $i;
				}
			}

		} else if ($stop < $start) {
			for ($i = $start; $i >= $stop; $i -= $step) {
				if ($index) {
					$array[$i] = $i;
				} else {
					$array[] = $i;
				}
			}
		}

		return $array;
	}

	/**
	 * Remove an index from the array, determined by the given path.
	 *
	 * @access public
	 * @param array $set
	 * @param string $path
	 * @return array
	 * @static
	 */
	public static function remove($set, $path) {
		if (!is_array($set) || empty($path)) {
			return $set;
		}

		$search =& $set;
		$paths = explode('.', (string) $path);
		$total = count($paths);

		while ($total > 0) {
			$key = $paths[0];

			// Within the last path
			if ($total === 1) {
				unset($search[$key]);
				return $set;

			// Break out of non-existent paths early
			} else if (!array_key_exists($key, $search) || !is_array($search[$key])) {
				return $set;
			}

			$search =& $search[$key];
			array_shift($paths);
			$total--;
		}

		unset($search);

		return $set;
	}

	/**
	 * Set a value into the result set. If the paths is an array, loop over each one and insert the value.
	 *
	 * @access public
	 * @param array $set
	 * @param array|string $path
	 * @param mixed $value
	 * @return array
	 * @static
	 */
	public static function set($set, $path, $value = null) {
		if (is_array($path)) {
			foreach ($path as $key => $value) {
				$set = self::insert($set, $key, $value);
			}
		} else {
			$set = self::insert($set, $path, $value);
		}

		return $set;
	}

	/**
	 * Returns true if at least one element in the array satisfies the provided testing function.
	 *
	 * @access public
	 * @param array $set
	 * @param Closure $callback
	 * @return boolean
	 * @static
	 */
	public static function some($set, Closure $callback) {
		$pass = false;

		if (!empty($set)) {
			foreach ((array) $set as $key => $value) {
				if ($callback($value, $value)) {
					$pass = true;
					break;
				}
			}
		}

		return $pass;
	}

	/**
	 * Transforms a multi/single-dimensional object into a mirrored array.
	 * Only public class properties will be parsed into the array.
	 *
	 * @access public
	 * @param object $object
	 * @return array
	 * @throws titon\libs\utility\UtilityException
	 * @static
	 */
	public static function toArray($object) {
		if (is_array($object)) {
			return $object;

		} else if (!is_object($object)) {
			throw new UtilityException('Value passed must be an object.');
		}

		$array = get_object_vars($object);

		foreach ($array as $key => $value) {
			if (is_object($value)) {
				$array[$key] = self::toArray($value);
			}
		}

		return $array;
	}

	/**
	 * Transforms a multi/single-dimensional array into a mirrored object.
	 *
	 * @access public
	 * @param array $array
	 * @return object
	 * @throws titon\libs\utility\UtilityException
	 * @static
	 */
	public static function toObject($array) {
		if (is_object($array)) {
			return $array;

		} else if (!is_array($array)) {
			throw new UtilityException('Value passed must be an array.');
		}

		$object = (object) $array;

		foreach ($object as $key => $value) {
			if (is_array($value)) {
				$object->{$key} = self::toObject($value);
			}
		}

		return $object;
	}

	/**
	 * Convenience function for converting an object to array, or array to object.
	 *
	 * @access public
	 * @param array|object $set
	 * @return array|object
	 * @static
	 */
	public static function transform($set) {
		if (is_object($set)) {
			return self::toArray($set);

		} else if (is_array($set)) {
			return self::toObject($set);

		} else {
			return $set;
		}
	}

}
