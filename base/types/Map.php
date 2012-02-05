<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base\types;

use \titon\base\types\Type;
use \Closure;

/**
 * The Map type allows for the modification, manipulation and traversal of an array through the use of an object like interface.
 * One can also modify the map using a series of chained method calls that sequentially alter the initial value.
 *
 * @package	titon.base.types
 */
class Map extends Type implements \ArrayAccess, \Iterator, \Countable {

	/**
	 * Constants related to traverse()ing.
	 */
	const EXTRACT = 1;
	const EXISTS = 2;
	const INSERT = 3;
	const REMOVE = 4;

	/**
	 * Depth of the array.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_depth = null;

	/**
	 * Type cast to an array.
	 *
	 * @access public
	 * @param array $value
	 * @return void
	 */
	public function __construct($value) {
		parent::__construct((array) $value);
	}

	/**
	 * Add a value to the end of the array. This does not support literal keys.
	 *
	 * @access public
	 * @param mixed $value
	 * @return Map
	 * @chainable
	 */
	public function append($value) {
		if (is_array($value)) {
			$this->_value = $this->_value + $value;
		} else {
			$this->_value[] = $value;
		}

		return $this;
	}

	/**
	 * Split an array into chunks.
	 *
	 * @access public
	 * @param int $size
	 * @param boolean $preserve
	 * @return Map
	 * @chainable
	 */
	public function chunk($size, $preserve = false) {
		$this->_value = array_chunk($this->_value, (int) $size, $preserve);

		return $this;
	}

	/**
	 * Removes all empty, null, false and 0 items.
	 *
	 * @access public
	 * @param boolean $removeZero
	 * @return Map
	 * @chainable
	 */
	public function clean($removeZero = true) {
		if (!empty($this->_value)) {
			foreach ($this->_value as $key => $value) {
				if (($value == 0 && $removeZero) || empty($value)) {
					unset($this->_value[$key]);
				}
			}
		}

		return $this;
	}

	/**
	 * Empty the array.
	 *
	 * @access public
	 * @return Map
	 * @chainable
	 */
	public function clear() {
		$this->_value = array();

		return $this;
	}

	/**
	 * Compares the current array against the passed array and returns a new array
	 * with all the values that are found within both arrays. If strict is true,
	 * the keys must match as well as the values. A callback can be passed to
	 * further filter down the results.
	 *
	 * @access public
	 * @param array $array
	 * @param boolean $strict
	 * @param Closure $callback
	 * @return array
	 */
	public function compare(array $array, $strict = true, Closure $callback = null) {
		if ($strict) {
			if ($callback !== null) {
				return array_intersect_uassoc($this->_value, $array, $callback);
			} else {
				return array_intersect_assoc($this->_value, $array);
			}
		}

		return array_intersect($this->_value, $array);
	}

	/**
	 * Works exactly to compare() except that it uses a callback to validate the values.
	 * A second callback can be used to also compared against the array key.
	 *
	 * @access public
	 * @param array $array
	 * @param boolean $strict
	 * @param Closure $callback
	 * @param Closure $keyCallback
	 * @return array
	 */
	public function compareByCallback(array $array, $strict, Closure $callback, Closure $keyCallback = null) {
		if ($strict) {
			if ($keyCallback !== null) {
				return array_uintersect_uassoc($this->_value, $array, $callback, $keyCallback);
			} else {
				return array_uintersect_assoc($this->_value, $array, $callback);
			}
		}

		return array_uintersect($this->_value, $array, $callback);
	}

	/**
	 * Compares the current array against the passed array and returns a new array
	 * with all the values where keys are matched in both arrays.
	 * Only differences from the class instance is returned.
	 *
	 * @access public
	 * @param array $array
	 * @param Closure $callback
	 * @return array
	 */
	public function compareKeys(array $array, Closure $callback = null) {
		if ($callback !== null) {
			return array_intersect_ukey($this->_value, $array, $callback);
		}

		return array_intersect_key($this->_value, $array);
	}

	/**
	 * Merges the passed array with the current array and returns a new Map object.
	 *
	 * @access public
	 * @param array $array
	 * @param boolean $append
	 * @return Map
	 */
	public function concat(array $array, $append = true) {
		if ($append) {
			$array = $array + $this->_value;
		} else {
			$array = $this->_value + $array;
		}

		return new Map((array) $array);
	}

	/**
	 * Checks if a value exists in the array.
	 * 
	 * @access public
	 * @param mixed $value
	 * @return boolean
	 */
	public function contains($value) {
		return in_array($value, $this->_value);
	}

