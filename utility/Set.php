<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\utility;

/**
 * Manipulates, manages and processes multiple types of result sets, primarily objects and arrays.
 *
 * @package	titon.utility
 */
class Set {

	/**
	 * Traversing constants.
	 */
	const EXTRACT = 1;
	const EXISTS = 2;
	const INSERT = 3;
	const REMOVE = 4;

	/**
	 * Determines the total depth of a multi-dimensional array or object.
	 * Has two methods of determining depth: based on recursive depth, or based on tab indentation (faster).
	 *
	 * @access public
	 * @param array|object $set
	 * @param boolean $recursive
	 * @return int
	 * @static
	 */
	public static function depth($set, $recursive = false) {
		if (empty($set)) {
			return 0;
		} else if (is_object($set)) {
			$set = self::toArray($set);
		}

		$depth = 1;

		// Depth based on indentation
		if ($recursive === false) {
			$array = print_r($set, true);
			$lines = explode("\n", $array);

			foreach ($lines as $line) {
				$indentation = (strlen($line) - strlen(ltrim($line))) / 4;

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
	 * Checks to see if a key/value pair exists within an array, determined by the given path.
	 *
	 * @access public
	 * @param array $set
	 * @param string $path
	 * @return array
	 * @static
	 */
	public static function exists($set, $path) {
		if (!is_array($set)) {
			return false;
		}

		return self::traverse(self::EXISTS, $set, $path);
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
		if (!is_array($set)) {
			return false;
		}

		$data = array();

		if (!empty($set)) {
			foreach ($set as $key => $value) {
				$data = self::traverse(self::INSERT, $data, $key, $value);
			}
		}

		return $data;
	}

	/**
	 * Extract the value of an array, depending on the paths given, represented by Key.Key.Key notation.
	 * Can extract multiple values by passing an array of paths as the second argument.
	 *
	 * @access public
	 * @param array $set
	 * @param array|string $paths
	 * @return array|boolean
	 * @static
	 */
	public static function extract($set, $paths) {
		if (!is_array($set) || empty($set)) {
			return false;
		}

		if (!is_array($paths)) {
			$paths = array($paths);
		}

		$data = array();

		foreach ($paths as $path) {
			$data[$path] = self::traverse(self::EXTRACT, $set, $path);
		}

		if (count($data) == 1) {
			return $data[$paths[0]];
		} else {
			return $data;
		}
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
		if (!is_array($set)) {
			return $set;
		}

		if ($recursive === true) {
			foreach ($set as &$value) {
				$value = self::filter($value, $recursive);
			}
		}

		return array_filter($set, function($var) {
			return (($var === 0) || ($var === '0') || (!empty($var)));
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
		if (!is_array($set)) {
			return $set;
		}

		if (!empty($path)) {
			$path = $path . '.';
		}

		$data = array();

		foreach ($set as $key => $value) {
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
	 * Inserts a value into the array set based on the given path.
	 *
	 * @access public
	 * @param array $set
	 * @param string $path
	 * @param mixed $insert
	 * @return array
	 * @static
	 */
	public static function insert($set, $path, $insert) {
		if (!is_array($set)) {
			return $set;
		}

		return self::traverse(self::INSERT, $set, $path, $insert);
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
		if (!is_array($set)) {
			return false;
		}

		foreach ($set as $value) {
			if (!is_string($value)) {
				return false;
			}

			if ($strict === true) {
				if (is_string($value) && is_numeric($value)) {
					return false;
				}
			}
		}

		return true;
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
		if (!is_array($set)) {
			return false;
		}

		foreach ($set as $value) {
			if (!is_numeric($value)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Works in a similar fashion to array_map() but can be used recursively as well as supply arguments for the function callback.
	 * Additionally, the $function argument can be a string or array containing the class and method name.
	 *
	 * @access public
	 * @param array $set
	 * @param string|array $function
	 * @param array $args
	 * @return array
	 * @static
	 */
	public static function map($set, $function, $args = array()) {
		if (is_array($function)) {
			if (!class_exists(get_class($function[0])) || !method_exists($function[0], $function[1])) {
				return $set;
			}
		} else {
			if (!function_exists($function)) {
				return $set;
			}
		}

		if (is_array($set)) {
			foreach ($set as &$value) {
				$value = self::map($value, $function, $args);
			}

			return $set;
		}

		array_unshift($args, $set);

		return call_user_func_array($function, $args);
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
		if (!is_array($set1) || !is_array($set2)) {
			return false;
		}

		return ($set1 === $set2);
	}

	/**
	 * Merge is a combination of array_merge() and array_merge_recursive(). However, when merging two keys with the same key,
	 * the previous value will be overwritten instead of being added into an array. The later array takes precedence when merging.
	 *
	 * @access public
	 * @param array $set	- Array to be merged
	 * @param array $set2	- Array to be merged...
	 * @return array
	 * @static
	 */
	public static function merge() {
		$sets = func_get_args();
		$set = array();
		$total = count($sets) - 1;

		if (!empty($sets)) {
			for ($i = 0; $i <= $total; ++$i) {
				foreach ($sets[$i] as $key => $value) {
					if (isset($set[$key])) {
						if (is_array($value) && is_array($set[$key])) {
							$set[$key] = self::merge($set[$key], $value);

						} else if (is_int($key)) {
							array_push($set, $value);

						} else {
							$set[$key] = $value;
						}
					} else {
						$set[$key] = $value;
					}
				}
			}
		}

		return $set;
	}

	/**
	 * Works similar to merge(), except that it will only overwrite/merge values if the keys exist in the previous array.
	 * Does not have support for multi-dimensional arrays or recursion.
	 *
	 * @access public
	 * @param array $set1 - The base array
	 * @param array $set2 - The array to overwrite the base array
	 * @return void|array
	 * @static
	 */
	public static function overwrite($set1, $set2) {
		if (!is_array($set1) || !is_array($set2)) {
			return;
		}

		return array_merge($set1, array_intersect_key($set2, $set1));
	}

	/**
	 * Generate an array with a range of numbers. Can apply a step interval to increase/decrease with larger increments.
	 *
	 * @access public
	 * @param int $start
	 * @param int $stop
	 * @param int $step
	 * @return array
	 * @static
	 */
	public static function range($start, $stop, $step = 1) {
		$array = array();

		if ($stop > $start) {
			for ($i = (int)$start; $i <= (int)$stop; $i += (int)$step) {
				$array[$i] = $i;
			}

		} else if ($stop < $start) {
			for ($i = (int)$start; $i >= (int)$stop; $i -= (int)$step) {
				$array[$i] = $i;
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
		if (!is_array($set)) {
			return $set;
		}

		return self::traverse(self::REMOVE, $set, $path);
	}

	/**
	 * Reverses the array by replacing all array keys with their value, if the value is a string and the key is numeric.
	 * If the value is empty/null and $truncate is true, that key will be removed.
	 *
	 * @access public
	 * @param array $set
	 * @param boolean $truncate
	 * @return array
	 * @static
	 */
	public static function reverse($set, $truncate = true) {
		if (!is_array($set)) {
			return $set;
		}

		$data = array();

		foreach ($set as $key => $value) {
			if (is_array($value)) {
				$data[$key] = self::reverse($value, $truncate);

			} else if (is_int($key) && !empty($value)) {
				$data[$value] = '';

			} else {
				if ($truncate === true && !empty($value)) {
					$data[$key] = $value;
				}
			}
		}

		return $data;
	}

	/**
	 * Transforms a multi/single-dimensional object into a mirrored array.
	 * Only public class properties will be parsed into the array.
	 *
	 * @access public
	 * @param object $object
	 * @return array
	 * @static
	 */
	public static function toArray($object) {
		if (is_array($object) || !is_object($object)) {
			return $object;
		}

		return array_map(array(__CLASS__, 'toArray'), get_object_vars($object));
	}

	/**
	 * Transforms a multi/single-dimensional array into a mirrored object.
	 *
	 * @access public
	 * @param array $array
	 * @return object
	 * @static
	 */
	public static function toObject($array) {
		if (is_object($array) || !is_array($array)) {
			return $array;
		}

		return (object) array_map(array(__CLASS__, 'toObject'), $array);
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

	/**
	 * Primary method used for all Set traversal and manipulation.
	 * Used to insert, remove and extract keys/values from the array, determined by the given dot notated path.
	 *
	 * @access public
	 * @param int $command
	 * @param array $set
	 * @param string $path
	 * @param mixed $value
	 * @return array
	 * @static
	 */
	public static function traverse($command, $set, $path, $value = null) {
		if (!is_array($set) || empty($path)) {
			return $set;
		}

		$search =& $set;
		$paths = explode('.', $path);
		$total = count($paths);

		while ($total > 0) {
			$key = $paths[0];

			// Within the last path
			if ($total == 1) {
				if ($command == self::INSERT) {
					$search[$key] = $value;

				} else if ($command == self::REMOVE) {
					unset($search[$key]);
					
				} else if ($command == self::EXISTS) {
					return isset($search[$key]);
					
				} else if ($command == self::EXTRACT) {
					return isset($search[$key]) ? $search[$key] : null;
				}

			// Break out of unexistent paths early
			} else if (isset($search[$key]) && !is_array($search[$key]) && $command !== self::INSERT) {
				if ($command == self::EXISTS) {
					return false;
					
				} else if ($command == self::EXTRACT) {
					return null;

				} else {
					return $set;
				}

			// Merge references
			} else {
				$search =& $search[$key];
			}

			array_shift($paths);
			$total--;
		}

		unset($search);
		return $set;
	}

}
