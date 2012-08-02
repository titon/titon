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

/**
 * The Number utility allows for the twiddling and calculation of numbers and floats.
 * Provides helper methods to ease in the evaluation of numbers within context.
 *
 * @package	titon.utility
 */
class Number {

	/**
	 * Bases.
	 */
	const BINARY = 2;
	const OCTAL = 8;
	const DECIMAL = 10;
	const HEX = 16;

	/**
	 * Convert a readable string notated form of bytes (1KB) to the numerical equivalent (1024).
	 * Supports all the different format variations: k, kb, ki, kib, etc.
	 *
	 * @access public
	 * @param int $number
	 * @return int
	 * @static
	 */
	public static function bytesFrom($number) {
		$number = trim((string) $number);
		$sizes = [
			'k|kb|ki|kib' => 10,
			'm|mb|mi|mib' => 20,
			'g|gb|gi|gib' => 30,
			't|tb|ti|tib' => 40,
			'p|pb|pi|pib' => 50,
			'e|eb|ei|eib' => 60,
			'z|zb|zi|zib' => 70,
			'y|yb|yi|yib' => 80,
			'b' => 0
		];

		foreach ($sizes as $format => $pow) {
			if (preg_match('/^([0-9\.]+)(' . $format . ')?$/i', $number, $matches)) {
				$size = (float) $matches[1];

				if (empty($matches[2])) {
					return $size;
				}

				return ($size * pow(2, $pow));
			}
		}

		return $number;
	}

	/**
	 * Convert a numerical value to the readable string notated equivalent.
	 *
	 * @access public
	 * @param int $size
	 * @param int $precision
	 * @return string
	 * @static
	 */
	public static function bytesTo($size, $precision = 0) {
		$sizes = ['YB', 'ZB', 'EB', 'PB', 'TB', 'GB', 'MB', 'KB', 'B'];
		$total = count($sizes);

		while ($total-- && $size >= 1024) {
			$size /= 1024;
		}

		return self::precision($size, $precision) . $sizes[$total];
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
		if ($fromBase == $toBase) {
			return $no;
		}

		return base_convert($no, $fromBase, $toBase);
	}

	/**
	 * Convert a number to it's currency equivalent, respecting locale.
	 * Allow for overrides through an options array.
	 *
	 * @access public
	 * @param int $number
	 * @param array $options
	 * @return string
	 * @static
	 */
	public static function currency($number, array $options = []) {
		$defaults = [
			'thousands' => ',',
			'decimals' => '.',
			'places' => 2,
			'code' => 'USD #',
			'dollar' => '$#',
			'cents' => '#&cent;',
			'use' => 'dollar',
			'negative' => '(#)'
		];

		// Localization support
		if (Titon::g11n()->isEnabled()) {
			$defaults = array_merge($defaults,
				Titon::g11n()->current()->getFormats('number'),
				Titon::g11n()->current()->getFormats('currency'));
		}

		$options = $options + $defaults;
		$amount = number_format(self::precision(abs($number), $options['places']), $options['places'], $options['decimals'], $options['thousands']);

		// Cents
		if (($number < 1 && $number > -1) && $options['cents']) {
			$amount = str_replace('#', $amount, $options['cents']);

		// Dollars
		} else {
			if ($options['use'] === 'dollar') {
				$amount = str_replace('#', $amount, $options['dollar']);
			} else {
				$amount = str_replace('#', $amount, $options['code']);
			}
		}

		// Negative
		if ($number < 0 && $options['negative']) {
			$amount = str_replace('#', $amount, $options['negative']);
		}

		return $amount;
	}

	/**
	 * Format a number to a certain string sequence. All #'s in the format will be replaced by the number in the same position within the sequence.
	 * All *'s will mask the number in the sequence. Large numbers should be passed as strings.
	 *
	 * {{{
	 * 		Number::format(1234567890, '(###) ###-####');				(123) 456-7890
	 * 		Number::format(1234567890123456, '****-****-####-####');	****-****-9012-3456
	 * }}}
	 *
	 * @access public
	 * @param int|string $number
	 * @param string $format
	 * @return mixed
	 * @static
	 */
	public static function format($number, $format) {
		$number = (string) $number;
		$length = mb_strlen($format);
		$result = $format;
		$pos = 0;

		for ($i = 0; $i < $length; $i++) {
			$char = $format[$i];

			if (($char === '#' || $char === '*') && isset($number[$pos])) {
				$replace = ($char === '*') ? '*' : $number[$pos];
				$result = substr_replace($result, $replace, $i, 1);
				$pos++;
			}
		}

		return $result;
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
	 * @param int|array $options
	 * @return string
	 * @static
	 */
	public static function percentage($number, $options = []) {
		if (is_numeric($options)) {
			$options = ['places' => $options];
		}

		$defaults = [
			'thousands' => ',',
			'decimals' => '.',
			'places' => 2
		];

		// Localization support
		if (Titon::g11n()->isEnabled()) {
			$defaults = array_merge($defaults, Titon::g11n()->current()->getFormats('number'));
		}

		$options = (array) $options + $defaults;

		return number_format(self::precision($number, $options['places']), $options['places'], $options['decimals'], $options['thousands']) . '%';
	}

	/**
	 * Formats a number with a level of precision (even if it had none).
	 *
	 * @access public
	 * @param float $number
	 * @param int $precision
	 * @return float
	 * @static
	 */
	public static function precision($number, $precision = 2) {
		return sprintf('%01.' . $precision . 'F', $number);
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

		} else if ($number == 0) {
			return 0;

		} else {
			return 1;
		}
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
	public static function toBinary($number, $base = self::DECIMAL) {
		return self::convert($number, $base, self::BINARY);
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
	public static function toDecimal($number, $base = self::DECIMAL) {
		return self::convert($number, $base, self::DECIMAL);
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
	public static function toHex($number, $base = self::DECIMAL) {
		return self::convert($number, $base, self::HEX);
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
	public static function toOctal($number, $base = self::DECIMAL) {
		return self::convert($number, $base, self::OCTAL);
	}

}