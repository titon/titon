<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base;

use titon\base\Type;
use titon\utility\Hash;
use \Closure;
use \ArrayAccess;
use \Iterator;
use \Countable;

/**
 * The Map type allows for the modification, manipulation and traversal of an array through the use of an object like interface.
 * One can also modify the map using a series of chained method calls that sequentially alter the initial value.
 *
 * @package	titon.base
 */
class Map extends Type implements ArrayAccess, Iterator, Countable {

	/**
	 * Type cast to an array.
	 *
	 * @access public
	 * @param array $value
	 */
	public function __construct($value = []) {
		parent::__construct((array) $value);
	}

	/**
	 * Add a value to the end of the array. This does not support literal keys.
	 *
	 * @access public
	 * @param mixed $value
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function append($value) {
		if (is_array($value)) {
			foreach ($value as $v) {
				$this->append($v);
			}
		} else {
			array_push($this->_value, $value);
		}

		return $this;
	}

	/**
	 * Split an array into chunks.
	 *
	 * @access public
	 * @param int $size
	 * @param boolean $preserve
	 * @return array
	 */
	public function chunk($size, $preserve = true) {
		return array_chunk($this->_value, (int) $size, $preserve);
	}

	/**
	 * Removes all empty, null, false and 0 items.
	 *
	 * @access public
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function clean() {
		if ($this->_value) {
			foreach ($this->_value as $key => $value) {
				if (empty($value) && $value !== 0) {
					unset($this->_value[$key]);
				}
			}
		}

		return $this;
	}

	/**
	 * Compares the current array against the passed array and returns a new array
	 * with all the values that are found within both arrays. If strict is true,
	 * the keys must match as well as the values. A callback can be passed to
	 * further filter down the results.
	 *
	 * If options.on equals "keys":
	 * Compares the current array against the passed array and returns a new array
	 * with all the values where keys are matched in both arrays.
	 * Only differences from the class instance is returned.
	 *
	 * If options.valueCallback is set:
	 * Works exactly like default compare() except that it uses a callback to validate the values.
	 * A second callback can be used to also compared against the array key.
	 *
	 * @access public
	 * @param array $array
	 * @param mixed $options
	 * 		- strict: Will validate the array keys as well
	 * 		- callback: Closure to compare keys with
	 * 		- valeCallback: Closure to compare values with
	 * 		- on: Either "keys" or "values"
	 * @return array
	 */
	public function compare(array $array, $options = []) {
		if ($options instanceof Closure) {
			$options = array('callback' => $options);

		} else if (is_bool($options)) {
			$options = array('strict' => $options);
		}

		$options = (array) $options + array(
			'strict' => true,
			'callback' => null,
			'valueCallback' => null,
			'on' => 'values'
		);

		$callback = $options['callback'];
		$valueCallback = $options['valueCallback'];

		// Prepare array
		$value = Hash::filter($this->_value, false, function($val) {
			return !is_array($val);
		});

		// Values
		if ($options['on'] === 'values') {

			// Compare with callback
			if ($valueCallback instanceof Closure) {
				if ($callback instanceof Closure) {
					return array_uintersect_uassoc($value, $array, $valueCallback, $callback);

				} else if ($options['strict']) {
					return array_uintersect_assoc($value, $array, $valueCallback);

				} else {
					return array_uintersect($value, $array, $valueCallback);
				}

			// Compare regular
			} else {
				if ($options['strict']) {
					if ($callback instanceof Closure) {
						return array_intersect_uassoc($value, $array, $callback);

					} else {
						return array_intersect_assoc($value, $array);
					}
				} else {
					return array_intersect($value, $array);
				}
			}

		// Keys
		} else {
			if ($callback instanceof Closure) {
				return array_intersect_ukey($value, $array, $callback);

			} else {
				return array_intersect_key($value, $array);
			}
		}
	}

	/**
	 * Merges the passed array with the current array and returns a new Map object.
	 *
	 * @access public
	 * @param array $array
	 * @return \titon\base\Map
	 */
	public function concat(array $array) {
		return new Map(Hash::merge($this->_value, $array));
	}

	/**
	 * Checks if a value exists in the array.
	 *
	 * @access public
	 * @param mixed $value
	 * @return boolean
	 */
	public function contains($value) {
		return in_array($value, array_values($this->_value), true);
	}

	/**
	 * Counts all the values in the array.
	 *
	 * @access public
	 * @return int
	 */
	public function countValues() {
		$values = array_values($this->_value);

		foreach ($values as $key => $value) {
			if (!is_string($value) && !is_numeric($value)) {
				unset($values[$key]);
			}
		}

		return array_count_values($values);
	}

	/**
	 * Determines how deep the nested array is.
	 *
	 * @access public
	 * @return int
	 */
	public function depth() {
		return Hash::depth($this->_value);
	}

