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
use titon\utility\Number;
use titon\utility\Time;
use titon\utility\UtilityException;

/**
 * Format provides utility methods for converting raw data to specific visual formats.
 *
 * @package	titon.utility
 */
class Format {

	/**
	 * Format a date string. If G11n is enabled, grab the format from the locale.
	 *
	 * @access public
	 * @param string|int $time
	 * @param string $format
	 * @return string
	 * @static
	 */
	public static function date($time, $format = 'm/d/Y') {
		return date(self::get('date', $format), Time::toUnix($time));
	}

	/**
	 * Format a datetime string. If G11n is enabled, grab the format from the locale.
	 *
	 * @access public
	 * @param string|int $time
	 * @param string $format
	 * @return string
	 * @static
	 */
	public static function datetime($time, $format = 'm/d/Y h:ma') {
		return date(self::get('datetime', $format), Time::toUnix($time));
	}

	/**
	 * Format a value to a certain string sequence. All #'s in the format will be replaced by the character in the same position within the sequence.
	 * All *'s will mask the character in the sequence. Large numbers should be passed as strings.
	 *
	 * {{{
	 * 		Format::format(1234567890, '(###) ###-####');				(123) 456-7890
	 * 		Format::format(1234567890123456, '****-****-####-####');	****-****-9012-3456
	 * }}}
	 *
	 * @access public
	 * @param int|string $value
	 * @param string $format
	 * @return mixed
	 * @static
	 */
	public static function format($value, $format) {
		$value = (string) $value;
		$length = mb_strlen($format);
		$result = $format;
		$pos = 0;

		for ($i = 0; $i < $length; $i++) {
			$char = $format[$i];

			if (($char === '#' || $char === '*') && isset($value[$pos])) {
				$replace = ($char === '*') ? '*' : $value[$pos];
				$result = substr_replace($result, $replace, $i, 1);
				$pos++;
			}
		}

		return $result;
	}

	/**
	 * Get a formatting rule from G11n, else use the fallback.
	 *
	 * @access public
	 * @param string $key
	 * @param string $fallback
	 * @return string
	 * @throws \titon\utility\UtilityException
	 * @static
	 */
	public static function get($key, $fallback) {
		$pattern = $fallback;

		if (Titon::g11n()->isEnabled()) {
			$pattern = Titon::g11n()->current()->getValidations($key) ?: $fallback;
		}

		if (!$pattern) {
			throw new UtilityException(sprintf('Format pattern %s does not exist.', $key));
		}

		return $pattern;
	}

	/**
	 * Format a phone number. If G11n is enabled, grab the format from the locale.
	 * A phone number can support multiple variations, depending on how many numbers are present.
	 *
	 * @access public
	 * @param int $value
	 * @param string $format
	 * @return string
	 * @static
	 */
	public static function phone($value, $format = null) {
		$formats = self::get('phone', $format);
		$value = preg_replace('/[^0-9]+/', '', $value);

		if (is_array($formats)) {
			$length = mb_strlen($value);

			if ($length >= 11) {
				$format = $formats[11];
			} else if ($length >= 10) {
				$format = $formats[10];
			} else {
				$format = $formats[7];
			}
		} else {
			$format = $formats;
		}

		return self::format($value, $format);
	}

	/**
	 * Format a social security number. If G11n is enabled, grab the format from the locale.
	 *
	 * @access public
	 * @param string|int $value
	 * @param string $format
	 * @return string
	 * @static
	 */
	public static function ssn($value, $format = null) {
		return self::format($value, self::get('ssn', $format));
	}

	/**
	 * Format a time string. If G11n is enabled, grab the format from the locale.
	 *
	 * @access public
	 * @param string|int $time
	 * @param string $format
	 * @return string
	 * @static
	 */
	public static function time($time, $format = 'h:ma') {
		return date(self::get('time', $format), Time::toUnix($time));
	}

}