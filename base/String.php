<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base;

use titon\Titon;
use titon\base\Type;
use titon\utility\Inflector;
use titon\utility\String as Str;
use titon\utility\Sanitize;

/**
 * The String type allows for the modification and manipulation of a string as if it was an object.
 * One can also modify the string using a series of chained method calls that sequentially alter the initial value.
 *
 * @package	titon.base
 */
class String extends Type {

	/**
	 * Set string value.
	 *
	 * @access public
	 * @param string $value
	 */
	public function __construct($value = '') {
		parent::__construct((string) $value);
	}

	/**
	 * Append a string to the end of this string.
	 *
	 * @access public
	 * @param string $value
	 * @return \titon\base\String
	 * @chainable
	 */
	public function append($value) {
		$this->_value .= (string) $value;

		return $this;
	}

	/**
	 * Upper case the first letter of the first word.
	 *
	 * @access public
	 * @return \titon\base\String
	 * @chainable
	 */
	public function capitalize() {
		$this->_value = ucfirst($this->_value);

		return $this;
	}

	/**
	 * Return the character at the specified index, if not found returns null.
	 *
	 * @access public
	 * @param int $index
	 * @return string
	 */
	public function charAt($index) {
		return Str::charAt($this->_value, $index);
	}

	/**
	 * Removes all extraneous whitespace from a string and trims it.
	 *
	 * @access public
	 * @return \titon\base\String
	 * @chainable
	 */
	public function clean() {
		$this->_value = preg_replace('/\s{2,}+/', ' ', $this->_value);
		$this->trim();

		return $this;
	}

	/**
	 * Compares to strings alphabetically.
	 * Returns 0 if they are equal, negative if passed value is greater, or positive if current value is greater.
	 *
	 * @access public
	 * @param string $value
	 * @param boolean $strict
	 * @param int $length
	 * @return int
	 */
	public function compare($value, $strict = true, $length = 0) {
		return Str::compare($this->_value, $value, $strict, $length);
	}

	/**
	 * Concatenate two strings and return a new string object.
	 *
	 * @access public
	 * @param string $string
	 * @param boolean $append
	 * @return \titon\base\String
	 */
	public function concat($string, $append = true) {
		if ($append) {
			return new String($this->_value . (string) $string);
		}

		return new String((string) $string . $this->_value);
	}

	/**
	 * Check to see if a string exists within this string.
	 *
	 * @access public
	 * @param string $needle
	 * @param boolean $strict
	 * @param int $offset
	 * @return boolean
	 */
	public function contains($needle, $strict = true, $offset = 0) {
		return Str::contains($this->_value, $needle, $strict, $offset);
	}

	/**
	 * Checks to see if the string ends with a specific value.
	 *
	 * @access public
	 * @param string $value
	 * @return boolean
	 */
	public function endsWith($value) {
		return Str::endsWith($this->_value, $value);
	}

	/**
	 * Checks to see if both values are equal.
	 *
	 * @access public
	 * @param string $value
	 * @return boolean
	 */
	public function equals($value) {
		return ($this->_value === $value);
	}

	/**
	 * Escape the string.
	 *
	 * @access public
	 * @param int $flags
	 * @return \titon\base\String
	 * @chainable
	 */
	public function escape($flags = ENT_QUOTES) {
		$this->_value = Sanitize::escape($this->_value, ['flags' => $flags]);

		return $this;
	}

	/**
	 * Extracts a portion of a string (substring).
	 *
	 * @access public
	 * @param int $offset
	 * @param int $length
	 * @return string
	 */
	public function extract($offset, $length = null) {
		return Str::extract($this->_value, $offset, $length);
	}

	/**
	 * Grab the index of the first matched character.
	 *
	 * @access public
	 * @param string $needle
	 * @param boolean $strict
	 * @param int $offset
	 * @return int
	 */
	public function indexOf($needle, $strict = true, $offset = 0) {
		return Str::indexOf($this->_value, $needle, $strict, $offset);
	}

	/**
	 * Checks to see if the value is empty.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isBlank() {
		return ($this->_value === '');
	}

	/**
	 * Checks to see if the trimmed value is empty.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isEmpty() {
		return (trim($this->_value) === '');
	}

	/**
	 * Checks to see if the trimmed value is not empty.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isNotBlank() {
		return !$this->isBlank();
	}

	/**
	 * Checks to see if the value is not empty.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isNotEmpty() {
		return !$this->isEmpty();
	}

	/**
	 * Grab the index of the last matched character.
	 *
	 * @access public
	 * @param string $needle
	 * @param boolean $strict
	 * @param int $offset
	 * @return int
	 */
	public function lastIndexOf($needle, $strict = true, $offset = 0) {
		return Str::lastIndexOf($this->_value, $needle, $strict, $offset);
	}

	/**
	 * Return the string length.
	 *
	 * @access public
	 * @return int
	 */
	public function length() {
		return mb_strlen($this->_value);
	}

	/**
	 * Perform a regex pattern match.
	 *
	 * @access public
	 * @param string $pattern
	 * @param boolean $return
	 * @param int $flags
	 * @return int|array
	 */
	public function matches($pattern, $return = false, $flags = 0) {
		$regex = preg_match($pattern, $this->_value, $matches, $flags);

		return ($return) ? $matches : $regex;
	}

	/**
	 * Pad the string with a defined character for a specific length.
	 *
	 * @access public
	 * @param int $length
	 * @param string $value
	 * @param int $type
	 * @return \titon\base\String
	 * @chainable
	 */
	public function pad($length, $value = ' ', $type = STR_PAD_BOTH) {
		$this->_value = str_pad($this->_value, $length, $value, $type);

		return $this;
	}

