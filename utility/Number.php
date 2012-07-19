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
 * The Number utility allows for the twiddling and calculation of numbers (non-floats) and provides support for generic integer bases.
 * Provides helper methods to easy in the evaluation of numbers within context.
 *
 * @package	titon.utility
 */
class Number {

	/**
	 * Convert a word notated form of bytes (1MB) to the numerical equivalent.
	 *
	 * @access public
	 * @param int $number
	 * @return int
	 * @static
	 */
	public static function bytes($number) {
		// @todo
	}

	/**
	 * Convert a number from one base to another.
	 *
	 * @access public
	 * @param int $no
	 * @param int $fromBase
	 * @param int $toBase
	 * @return int
	 * @static
	 */
	public static function convert($no, $fromBase, $toBase) {
		return base_convert($no, $fromBase, $toBase);
	}

	/**
	 * Convert a number to it's currency equivalent, respecting locale.
	 *
	 * @access public
	 * @param int $number
	 * @return int
	 * @static
	 */
	public static function currency($number) {
		// @todo
	}

	public static function format() {}

	/**
	 * Return the highest number out of all the arguments. If no arguments are passed, -1 will be returned.
	 *
	 * @access public
	 * @return int
	 * @static
	 */
	public static function high() {
		$high = -1;

		foreach (func_get_args() as $number) {
			if ($number > $high) {
				$high = $number;
			}
		}

		return $high;
	}

	/**
	 * Return true if the number is within the min and max.
	 *
	 * @access public
	 * @param int $number
	 * @param int $min
	 * @param int $max
	 * @return boolean
	 * @static
	 */
	public static function in($number, $min, $max) {
		return ($number >= $min && $number <= $max);
	}

	/**
	 * Is the current value even?
	 *
	 * @access public
	 * @param int $number
	 * @return boolean
	 * @static
	 */
	public static function isEven($number) {
		return ($number % 2 === 0);
	}

	/**
	 * Is the current value negative; less than zero.
	 *
	 * @access public
	 * @param int $number
	 * @return boolean
	 * @static
	 */
	public static function isNegative($number) {
		return ($number < 0);
	}

	/**
	 * Is the current value odd?
	 *
	 * @access public
	 * @param int $number
	 * @return boolean
	 * @static
	 */
	public static function isOdd($number) {
		return !self::isEven($number);
	}

	/**
	 * Is the current value positive; greater than or equal to zero.
	 *
	 * @access public
	 * @param int $number
	 * @param boolean $zero
	 * @return boolean
	 * @static
	 */
	public static function isPositive($number, $zero = true) {
		return ($zero ? ($number >= 0) : ($number > 0));
	}

	/**
	 * Limits the number between two bounds.
	 *
	 * @access public
	 * @param int $number
	 * @param int $min
	 * @param int $max
	 * @return int
	 * @static
	 */
	public static function limit($number, $min, $max) {
		return self::max(self::min($number, $min), $max);
	}

	/**
	 * Return the lowest number out of all the arguments. If no arguments are passed, -1 will be returned.
	 *
	 * @access public
	 * @return int
	 * @static
	 */
	public static function low() {
		$high = call_user_func_array(array(__CLASS__, 'high'), func_get_args());
		$low = -1;

		foreach (func_get_args() as $number) {
			if ($number < $high) {
				$low = $number;
			}
		}

		return $low;
	}

	/**
	 * Increase the number to the minimum if below threshold.
	 *
	 * @access public
	 * @param int $number
	 * @param int $min
	 * @return int
	 * @static
	 */
	public static function min($number, $min) {
		if ($number < $min) {
			$number = $min;
		}

		return $number;
	}

	/**
	 * Decrease the number to the maximum if above threshold.
	 *
	 * @access public
	 * @param int $number
	 * @param int $max
	 * @return int
	 * @static
	 */
	public static function max($number, $max) {
		if ($number > $max) {
			$number = $max;
		}

		return $number;
	}

	/**
	 * Return true if the number is outside the min and max.
	 *
	 * @access public
	 * @param int $number
	 * @param int $min
	 * @param int $max
	 * @return boolean
	 * @static
	 */
	public static function out($number, $min, $max) {
		return ($number < $min || $number > $max);
	}

	/**
	 * Convert a number to a percentage string with decimal and comma separations.
	 *
	 * @access public
	 * @param int $number
	 * @return string
	 * @static
	 */
	public static function percentage($number) {
		// @todo - different . and , for locales
		return number_format($number, 2) . '%';
	}

	/**
	 * Returns -1 if the value is negative, 0 if the value equals 0, or 1 if the value is positive.
	 *
	 * @access public
	 * @param int $number
	 * @return int
	 * @static
	 */
	public static function signum($number) {
		if ($number < 0) {
			return -1;

		} else if ($number === 0) {
			return 0;

		} else {
			return 1;
		}
	}

	/**
	 * Alias method for toInt().
	 *
	 * @access public
	 * @param int $number
	 * @return int
	 * @static
	 */
	public static function to($number) {
		return self::toInt($number);
	}

	/**
	 * Returns as an unsigned integer in base 2 (binary).
	 *
	 * @access public
	 * @param int $number
	 * @param int $base
	 * @return int
	 * @static
	 */
	public static function toBinary($number, $base = 10) {
		return self::convert($number, $base, 2);
	}

	/**
	 * Returns as a byte (or what the PHP integer representation would be).
	 *
	 * @access public
	 * @param int $number
	 * @return int
	 * @static
	 */
	public static function toByte($number) {
		return ($number << (64 - 8)) / (1 << (64 - 8));
	}

	/**
	 * Returns as an unsigned integer in base 10 (decimal).
	 *
	 * @access public
	 * @param int $number
	 * @param int $base
	 * @return int
	 * @static
	 */
	public static function toDecimal($number, $base = 10) {
		return self::convert($number, $base, 10);
	}

	/**
	 * Converts the number to a float.
	 *
	 * @access public
	 * @param int $number
	 * @return float
	 * @static
	 */
	public static function toFloat($number) {
		return (float) $number;
	}

	/**
	 * Returns as an unsigned integer in base 16 (hexadecimal).
	 *
	 * @access public
	 * @param int $number
	 * @param int $base
	 * @return string
	 * @static
	 */
	public static function toHex($number, $base = 10) {
		return self::convert($number, $base, 16);
	}

	/**
	 * Converts the number to an integer.
	 *
	 * @access public
	 * @param int $number
	 * @return int
	 * @static
	 */
	public static function toInt($number) {
		return (int) $number;
	}

	/**
	 * Returns as an unsigned integer in base 8 (octal).
	 *
	 * @access public
	 * @param int $number
	 * @param int $base
	 * @return string
	 * @static
	 */
	public static function toOctal($number, $base = 10) {
		return self::convert($number, $base, 8);
	}

}