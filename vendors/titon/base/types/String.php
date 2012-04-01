<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base\types;

use titon\base\types\Type;

/**
 * The String type allows for the modification and manipulation of a string as if it was an object. 
 * One can also modify the string using a series of chained method calls that sequentially alter the initial value.
 *
 * @package	titon.base.types
 */
class String extends Type {

	/**
	 * Current encoding.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_encoding = 'UTF-8';

	/**
	 * Set value and lengths, apply multibyte overloading.
	 *
	 * @access public
	 * @param string $value
	 * @param string $encoding
	 */
	public function __construct($value, $encoding = 'UTF-8') {
		parent::__construct((string) $value);

		if (function_exists('mb_detect_encoding') && empty($encoding)) {
			$encoding = mb_detect_encoding($this->_value);
		}

		if (!empty($encoding)) {
			$this->_encoding = strtoupper(str_replace(' ', '-', (string) $encoding));
		}
	}

	/**
	 * Append a string to the end of this string.
	 *
	 * @access public
	 * @param string $value
	 * @return titon\base\types\String
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
	 * @return titon\base\types\String
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
	 * @return titon\base\types\String|null
	 */
	public function charAt($index = 0) {
		return isset($this->_value[$index]) ? $this->_value[$index] : null;
	}

	/**
	 * Removes all extraneous whitespace from a string and trims it.
	 *
	 * @access public
	 * @return titon\base\types\String
	 * @chainable
	 */
	public function clean() {
		$this->_value = preg_replace('/\s+/ig', '', $this->_value);
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
		if ($strict) {
			if ($length > 0) {
				return strncmp($this->_value, $value, (int) $length);
			}

			return strcmp($this->_value, $value);
		}
		
		if ($length > 0) {
			return strncasecmp($this->_value, $value, (int) $length);
		}

		return strcasecmp($this->_value, $value);
	}

	/**
	 * Concatenate two strings and return a new string object.
	 *
	 * @access public
	 * @param string $string
	 * @param boolean $append
	 * @return titon\base\types\String
	 */
	public function concat($string, $append = true) {
		if ($append) {
			return new String($this->_value . (string) $string, $this->_encoding);
		}

		return new String((string) $string . $this->_value, $this->_encoding);
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
		return ($this->indexOf($needle, $strict, $offset) !== false);
	}

	/**
	 * Checks to see if the string ends with a specific value.
	 *
	 * @access public
	 * @param string $value
	 * @return boolean
	 */
	public function endsWith($value) {
		return ($this->extract(-strlen($value)) == $value);
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
	 * @return titon\base\types\String
	 * @chainable
	 */
	public function escape($flags = ENT_QUOTES) {
		$this->_value = htmlentities($this->_value, $flags, $this->_encoding);

		return $this;
	}

	/**
	 * Extracts a portion of a string (substring).
	 *
	 * @access public
	 * @param int $offset
	 * @param int $length
	 * @return titon\base\types\String
	 */
	public function extract($offset, $length = null) {
		if ($length !== null) {
			return substr($this->_value, (int) $offset, $length);
		}

		return substr($this->_value, (int) $offset);
	}

	/**
	 * Grab the index of the first matched character.
	 *
	 * @access public
	 * @param string $needle
	 * @param boolean $strict
	 * @param int $offset
	 * @return boolean
	 */
	public function indexOf($needle, $strict = true, $offset = 0) {
		if ($strict) {
			return strpos($this->_value, $needle, (int) $offset);
		}

		return stripos($this->_value, $needle, (int) $offset);
	}

	/**
	 * Checks to see if the trimmed value is empty.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isBlank() { 
		return (trim($this->_value) === '');
	}

	/**
	 * Checks to see if the value is empty.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isEmpty() { 
		return ($this->_value === '');
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
	 * @return boolean
	 */
	public function lastIndexOf($needle, $strict = true, $offset = 0) {
		if ($strict) {
			return strrpos($this->_value, $needle, (int) $offset);
		}

		return strripos($this->_value, $needle, (int) $offset);
	}

	/**
	 * Return the string length.
	 *
	 * @access public
	 * @param boolean $reset
	 * @return int
	 */
	public function length($reset = false) {
		if ($this->_length === null || $reset) {
			$this->_length = strlen($this->_value);
		}

		return $this->_length;
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
	 * @return titon\base\types\String
	 * @chainable
	 */
	public function pad($length, $value = ' ', $type = STR_PAD_BOTH) {
		$this->_value = str_pad($this->_value, (int) $length, (string) $value, $type);

		return $this;
	}

	/**
	 * Pad the string on the left.
	 *
	 * @access public
	 * @param int $length
	 * @param string $value
	 * @return titon\base\types\String
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
	 * @return titon\base\types\String
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
	 * @return titon\base\types\String
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
	 * @return titon\base\types\String
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
	 * @return titon\base\types\String
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
	 * @return titon\base\types\String
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
		return ($this->extract(0, strlen($value)) == $value);
	}

	/**
	 * Strips the string of its tags and anything in between them.
	 *
	 * @access public
	 * @return titon\base\types\String
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
	 * Converts a hyphenated string to a camelcased string.
	 *
	 * @access public
	 * @return titon\base\types\String
	 * @chainable
	 */
	public function toCamelCase() {
		$this->_value = str_replace(' ', '', ucwords(strtolower(str_replace(array('_', '-'), ' ', preg_replace('/[^-_A-Za-z0-9\s]+/', '', $this->_value)))));

		return $this;
	}

	/**
	 * Lower case the string.
	 *
	 * @access public
	 * @return titon\base\types\String
	 * @chainable
	 */
	public function toLowerCase() {
		$this->_value = strtolower($this->_value);

		return $this;
	}

	/**
	 * Upper case the string.
	 *
	 * @access public
	 * @return titon\base\types\String
	 * @chainable
	 */
	public function toUpperCase() {
		$this->_value = strtoupper($this->_value);

		return $this;
	}

	/**
	 * Upper case the first letter of every word.
	 *
	 * @access public
	 * @return titon\base\types\String
	 * @chainable
	 */
	public function toUpperWords() {
		$this->_value = ucwords($this->_value);

		return $this;
	}

	/**
	 * Trim the string.
	 *
	 * @access public
	 * @param string $char
	 * @return titon\base\types\String
	 * @chainable
	 */
	public function trim($char = null) {
		$this->_value = trim($this->_value, $char);

		return $this;
	}

	/**
	 * Trim the string on the left/
	 *
	 * @access public
	 * @param string $char
	 * @return titon\base\types\String
	 * @chainable
	 */
	public function trimLeft($char = null) {
		$this->_value = ltrim($this->_value, $char);

		return $this;
	}

	/**
	 * Trim the string on the right.
	 *
	 * @access public
	 * @param string $char
	 * @return titon\base\types\String
	 * @chainable
	 */
	public function trimRight($char = null) {
		$this->_value = rtrim($this->_value, $char);

		return $this;
	}

	/**
	 * Lower case the first letter of the first word.
	 *
	 * @access public
	 * @return titon\base\types\String
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