	/**
	 * Compares the current array against the passed array and returns a new array
	 * with all the values that are not found within the passed array. If strict is true,
	 * the keys must match as well as the values. A callback can be passed to
	 * further filter down the results.
	 *
	 * If options.on equals "keys":
	 * Compares the current array against the passed array and returns a new array
	 * with all the values where keys are not matched in both arrays.
	 * Only differences from the class instance is returned.
	 *
	 * If options.valueCallback is set:
	 * Works exactly like default difference() except that it uses a callback to validate the values.
	 * A second callback can be used to also compared against the array key.
	 *
	 * @access public
	 * @param array $array
	 * @param mixed $options
	 * 		- strict: Will validate the array keys as well
	 * 		- callback: Closure to compare keys with
	 * 		- valeCallback: Closure to compare values with
	 * 		- on: Either "keys" or "values"
	 * @return array
	 */
	public function difference(array $array, $options = []) {
		if ($options instanceof Closure) {
			$options = array('callback' => $options);

		} else if (is_bool($options)) {
			$options = array('strict' => $options);
		}

		$options = (array) $options + array(
			'strict' => true,
			'callback' => null,
			'valueCallback' => null,
			'on' => 'values'
		);

		$callback = $options['callback'];
		$valueCallback = $options['valueCallback'];

		// Prepare array
		$value = Hash::filter($this->_value, false, function($val) {
			return !is_array($val);
		});

		// Values
		if ($options['on'] === 'values') {

			// Compare with callback
			if ($valueCallback instanceof Closure) {
				if ($callback instanceof Closure) {
					return array_udiff_uassoc($value, $array, $valueCallback, $callback);

				} else if ($options['strict']) {
					return array_udiff_assoc($value, $array, $valueCallback);

				} else {
					return array_udiff($value, $array, $valueCallback);
				}

			// Compare regular
			} else {
				if ($options['strict']) {
					if ($callback instanceof Closure) {
						return array_diff_uassoc($value, $array, $callback);

					} else {
						return array_diff_assoc($value, $array);
					}
				} else {
					return array_diff($value, $array);
				}
			}

			// Keys
		} else {
			if ($callback instanceof Closure) {
				return array_diff_ukey($value, $array, $callback);

			} else {
				return array_diff_key($value, $array);
			}
		}
	}

	/**
	 * Apply a user function to every member of an array.
	 *
	 * @access public
	 * @param Closure $callback
	 * @param boolean $recursive
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function each(Closure $callback, $recursive = true) {
		$this->_value = Hash::each($this->_value, $callback, $recursive);

		return $this;
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
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function erase($data) {
		if ($this->_value) {
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
		return Hash::every($this->_value, $callback);
	}

	/**
	 * Extracts a value from the specified index. Accepts a dot notated path to filter down the depth.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function extract($key) {
		return Hash::get($this->_value, $key);
	}

	/**
	 * Filters elements of the array using a callback function.
	 *
	 * @access public
	 * @param Closure $callback
	 * @param boolean $recursive
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function filter(Closure $callback = null, $recursive = true) {
		$this->_value = Hash::filter($this->_value, $recursive, $callback);

		return $this;
	}

	/**
	 * Return the first element in the array.
	 *
	 * @access public
	 * @return mixed
	 */
	public function first() {
		if ($this->isNotEmpty()) {
			foreach ($this->_value as $value) {
				return $value;
			}
		}

		return null;
	}

	/**
	 * Flattens a multidimensional array into a single array.
	 *
	 * @access public
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function flatten() {
		$this->_value = Hash::flatten($this->_value);

		return $this;
	}

	/**
	 * Exchanges all keys with their associated values in the array.
	 *
	 * @access public
	 * @param boolean $recursive
	 * @param boolean $truncate
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function flip($recursive = true, $truncate = true) {
		$this->_value = Hash::flip($this->_value, $recursive, $truncate);

		return $this;
	}

	/**
	 * Empty the array.
	 *
	 * @access public
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function flush() {
		$this->_value = [];

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
	public function get($key) {
		return isset($this->_value[$key]) ? $this->_value[$key] : null;
	}

	/**
	 * Checks to see if a certain index exists. Accepts a dot notated path to filter down the depth.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return Hash::has($this->_value, $key);
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
				if ($index === $key) {
					return $count;
				}

				++$count;
			}
		}

		return -1;
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
		return !empty($this->_value);
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
	 * Return the last element in the array.
	 *
	 * @access public
	 * @return mixed
	 */
	public function last() {
		if ($this->isNotEmpty()) {
			$length = $this->length();
			$counter = 1;

			foreach ($this->_value as $value) {
				if ($counter === $length) {
					return $value;
				}

				++$counter;
			}
		}

		return null;
	}