	/**
	 * Counts all the values in the array.
	 *
	 * @access public
	 * @return int
	 */
	public function countValues() {
		return array_count_values($this->_value);
	}

	/**
	 * Determines how deep the nested array is.
	 * Caches the depth value instead of running it every time.
	 *
	 * @access public
	 * @param array $set
	 * @param boolean $save
	 * @return int
	 */
	public function depth(array $set = array(), $save = true) {
		if ($this->_depth !== null) {
			return $this->_depth;
		}

		if (empty($set)) {
			$set = $this->_value;
		}

		$depth = 1;

		if (!empty($set)) {
			foreach ($set as $value) {
				if (is_array($value)) {
					$count = $this->depth($value, false) + 1;

					if ($count > $depth) {
						$depth = $count;
					}
				}
			}
		}

		if ($save) {
			$this->_depth = (int) $depth;
		}

		return $depth;
	}

	/**
	 * Compares the current array against the passed array and returns a new array
	 * with all the values that are not found within the passed array. If strict is true,
	 * the keys must match as well as the values. A callback can be passed to
	 * further filter down the results.
	 *
	 * @access public
	 * @param array $array
	 * @param boolean $strict
	 * @param Closure $callback
	 * @return array
	 */
	public function difference(array $array, $strict = true, Closure $callback = null) {
		if ($strict) {
			if ($callback !== null) {
				return array_diff_uassoc($this->_value, $array, $callback);
			} else {
				return array_diff_assoc($this->_value, $array);
			}
		}

		return array_diff($this->_value, $array);
	}

	/**
	 * Works exactly to difference() except that it uses a callback to validate the values.
	 * A second callback can be used to also compared against the array key.
	 *
	 * @access public
	 * @param array $array
	 * @param boolean $strict
	 * @param Closure $callback
	 * @param Closure $keyCallback
	 * @return array
	 */
	public function differenceByCallback(array $array, $strict, Closure $callback, Closure $keyCallback = null) {
		if ($strict) {
			if ($keyCallback !== null) {
				return array_udiff_uassoc($this->_value, $array, $callback, $keyCallback);
			} else {
				return array_udiff_assoc($this->_value, $array, $callback);
			}
		}

		return array_udiff($this->_value, $array, $callback);
	}

	/**
	 * Compares the current array against the passed array and returns a new array
	 * with all the values where keys are not matched in both arrays.
	 * Only differences from the class instance is returned.
	 *
	 * @access public
	 * @param array $array
	 * @param Closure $callback
	 * @return array
	 */
	public function differenceKeys(array $array, Closure $callback = null) {
		if ($callback !== null) {
			return array_diff_ukey($this->_value, $array, $callback);
		}

		return array_diff_key($this->_value, $array);
	}

	/**
	 * Checks to see if the passed argument is an explicit exact match.
	 *
	 * @access public
	 * @param mixed $value
	 * @return boolean
	 */
	public function equals($value) {
		return ($this->_value === $value);
	}

	/**
	 * Removes all occurrences of an item from the array.
	 *
	 * @access public
	 * @param mixed $data
	 * @return Map
	 * @chainable
	 */
	public function erase($data) {
		if (!empty($this->_value)) {
			foreach ($this->_value as $key => $value) {
				if ($value === $data) {
					unset($this->_value[$key]);
				}
			}
		}

		return $this;
	}

