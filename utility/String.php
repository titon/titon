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
 * Specific methods that deal with string manipulation, truncation, formation, etc.
 *
 * @package	titon.utility
 */
class String {

	/**
	 * Return the character at the specified index, if not found returns null.
	 *
	 * @access public
	 * @param string $string
	 * @param int $index
	 * @return string
	 * @static
	 */
	public static function charAt($string, $index) {
		return isset($string[$index]) ? $string[$index] : null;
	}

	/**
	 * Compares to strings alphabetically. Returns 0 if they are equal, negative if passed value is greater, or positive if current value is greater.
	 *
	 * @access public
	 * @param string $string
	 * @param string $value
	 * @param boolean $strict
	 * @param int $length
	 * @return int
	 * @static
	 */
	public static function compare($string, $value, $strict = true, $length = 0) {
		if ($strict) {
			if ($length > 0) {
				return strncmp($string, $value, (int) $length);
			}

			return strcmp($string, $value);
		}

		if ($length > 0) {
			return strncasecmp($string, $value, (int) $length);
		}

		return strcasecmp($string, $value);
	}

	/**
	 * Check to see if a string exists within this string.
	 *
	 * @access public
	 * @param string $string
	 * @param string $needle
	 * @param boolean $strict
	 * @param int $offset
	 * @return boolean
	 * @static
	 */
	public static function contains($string, $needle, $strict = true, $offset = 0) {
		return (self::indexOf($string, $needle, $strict, $offset) !== false);
	}

	/**
	 * Checks to see if the string ends with a specific value.
	 *
	 * @access public
	 * @param string $string
	 * @param string $value
	 * @return boolean
	 * @static
	 */
	public static function endsWith($string, $value) {
		return (self::extract($string, -mb_strlen($value)) === $value);
	}

	/**
	 * Extracts a portion of a string.
	 *
	 * @access public
	 * @param string $string
	 * @param int $offset
	 * @param int $length
	 * @return string
	 * @static
	 */
	public static function extract($string, $offset, $length = null) {
		return mb_substr($string, $offset, $length);
	}

	/**
	 * Generates a string of random characters.
	 *
	 * @access public
	 * @param int $length
	 * @return string
	 * @static
	 */
	public static function generate($length = 10) {
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$return = '';

		if ($length > 0) {
			$totalChars = mb_strlen($characters) - 1;

			for ($i = 0; $i <= $length; ++$i) {
				$return .= $characters[rand(0, $totalChars)];
			}
		}

		return $return;
	}

	/**
	 * Grab the index of the first matched character.
	 *
	 * @access public
	 * @param string $string
	 * @param string $needle
	 * @param boolean $strict
	 * @param int $offset
	 * @return int
	 * @static
	 */
	public static function indexOf($string, $needle, $strict = true, $offset = 0) {
		if ($strict) {
			return mb_strpos($string, $needle, $offset);
		}

		return mb_stripos($string, $needle, $offset);
	}

	/**
	 * Grab the index of the last matched character.
	 *
	 * @access public
	 * @param string $string
	 * @param string $needle
	 * @param boolean $strict
	 * @param int $offset
	 * @return int
	 * @static
	 */
	public static function lastIndexOf($string, $needle, $strict = true, $offset = 0) {
		if ($strict) {
			return mb_strrpos($string, $needle, $offset);
		}

		return mb_strripos($string, $needle, $offset);
	}

	/**
	 * Creates a comma separated list with the last item having an "and".
	 *
	 * @access public
	 * @param array $items
	 * @param string $and
	 * @param string $sep
	 * @return string
	 * @static
	 */
	public static function listing($items, $and = 'and', $sep = ', ') {
		if (is_array($items)) {
			$lastItem = array_pop($items);

			if (count($items) === 1) {
				return $lastItem;
			}

			$items = implode($sep, $items);
			$items = $items . ' ' . $and . ' ' . $lastItem;
		}

		return $items;
	}

	/**
	 * If a string is too long, shorten it in the middle.
	 *
	 * @access public
	 * @param string $string
	 * @param int $limit
	 * @return string
	 * @static
	 */
	public static function shorten($string, $limit = 25) {
		if (mb_strlen($string) > $limit) {
			$pre = self::extract($string, 0, ($limit / 2));
			$suf = self::extract($string, -($limit / 2));

			$string = $pre . ' &hellip; ' . $suf;
		}

		return $string;
	}

	/**
	 * Checks to see if the string starts with a specific value.
	 *
	 * @access public
	 * @param string $string
	 * @param string $value
	 * @return boolean
	 * @static
	 */
	public static function startsWith($string, $value) {
		return (self::extract($string, 0, strlen($value)) === $value);
	}

	/**
	 * Truncates a string to a certain length.
	 *
	 * @access public
	 * @param string $string
	 * @param int $limit
	 * @param string $suffix
	 * @return string
	 * @static
	 */
	public static function truncate($string, $limit = 25, $suffix = '&hellip;') {
		if (strlen($string) > $limit) {
			$string = strip_tags($string);
			$string = self::extract($string, 0, $limit);
			$string = self::extract($string, 0, -(mb_strlen(mb_strrchr($string, ' '))));
			$string = $string . $suffix;
		}

		return $string;
	}

}