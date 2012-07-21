<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\utility;

use titon\Titon;
use titon\utility\UtilityException;

/**
 * Specific methods that deal with string manipulation, truncation, formation, etc.
 *
 * @package	titon.utility
 */
class String {

	/**
	 * Generator types.
	 */
	const ALPHA = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const ALPHA_LOWER = 'abcdefghijklmnopqrstuvwxyz';
	const ALPHA_UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const NUMERIC = '0123456789';
	const NUMERIC_NOZERO = '123456789';
	const NUMERIC_EVEN = '02468';
	const NUMERIC_ODD = '13579';
	const ALNUM = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	const HEX = '0123456789abcdef';

	/**
	 * UUID versions.
	 */
	const UUID_1 = 1;
	const UUID_2 = 2;
	const UUID_3 = 3;
	const UUID_4 = 4;
	const UUID_5 = 5;

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
		$string = (string) $string;
		$value = (string) $value;

		if ($strict) {
			if ($length > 0) {
				return strncmp($string, $value, $length);
			}

			return strcmp($string, $value);
		}

		if ($length > 0) {
			return strncasecmp($string, $value, $length);
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
	 * @param string $needle
	 * @param boolean $strict
	 * @return boolean
	 * @static
	 */
	public static function endsWith($string, $needle, $strict = true) {
		$end = self::extract($string, -mb_strlen($needle));

		if ($strict) {
			return ($end === $needle);
		}

		return (mb_strtolower($end) === mb_strtolower($needle));
	}

	/**
	 * Escape a string using the apps encoding.
	 *
	 * @access public
	 * @param string $string
	 * @param int $flags
	 * @return string
	 * @static
	 */
	public static function escape($string, $flags = ENT_QUOTES) {
		return htmlspecialchars($string, $flags, Titon::config()->encoding());
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
		if ($length) {
			return mb_substr($string, $offset, $length);
		}

		return mb_substr($string, $offset);
	}

	/**
	 * Generates a string of random characters.
	 *
	 * @access public
	 * @param int $length
	 * @param string $seed
	 * @return string
	 * @static
	 */
	public static function generate($length, $seed = self::ALNUM) {
		$return = '';
		$seed = (string) $seed;
		$totalChars = mb_strlen($seed) - 1;

		for ($i = 0; $i < $length; ++$i) {
			$return .= $seed[rand(0, $totalChars)];
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
	 * Insert values into a string defined by an array of key tokens.
	 *
	 * @access public
	 * @param string $string
	 * @param array $data
	 * @param array $options
	 * @return string
	 * @static
	 */
	public static function insert($string, array $data, array $options = []) {
		$options = $options + [
			'before' => '{',
			'after' => '}',
			'escape' => true
		];

		foreach ($data as $key => $value) {
			$string = str_replace($options['before'] . $key . $options['after'], $value, $string);
		}

		if ($options['escape']) {
			$string = self::escape($string);
		}

		return $string;
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
	 * Creates a comma separated list with the last item having an ampersand prefixing it.
	 *
	 * @access public
	 * @param array $items
	 * @param string $glue
	 * @param string $sep
	 * @return string
	 * @static
	 */
	public static function listing($items, $glue = ' &amp; ', $sep = ', ') {
		if (is_array($items)) {
			$lastItem = array_pop($items);

			if (count($items) === 0) {
				return $lastItem;
			}

			$items = implode($sep, $items);
			$items = $items . $glue . $lastItem;
		}

		return $items;
	}

	/**
	 * If a string is too long, shorten it in the middle while also respecting whitespace and preserving words.
	 *
	 * @access public
	 * @param string $string
	 * @param int $limit
	 * @param string $glue
	 * @return string
	 * @static
	 */
	public static function shorten($string, $limit = 25, $glue = ' &hellip; ') {
		if (mb_strlen($string) > $limit) {
			$width = round($limit / 2);

			// prefix
			$pre = mb_substr($string, 0, $width);

			if (mb_substr($pre, -1) !== ' ' && ($i = self::lastIndexOf($pre, ' '))) {
				$pre = mb_substr($pre, 0, $i);
			}

			// suffix
			$suf = mb_substr($string, -$width);

			if (mb_substr($suf, 0, 1) !== ' ' && ($i = self::indexOf($suf, ' '))) {
				$suf = mb_substr($suf, $i);
			}

			return trim($pre) . $glue . trim($suf);
		}

		return $string;
	}

	/**
	 * Checks to see if the string starts with a specific value.
	 *
	 * @access public
	 * @param string $string
	 * @param string $needle
	 * @param boolean $strict
	 * @return boolean
	 * @static
	 */
	public static function startsWith($string, $needle, $strict = true) {
		$start = self::extract($string, 0, mb_strlen($needle));

		if ($strict) {
			return ($start === $needle);
		}

		return (mb_strtolower($start) === mb_strtolower($needle));
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
		if (mb_strlen($string) > $limit) {
			$string = strip_tags($string);
			$string = self::extract($string, 0, $limit);
			$string = self::extract($string, 0, -(mb_strlen(mb_strrchr($string, ' '))));
			$string = $string . $suffix;
		}

		// @todo

		return $string;
	}

	/**
	 * @todo
	 *
	 * @access public
	 * @param int $version
	 * @return string
	 * @throws titon\utility\UtilityException
	 * @static
	 */
	public static function uuid($version = self::UUID_4) {
		switch ($version) {
			case self::UUID_4:
			case self::UUID_5:
				$uuid = sprintf('%s-%s-%s%s-%s%s-%s',
					self::generate(8, self::HEX), // 1
					self::generate(4, self::HEX), // 2
					$version, // 3
					self::generate(3, self::HEX), // 3
					self::generate(1, '89AB'), // 4
					self::generate(3, self::HEX), // 4
					self::generate(12, self::HEX)); // 5
			break;
			default:
				throw new UtilityException('This UUID version has not been implemented yet.');
			break;
		}

		return $uuid;
	}

}