	/**
	 * Returns true if every element in the array satisfies the provided testing function.
	 *
	 * @access public
	 * @param Closure $callback
	 * @return boolean
	 */
	public function every(Closure $callback) {
		if (!empty($this->_value)) {
			foreach ($this->_value as $key => $value) {
				if (!$callback($key, $value)) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Checks to see if a certain index exists. Accepts a dot notated path to filter down the depth.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function exists($key) {
		return $this->traverse(self::EXISTS, (string) $key);
	}

	/**
	 * Extracts a value from the specified index. Accepts a dot notated path to filter down the depth.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function extract($key) {
		return $this->traverse(self::EXTRACT, (string) $key);
	}

	/**
	 * Filters elements of the array using a callback function.
	 *
	 * @access public
	 * @param Closure $callback
	 * @return Map
	 * @chainable
	 */
	public function filter(Closure $callback) {
		$this->_value = array_filter($this->_value, $callback);

		return $this;
	}

	/**
	 * Return the first element in the array. If preserve is disabled, the array indices will be reset.
	 *
	 * @access public
	 * @param boolean $preserve
	 * @return mixed
	 */
	public function first($preserve = true) {
		if ($preserve && $this->isNotEmpty()) {
			foreach ($this->_value as $value) {
				return $value;
			}
		}

		return array_shift($this->_value);
	}

	/**
	 * Flattens a multidimensional array into a single array.
	 * If serialize is false, it returns the default value.
	 *
	 * @access public
	 * @param boolean $serialize
	 * @param mixed $default
	 * @return Map
	 * @chainable
	 */
	public function flatten($serialize = true, $default = null) {
		if (!empty($this->_value)) {
			foreach ($this->_value as $key => $value) {
				if (is_array($value)) {
					if ($serialize) {
						$this->_value[$key] = serialize($value);

					} else if ($default instanceof Closure) {
						$this->_value[$key] = $default($key, $value);
						
					} else {
						$this->_value[$key] = $default;
					}
				}
			}
		}

		return $this;
	}

	/**
	 * Exchanges all keys with their associated values in the array.
	 *
	 * @access public
	 * @return Map
	 * @chainable
	 */
	public function flip() {
		$this->_value = array_flip($this->_value);

		return $this;
	}

	/**
	 * Grab a value based on a single key. Returns by reference to support objects in ArrayAccess.
	 * Use extract() to go further than a single key deep.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function &get($key) {
		return isset($this->_value[$key]) ? $this->_value[$key] : null;
	}

	/**
	 * Returns the index in which the passed key exists. Validates against literal and numeric keys.
	 *
	 * @access public
	 * @param mixed $key
	 * @return int
	 */
	public function indexOf($key) {
		$count = 0;

		if ($this->isNotEmpty()) {
			foreach ($this->_value as $index => $value) {
				if ($index == $key) {
					return $count;
				}

				++$count;
			}
		}

		return false;
	}

	/**
	 * Checks to see if the array is empty.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isEmpty() {
		return empty($this->_value);
	}

	/**
	 * Checks to see if the array is not empty.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isNotEmpty() {
		return !$this->isEmpty();
	}
	
	/**
	 * Return all the keys or a subset of the keys of the array.
	 * 
	 * @access public
	 * @return array
	 */
	public function keys() {
		return array_keys($this->_value);
	}

	/**
	 * Return the last element in the array. If preserve is disabled, the array indices will be reset.
	 *
	 * @access public
	 * @param boolean $preserve
	 * @return mixed
	 */
	public function last($preserve = true) {
		if ($preserve && $this->isNotEmpty()) {
			$length = $this->length();
			$counter = 1;

			foreach ($this->_value as $value) {
				if ($counter == $length) {
					return $value;
				}

				++$counter;
			}
		}
		
		return array_pop($this->_value);
	}

	/**
	 * Returns the last index in which the passed key exists. Validates against literal and numeric keys.
	 *
	 * @access public
	 * @param mixed $key
	 * @return int
	 */
	public function lastIndexOf($key) {
		$count = 0;
		$last = false;

		if ($this->isNotEmpty()) {
			foreach ($this->_value as $index => $value) {
				if ($index == $key) {
					$last = $count;
				}

				++$count;
			}
		}

		return $last;
	}

	/**
	 * Return the length of the array.
	 *
	 * @access public
	 * @param boolean $reset
	 * @return int
	 */
	public function length($reset = false) {
		if ($this->_length === null || $reset) {
			$this->_length = count($this->_value);
		}

		return $this->_length;
	}

	/**
	 * Applies the callback to the elements of the array.
	 *
	 * @access public
	 * @param Closure $callback
	 * @param array $data
	 * @return Map
	 * @chainable
	 */
	public function map(Closure $callback, array $data = array()) {
		$this->_value = array_map($callback, $this->_value, $data);

		return $this;
	}

	/**
	 * Merge an array with the current array. If recursive is true, it will merge children arrays recursively.
	 *
	 * @access public
	 * @param array $array
	 * @param boolean $recursive
	 * @return Map
	 * @chainable
	 */
	public function merge(array $array, $recursive = true) {
		if ($recursive) {
			$this->_value = array_merge_recursive($this->_value, $array);
		} else {
			$this->_value = array_merge($this->_value, $array);
		}

		return $this;
	}

	/**
	 * Add a value to the beginning of the array. This will reset all numerical indices.
	 *
	 * @access public
	 * @param mixed $value
	 * @return Map
	 * @chainable
	 */
	public function prepend($value) {
		if (is_array($value)) {
			$this->_value = $value + $this->_value;
		} else {
			array_unshift($this->_value, $value);
		}

		return $this;
	}

	/**
	 * Calculate the product of values in the array.
	 *
	 * @access public
	 * @return int
	 */
	public function product() {
		return array_product($this->_value);
	}

	/**
	 * Returns a random item from the array.
	 *
	 * @access public
	 * @return mixed
	 */
	public function random() {
		$values = array_values($this->_value);
		$random = rand(0, count($values) - 1);

		return $this->_value[$random];
	}

	/**
	 * Iteratively reduce the array to a single value using a callback function.
	 *
	 * @access public
	 * @param Closure $callback
	 * @param boolean $initial
	 * @return Map
	 * @chainable
	 */
	public function reduce(Closure $callback, $initial = null) {
		$this->_value = array_reduce($this->_value, $callback, $initial);

		return $this;
	}

	/**
	 * Remove an index from the array. Accepts a dot notated path to drill down the dimensions.
	 *
	 * @access public
	 * @param string $key
	 * @return Map
	 * @chainable
	 */
	public function remove($key) {
		$this->traverse(self::REMOVE, (string) $key);

		return $this;
	}

	/**
	 * Alias for rewind(), however returns the chainable object.
	 *
	 * @access public
	 * @return Map
	 * @chainable
	 */
	public function reset() {
		$this->rewind();

		return $this;
	}

	/**
	 * Reverse the order of the array. If preserve is true, keys will not be reset.
	 *
	 * @access public
	 * @param boolean $preserve
	 * @return Map
	 * @chainable
	 */
	public function reverse($preserve = true) {
		$this->_value = array_reverse($this->_value, $preserve);

		return $this;
	}

	/**
	 * Randomize the order of elements in the array.
	 *
	 * @access public
	 * @return Map
	 * @chainable
	 */
	public function shuffle() {
		shuffle($this->_value);

		return $this;
	}

	/**
	 * Extract a slice of the array.
	 *
	 * @access public
	 * @param int $offset
	 * @param int $length
	 * @param boolean $preserve
	 * @return array
	 */
	public function slice($offset, $length = null, $preserve = false) {
		if (!$length && $length !== 0) {
			$length = abs($offset);
		}
		
		return array_slice($this->_value, (int) $offset, (int) $length, $preserve);
	}

	/**
	 * Returns true if at least one element in the array satisfies the provided testing function.
	 *
	 * @access public
	 * @param Closure $callback
	 * @return boolean
	 */
	public function some(Closure $callback) {
		$pass = true;

		if (!empty($this->_value)) {
			foreach ($this->_value as $key => $value) {
				if (!$callback($key, $value)) {
					$pass = false;
					break;
				}
			}
		}

		return $pass;
	}

	/**
	 * Sort the values in the array based on a specific flag. Set $reverse to true to sort in reverse.
	 * If $preserve is true, the indices will be left in tact.
	 *
	 * @access public
	 * @param boolean $reverse
	 * @param boolean $preserve
	 * @param int $flags
	 * @return Map
	 * @chainable
	 */
	public function sort($reverse = false, $preserve = true, $flags = SORT_REGULAR) {
		if ($reverse) {
			if ($preserve) {
				arsort($this->_value, $flags);
			} else {
				rsort($this->_value, $flags);
			}
		} else {
			if ($preserve) {
				asort($this->_value, $flags);
			} else {
				sort($this->_value, $flags);
			}
		}

		return $this;
	}

	/**
	 * Sort the values in the array using a custom defined callback.
	 * If $preserve is true, the indices will be left in tact.
	 *
	 * @access public
	 * @param Closure $callback
	 * @param boolean $preserve
	 * @return Map
	 * @chainable
	 */
	public function sortByCallback(Closure $callback, $preserve = true) {
		if ($preserve) {
			uasort($this->_value, $callback);
		} else {
			usort($this->_value, $callback);
		}

		return $this;
	}

	/**
	 * Sort the keys in the array based on a specific flag. Set $reverse to true to sort in reverse.
	 *
	 * @access public
	 * @param boolean $reverse
	 * @param int $flags
	 * @return Map
	 * @chainable
	 */
	public function sortKeys($reverse = false, $flags = SORT_REGULAR) {
		if ($reverse) {
			krsort($this->_value, $flags);
		} else {
			ksort($this->_value, $flags);
		}

		return $this;
	}

	/**
	 * Sort the keys in the array using a custom defined callback.
	 *
	 * @access public
	 * @param Closure $callback
	 * @return Map
	 * @chainable
	 */
	public function sortKeysByCallback(Closure $callback) {
		uksort($this->_value, $callback);

		return $this;
	}

	/**
	 * Sort the array using a natural algorythm. This function implements a sort algorithm that orders
	 * alphanumeric strings in the way a human being would while maintaining key/value associations.
	 *
	 * @access public
	 * @param boolean $strict
	 * @return Map
	 * @chainable
	 */
	public function sortNatural($strict = false) {
		if ($strict) {
			natsort($this->_value);
		} else {
			natcasesort($this->_value);
		}

		return $this;
	}

	/**
	 * Remove a portion of the array and replace it with something else.
	 *
	 * @access public
	 * @param int $offset
	 * @param int $length
	 * @param array $replacement
	 * @return array
	 */
	public function splice($offset, $length, array $replacement) {
		return array_splice($this->_value, (int) $offset, (int) $length, $replacement);
	}
	
	/**
	 * Calculate the sum of values in the array.
	 * 
	 * @access public
	 * @return int 
	 */
	public function sum() {
		return array_sum($this->_value);
	}

	/**
	 * Used to insert, remove and extract keys/values from the array, determined by the given dot notated path.
	 *
	 * @access public
	 * @param int $command
	 * @param string $path
	 * @param mixed $value
	 * @return mixed
	 */
	public function traverse($command, $path, $value = null) {
		$set = $this->_value;
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
					return $search[$key] ?: null;
				}

			// Break out of unexistent paths early
			} else if (!is_array($search[$key]) && $command !== self::INSERT) {
				if ($command == self::EXISTS) {
					return false;

				} else if ($command == self::EXTRACT) {
					return null;

				} else {
					$this->_value = $set;
					return;
				}

			// Merge references
			} else {
				$search =& $search[$key];
			}

			array_shift($paths);
			$total--;
		}

		unset($search);
		$this->_value = $set;
	}