	/**
	 * Pad the string on the left.
	 *
	 * @access public
	 * @param int $length
	 * @param string $value
	 * @return \titon\base\String
	 * @chainable
	 */
	public function padLeft($length, $value = ' ') {
		return $this->pad($length, $value, STR_PAD_LEFT);
	}

	/**
	 * Pad the string on the right.
	 *
	 * @access public
	 * @param int $length
	 * @param string $value
	 * @return \titon\base\String
	 * @chainable
	 */
	public function padRight($length, $value = ' ') {
		return $this->pad($length, $value, STR_PAD_RIGHT);
	}

	/**
	 * Prepend a string to the beginning of this string.
	 *
	 * @access public
	 * @param string $value
	 * @return \titon\base\String
	 * @chainable
	 */
	public function prepend($value) {
		$this->_value = (string) $value . $this->_value;

		return $this;
	}

	/**
	 * Replace specific values with a new value.
	 *
	 * @access public
	 * @param string|array $search
	 * @param string|array $replace
	 * @param boolean $strict
	 * @return \titon\base\String
	 * @chainable
	 */
	public function replace($search, $replace, $strict = true) {
		if ($strict) {
			$this->_value = str_replace($search, $replace, $this->_value);
		} else {
			$this->_value = str_ireplace($search, $replace, $this->_value);
		}

		return $this;
	}

	/**
	 * Reverse the string.
	 *
	 * @access public
	 * @return \titon\base\String
	 * @chainable
	 */
	public function reverse() {
		$this->_value = strrev($this->_value);

		return $this;
	}

	/**
	 * Shuffle the string.
	 *
	 * @access public
	 * @return \titon\base\String
	 * @chainable
	 */
	public function shuffle() {
		$this->_value = str_shuffle($this->_value);

		return $this;
	}

	/**
	 * Checks to see if the string starts with a specific value.
	 *
	 * @access public
	 * @param string $value
	 * @return boolean
	 */
	public function startsWith($value) {
		return Str::startsWith($this->_value, $value);
	}

	/**
	 * Overwrite the current value.
	 *
	 * @access public
	 * @param string $value
	 * @return String
	 */
	public function set($value) {
		$this->_value = (string) $value;

		return $this;
	}

	/**
	 * Strips the string of its tags and anything in between them.
	 *
	 * @access public
	 * @return \titon\base\String
	 * @chainable
	 */
	public function strip() {
		$this->_value = strip_tags($this->_value);

		return $this;
	}

	/**
	 * Split the string.
	 *
	 * @access public
	 * @param string $delimiter
	 * @param int $length
	 * @return array
	 */
	public function split($delimiter = null, $length = null) {
		if ($delimiter !== null) {
			if ($length !== null) {
				return explode((string) $delimiter, $this->_value, $length);
			} else {
				return explode((string) $delimiter, $this->_value);
			}
		}

		if (!$length) {
			$length = 1;
		}

		return str_split($this->_value, (int) $length);
	}

	/**
	 * Converts the string to a camel case form.
	 *
	 * @access public
	 * @return \titon\base\String
	 * @chainable
	 */
	public function toCamelCase() {
		$this->_value = Inflector::camelCase($this->_value);

		return $this;
	}

	/**
	 * Lower case the string.
	 *
	 * @access public
	 * @return \titon\base\String
	 * @chainable
	 */
	public function toLowerCase() {
		$this->_value = mb_strtolower($this->_value);

		return $this;
	}

	/**
	 * Upper case the string.
	 *
	 * @access public
	 * @return \titon\base\String
	 * @chainable
	 */
	public function toUpperCase() {
		$this->_value = mb_strtoupper($this->_value);

		return $this;
	}

	/**
	 * Upper case the first letter of every word.
	 *
	 * @access public
	 * @return \titon\base\String
	 * @chainable
	 */
	public function toUpperWords() {
		$this->_value = mb_convert_case($this->_value, MB_CASE_TITLE);

		return $this;
	}

	/**
	 * Trim the string.
	 *
	 * @access public
	 * @param string $char
	 * @return \titon\base\String
	 * @chainable
	 */
	public function trim($char = null) {
		if ($char) {
			$this->_value = trim($this->_value, $char);
		} else {
			$this->_value = trim($this->_value);
		}

		return $this;
	}

	/**
	 * Trim the string on the left/
	 *
	 * @access public
	 * @param string $char
	 * @return \titon\base\String
	 * @chainable
	 */
	public function trimLeft($char = null) {
		if ($char) {
			$this->_value = ltrim($this->_value, $char);
		} else {
			$this->_value = ltrim($this->_value);
		}

		return $this;
	}

	/**
	 * Trim the string on the right.
	 *
	 * @access public
	 * @param string $char
	 * @return \titon\base\String
	 * @chainable
	 */
	public function trimRight($char = null) {
		if ($char) {
			$this->_value = rtrim($this->_value, $char);
		} else {
			$this->_value = rtrim($this->_value);
		}

		return $this;
	}

	/**
	 * Lower case the first letter of the first word.
	 *
	 * @access public
	 * @return \titon\base\String
	 * @chainable
	 */
	public function uncapitalize() {
		$this->_value = lcfirst($this->_value);

		return $this;
	}

	/**
	 * Count how many words exist within the string.
	 *
	 * @access public
	 * @param string $inherit
	 * @return int
	 */
	public function wordCount($inherit = '') {
		return str_word_count($this->_value, 0, $inherit);
	}

}