	/**
	 * Return the length of the array.
	 *
	 * @access public
	 * @return int
	 */
	public function length() {
		return count($this->_value);
	}

	/**
	 * Applies the callback to the elements of the array.
	 *
	 * @access public
	 * @param Closure $callback
	 * @param array $data
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function map(Closure $callback, array $data = []) {
		if (empty($data)) {
			$this->_value = array_map($callback, $this->_value);
		} else {
			$this->_value = array_map($callback, $this->_value, $data);
		}

		return $this;
	}

	/**
	 * Merge an array with the current array.
	 *
	 * @access public
	 * @param array $array
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function merge(array $array) {
		$this->_value = Hash::merge($this->_value, $array);

		return $this;
	}

	/**
	 * Add a value to the beginning of the array. This will reset all numerical indices.
	 *
	 * @access public
	 * @param mixed $value
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function prepend($value) {
		if (is_array($value)) {
			foreach ($value as $v) {
				$this->prepend($v);
			}
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

		return $values[$random];
	}

	/**
	 * Iteratively reduce the array to a single value using a callback function.
	 *
	 * @access public
	 * @param Closure $callback
	 * @param mixed $initial
	 * @return int
	 */
	public function reduce(Closure $callback, $initial = null) {
		return array_reduce($this->_value, $callback, $initial);
	}

	/**
	 * Remove an index from the array. Accepts a dot notated path to drill down the dimensions.
	 *
	 * @access public
	 * @param string $key
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function remove($key) {
		$this->_value = Hash::remove($this->_value, $key);

		return $this;
	}

	/**
	 * Alias for rewind(), however returns the chainable object.
	 *
	 * @access public
	 * @return \titon\base\Map
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
	 * @return \titon\base\Map
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
	 * @return \titon\base\Map
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
	public function slice($offset, $length = null, $preserve = true) {
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
		return Hash::some($this->_value, $callback);
	}

	/**
	 * Sort the values in the array based on the supplied options.
	 *
	 * @access public
	 * @param mixed $options
	 * 		- reverse: Will reverse sort
	 * 		- preserve: Indices will be left in tact
	 * 		- flags: Sorting flags
	 * 		- callback: Closure callback to sort with
	 * 		- on: Either "keys" or "values"
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function sort($options = []) {
		if ($options instanceof Closure) {
			$options = array('callback' => $options);

		} else if (is_bool($options)) {
			$options = array('reverse' => $options);
		}

		$options = $options + array(
			'reverse' => false,
			'preserve' => true,
			'flags' => SORT_REGULAR,
			'callback' => null,
			'on' => 'values'
		);

		$flags = $options['flags'];
		$preserve = $options['preserve'];

		// Sort by callback
		if ($options['callback'] instanceof Closure) {

			// Sort keys by callback
			if ($options['on'] === 'keys') {
				uksort($this->_value, $options['callback']);

			// Sort values by callback
			} else {
				if ($preserve) {
					uasort($this->_value, $options['callback']);
				} else {
					usort($this->_value, $options['callback']);
				}
			}

		// Sort regular
		} else {

			// Sort by keys
			if ($options['on'] === 'keys') {
				ksort($this->_value, $flags);

			// Sort by values
			} else {
				if ($preserve) {
					asort($this->_value, $flags);
				} else {
					sort($this->_value, $flags);
				}
			}
		}

		// Reverse it
		if ($options['reverse']) {
			$this->_value = array_reverse($this->_value, $preserve);
		}

		return $this;
	}

	/**
	 * Sort the array using a natural algorithm. This function implements a sort algorithm that orders
	 * alphanumeric strings in the way a human being would while maintaining key/value associations.
	 *
	 * @access public
	 * @param boolean $strict
	 * @return \titon\base\Map
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
	 * Remove a portion of the array and replace it with something else; will preserve keys.
	 *
	 * @access public
	 * @param int $offset
	 * @param int $length
	 * @param array $replacement
	 * @return array
	 */
	public function splice($offset, $length, array $replacement) {
		$before = [];
		$after = [];
		$splice = [];
		$i = 0;
		$l = 0;

		foreach ($this->_value as $key => $value) {
			if ($i >= $offset && $l < $length) {
				$splice[$key] = $value;
				$l++;

			} else if ($i < $offset) {
				$before[$key] = $value;

			} else {
				$after[$key] = $value;
			}

			$i++;
		}

		$this->_value = Hash::merge($before, $replacement, $after);

		return $splice;
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
	 * @return \titon\base\Map
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
	 * Set a key/value pair within in any multi-dimensional array depth.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return \titon\base\Map
	 * @chainable
	 */
	public function set($key, $value) {
		$this->_value = Hash::set($this->_value, $key, $value);

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
		return $this->has($key);
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
			$this->set($key, $value);
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
	 * @return boolean
	 */
	public function count() {
		return $this->length();
	}

}