	/**
	 * Define basic to string.
	 *
	 * @access public
	 * @return mixed
	 */
	public function toString() {
		return serialize($this->_value);
	}

	/**
	 * Removes duplicate values from the array.
	 *
	 * @access public
	 * @param int $flags
	 * @return Map
	 * @chainable
	 */
	public function unique($flags = SORT_REGULAR) {
		$this->_value = array_unique($this->_value, $flags);

		return $this;
	}

	/**
	 * Return all the values of an array and reorder indices.
	 *
	 * @access public
	 * @return array
	 */
	public function values() {
		return array_values($this->_value);
	}

	/**
	 * Apply a user function to every member of an array.
	 *
	 * @access public
	 * @param Closure $callback
	 * @param boolean $recursive
	 * @param mixed $data
	 * @return Map
	 * @chainable
	 */
	public function walk(Closure $callback, $recursive = true, $data = null) {
		if ($recursive) {
			$this->_value = array_walk_recursive($this->_value, $callback, $data);
		} else {
			$this->_value = array_walk($this->_value, $callback, $data);
		}

		return $this;
	}

	/**
	 * Set a key/value pair within in any multi-dimensional array depth.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return Map
	 * @chainable
	 */
	public function write($key, $value) {
		$this->traverse(self::INSERT, (string) $key, $value);

		return $this;
	}

	/**
	 * ArrayAccess: Checking if a key/index exists.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function offsetExists($key) {
		return $this->exists($key);
	}

	/**
	 * ArrayAccess: Getting a value based on key.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function offsetGet($key) {
		return $this->get($key);
	}

	/**
	 * ArrayAccess: Setting a value.
	 *
	 * @access public
	 * @param string $key
	 * @param string $value
	 * @return boolean
	 */
	public function offsetSet($key, $value) {
		if ($key === null) {
			$this->append($value);
		} else {
			$this->write($key, $value);
		}
	}

	/**
	 * ArrayAccess: Deleting an array index.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function offsetUnset($key) {
		$this->remove($key);
	}

	/**
	 * Iterator: Reset the iterator back to the first array index.
	 *
	 * @access public
	 * @return boolean
	 */
	public function rewind() {
		reset($this->_value);
	}

	/**
	 * Iterator: Return the current value.
	 *
	 * @access public
	 * @return boolean
	 */
	public function current() {
		return current($this->_value);
	}

	/**
	 * Iterator: Return the current key.
	 *
	 * @access public
	 * @return boolean
	 */
	public function key() {
		return key($this->_value);
	}

	/**
	 * Iterator: Grab the next key.
	 *
	 * @access public
	 * @return boolean
	 */
	public function next() {
		return next($this->_value);
	}

	/**
	 * Iterator: Check if current key is valid.
	 *
	 * @access public
	 * @return boolean
	 */
	public function valid() {
		return ($this->current() !== false);
	}

	/**
	 * Countable: Return the length of the array.
	 *
	 * @access public
	 * @param boolean $reset;
	 * @return boolean
	 */
	public function count($reset = false) {
		return $this->length($reset);
	}

}
