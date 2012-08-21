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
use titon\io\File;
use titon\utility\Number;
use titon\utility\Time;
use titon\utility\UtilityException;

/**
 * Validate provides methods for validating data against specific conditions. This should not be used to validate formatting (excluding a few),
 * but should allow validation of data before the formatting of data.
 *
 * @package	titon.utility
 */
class Validate {

	/**
	 * Validation constants.
	 */
	const IPV4 = 'ipv4';
	const IPV6 = 'ipv6';

	/**
	 * Validate input is alphabetical.
	 *
	 * @access public
	 * @param string $input
	 * @param array $exceptions
	 * @return boolean
	 * @static
	 */
	public static function alpha($input, $exceptions = []) {
		return (bool) preg_match('/^[\p{L}\s' . self::escape($exceptions) . ']+$/imU', $input);
	}

	/**
	 * Validate input is numerical and alphabetical (does not include punctuation).
	 *
	 * @access public
	 * @param string $input
	 * @param array $exceptions
	 * @return boolean
	 * @static
	 */
	public static function alphaNumeric($input, $exceptions = []) {
		return (bool) preg_match('/^[\p{L}\p{N}\p{Nd}\s' . self::escape($exceptions) . ']+$/imU', $input);
	}

	/**
	 * Validate input string length is between the min and max.
	 *
	 * @access public
	 * @param string $input
	 * @param int $max
	 * @param int $min
	 * @return boolean
	 * @static
	 */
	public static function between($input, $max = 2500, $min = 1) {
		$length = mb_strlen($input);

		return ($length <= $max && $length >= $min);
	}

	/**
	 * Validate input is a boolean or boolean-like flag.
	 *
	 * @access public
	 * @param string $input
	 * @return boolean
	 * @static
	 */
	public static function boolean($input) {
		return (bool) filter_var($input, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * Compare two numerical values.
	 *
	 * @access public
	 * @param int $input
	 * @param int $check
	 * @param string $mode
	 * @return boolean
	 * @throws \titon\utility\UtilityException
	 * @static
	 */
	public static function comparison($input, $check, $mode) {
		switch (mb_strtolower($mode)) {
			case 'greater':
			case 'gt':
			case '>':
				return ($input > $check);
			break;
			case 'greaterorequal':
			case 'gte':
			case '>=':
				return ($input >= $check);
			break;
			case 'less':
			case 'lt':
			case '<':
				return ($input < $check);
			break;
			case 'lessorequal':
			case 'lte':
			case '<=':
				return ($input <= $check);
			break;
			case 'equal':
			case '==':
			case '=':
				return ($input == $check);
			break;
			case 'notequal':
			case '!=':
				return ($input != $check);
			break;
			default:
				throw new UtilityException(sprintf('Unsupported mode %s for %s.', $mode, __METHOD__));
			break;
		}
	}

	/**
	 * Validate input is a credit card number. If $types is defined, will only validate against those cards, else will validate against all.
	 *
	 * @access public
	 * @param string $input
	 * @param string|array $types
	 * @return boolean
	 * @throws \titon\utility\UtilityException
	 * @static
	 */
	public static function creditCard($input, $types = null) {
		$input = str_replace(array('-', ' '), '', $input);

		if (mb_strlen($input) < 13) {
			return false;
		}

		$cards = [
			'amex'		=> '/^3[4|7]\\d{13}$/',
			'bankcard'	=> '/^56(10\\d\\d|022[1-5])\\d{10}$/',
			'diners'	=> '/^(?:3(0[0-5]|[68]\\d)\\d{11})|(?:5[1-5]\\d{14})$/',
			'disc'		=> '/^(?:6011|650\\d)\\d{12}$/',
			'electron'	=> '/^(?:417500|4917\\d{2}|4913\\d{2})\\d{10}$/',
			'enroute'	=> '/^2(?:014|149)\\d{11}$/',
			'jcb'		=> '/^(3\\d{4}|2100|1800)\\d{11}$/',
			'maestro'	=> '/^(?:5020|6\\d{3})\\d{12}$/',
			'mc'		=> '/^5[1-5]\\d{14}$/',
			'solo'		=> '/^(6334[5-9][0-9]|6767[0-9]{2})\\d{10}(\\d{2,3})?$/',
			'switch'	=> '/^(?:49(03(0[2-9]|3[5-9])|11(0[1-2]|7[4-9]|8[1-2])|36[0-9]{2})\\d{10}(\\d{2,3})?)|(?:564182\\d{10}(\\d{2,3})?)|(6(3(33[0-4][0-9])|759[0-9]{2})\\d{10}(\\d{2,3})?)$/',
			'visa'		=> '/^4\\d{12}(\\d{3})?$/',
			'voyager'	=> '/^8699[0-9]{11}$/'
		];

		if ($types) {
			$validate = [];

			foreach ((array) $types as $card) {
				if (isset($cards[$card])) {
					$validate[$card] = $cards[$card];
				} else {
					throw new UtilityException(sprintf('Credit card type %s does not exist.', $card));
				}
			}
		} else {
			$validate = $cards;
		}

		foreach ($validate as $pattern) {
			if (preg_match($pattern, $input)) {
				return self::luhn($input);
			}
		}

		return false;
	}

	/**
	 * Validate input matches a currency format.
	 *
	 * @access public
	 * @param string $input
	 * @return boolean
	 * @static
	 */
	public static function currency($input) {
		return self::custom($input, self::get('currency'));
	}

	/**
	 * Validate input against a custom regex pattern.
	 *
	 * @access public
	 * @param string $input
	 * @param string $expression
	 * @return boolean
	 * @static
	 */
	public static function custom($input, $expression) {
		return (bool) preg_match($expression, $input);
	}

	/**
	 * Validate input is a real date.
	 *
	 * @access public
	 * @param string $input
	 * @return boolean
	 * @static
	 */
	public static function date($input) {
		list($m, $d, $y) = explode('/', date('m/d/Y', Time::toUnix($input)));

		return checkdate($m, $d, $y);
	}

	/**
	 * Validate input is a decimal.
	 *
	 * @access public
	 * @param string $input
	 * @param int $decimals
	 * @return boolean
	 * @static
	 */
	public static function decimal($input, $decimals = 2) {
		return (filter_var($input, FILTER_VALIDATE_FLOAT, [
			'options' => ['decimals' => $decimals],
			'flags' => FILTER_FLAG_ALLOW_THOUSAND
		]) !== false);
	}

	/**
	 * Validate an images dimensions.
	 *
	 * @access public
	 * @param array $input
	 * @param string $type
	 * @param int $size
	 * @return boolean
	 * @static
	 */
	public static function dimensions($input, $type, $size) {
		if (self::file($input)) {
			$file = getimagesize($input['tmp_name']);

			if (!$file) {
				return false;
			}

			$width = $file[0];
			$height = $file[1];

			switch ($type) {
				case 'width':		return ($width == $size);
				case 'height':		return ($height == $size);
				case 'maxWidth':	return ($width <= $size);
				case 'maxHeight':	return ($height <= $size);
				case 'minWidth':	return ($width >= $size);
				case 'minHeight':	return ($height >= $size);
			}
		}

		return false;
	}

	/**
	 * Validate input is an email. If $dns is true, will check DNS records as well.
	 *
	 * @access public
	 * @param string $input
	 * @param boolean $dns
	 * @return boolean
	 * @static
	 */
	public static function email($input, $dns = true) {
		$result = (bool) filter_var($input, FILTER_VALIDATE_EMAIL);

		if (!$result) {
			return false;
		}

		if ($dns) {
			$host = trim(mb_strstr(filter_var($input, FILTER_SANITIZE_EMAIL), '@'), '@');

			if (function_exists('checkdnsrr') && checkdnsrr($host, 'MX')) {
				return true;
			}

			return is_array(gethostbynamel($host));
		}

		return $result;
	}

	/**
	 * Validate two values are equal.
	 *
	 * @access public
	 * @param string $input
	 * @param string $check
	 * @return boolean
	 * @static
	 */
	public static function equal($input, $check) {
		return ($input == $check);
	}

	/**
	 * Validate two types match exactly.
	 *
	 * @access public
	 * @param string $input
	 * @param string $check
	 * @return boolean
	 * @static
	 */
	public static function exact($input, $check) {
		return ($input === $check);
	}

	/**
	 * Escapes characters that would break the regex.
	 *
	 * @access public
	 * @param array|string $characters
	 * @return string
	 * @static
	 */
	public static function escape($characters) {
		if (is_array($characters)) {
			$characters = implode('', $characters);
		}

		return preg_quote($characters, '/');
	}

	/**
	 * Validate input has an extension and is in the whitelist.
	 *
	 * @access public
	 * @param string $input
	 * @param string|array $extensions
	 * @return boolean
	 * @static
	 */
	public static function ext($input, $extensions = ['gif', 'jpeg', 'png', 'jpg']) {
		if (isset($input['name'])) {
			$input = $input['name'];
		}

		return in_array(Titon::loader()->ext($input), (array) $extensions, true);
	}

	/**
	 * Validate input is a file upload by checking for tmp_name and verifying error.
	 *
	 * @access public
	 * @param string $input
	 * @return boolean
	 * @static
	 */
	public static function file($input) {
		return (is_array($input) && !empty($input['tmp_name']) && $input['error'] == 0);
	}

	/**
	 * Get a validation rule from G11n, else use the fallback.
	 *
	 * @access public
	 * @param string $key
	 * @param string $fallback
	 * @return string
	 * @throws \titon\utility\UtilityException
	 * @static
	 */
	public static function get($key, $fallback = null) {
		$pattern = $fallback;

		if (Titon::g11n()->isEnabled()) {
			$pattern = Titon::g11n()->current()->getValidations($key) ?: $fallback;
		}

		if (!$pattern) {
			throw new UtilityException(sprintf('Validation pattern %s does not exist.', $key));
		}

		return $pattern;
	}

	/**
	 * Validate an images height is exact.
	 *
	 * @access public
	 * @param array $input
	 * @param int $size
	 * @return boolean
	 * @static
	 */
	public static function height($input, $size = 0) {
		return self::dimensions($input, 'height', $size);
	}

	/**
	 * Validate input is in the list.
	 *
	 * @access public
	 * @param string $input
	 * @param array $list
	 * @return boolean
	 * @static
	 */
	public static function inList($input, array $list) {
		return in_array($input, $list, true);
	}

	/**
	 * Validate input is within the min and max range.
	 *
	 * @access public
	 * @param string $input
	 * @param int $max
	 * @param int $min
	 * @return boolean
	 * @static
	 */
	public static function inRange($input, $max, $min = 1) {
		return ($input <= $max && $input >= $min);
	}

	/**
	 * Validate input is an IP address. Optional $mode can be passed to flag as IP v4 or v6.
	 *
	 * @access public
	 * @param string $input
	 * @param string $mode
	 * @return boolean
	 * @static
	 */
	public static function ip($input, $mode = null) {
		if ($mode === self::IPV4) {
			$flags = FILTER_FLAG_IPV4;

		} else if ($mode === self::IPV6) {
			$flags = FILTER_FLAG_IPV6;

		} else {
			$flags = 0;
		}

		return (bool) filter_var($input, FILTER_VALIDATE_IP, ['flags' => $flags]);
	}

	/**
	 * Luhn algorithm.
	 *
	 * @access public
	 * @param string $input
	 * @return boolean
	 * @static
	 * @link http://en.wikipedia.org/wiki/Luhn_algorithm
	 */
	public static function luhn($input) {
		if ($input == 0) {
			return false;
		}

		$sum = 0;
		$length = mb_strlen($input);

		for ($position = 1 - ($length % 2); $position < $length; $position += 2) {
			$sum += $input[$position];
		}

		for ($position = ($length % 2); $position < $length; $position += 2) {
			$number = $input[$position] * 2;
			$sum += ($number < 10) ? $number : $number - 9;
		}

		return ($sum % 10 == 0);
	}

	/**
	 * Validate a files mimetype is in the whitelist.
	 *
	 * @access public
	 * @param string $input
	 * @param string|array $mimes
	 * @return boolean
	 * @static
	 */
	public static function mimeType($input, $mimes) {
		if (self::file($input)) {
			$file = new File($input['tmp_name']);

			return in_array($file->mimeType(), (array) $mimes);
		}

		return false;
	}

	/**
	 * Validate an images file size is above the minimum.
	 *
	 * @access public
	 * @param array $input
	 * @param int $size
	 * @return boolean
	 * @static
	 */
	public static function minFilesize($input, $size) {
		if (self::file($input)) {
			return ($input['size'] > Number::bytesFrom($size));
		}

		return false;
	}

	/**
	 * Validate an images height is above the minimum.
	 *
	 * @access public
	 * @param array $input
	 * @param int $size
	 * @return boolean
	 * @static
	 */
	public static function minHeight($input, $size) {
		return self::dimensions($input, 'minHeight', $size);
	}

	/**
	 * Validate input length has a minimum amount of characters.
	 *
	 * @access public
	 * @param string $input
	 * @param int $min
	 * @return boolean
	 * @static
	 */
	public static function minLength($input, $min) {
		return (mb_strlen($input) >= $min);
	}

	/**
	 * Validate an images width is above the minimum.
	 *
	 * @access public
	 * @param array $input
	 * @param int $size
	 * @return boolean
	 * @static
	 */
	public static function minWidth($input, $size) {
		return self::dimensions($input, 'minWidth', $size);
	}

	/**
	 * Validate an images file size is below the maximum.
	 *
	 * @access public
	 * @param array $input
	 * @param int $size
	 * @return boolean
	 * @static
	 */
	public static function maxFilesize($input, $size) {
		if (self::file($input)) {
			return ($input['size'] <= Number::bytesFrom($size));
		}

		return false;
	}

	/**
	 * Validate an images height is below the maximum.
	 *
	 * @access public
	 * @param array $input
	 * @param int $size
	 * @return boolean
	 * @static
	 */
	public static function maxHeight($input, $size) {
		return self::dimensions($input, 'maxHeight', $size);
	}

	/**
	 * Validate input length has a maximum amount of characters.
	 *
	 * @access public
	 * @param string $input
	 * @param int $max
	 * @return boolean
	 * @static
	 */
	public static function maxLength($input, $max) {
		return (mb_strlen($input) <= $max);
	}

	/**
	 * Validate an images width is below the maximum.
	 *
	 * @access public
	 * @param array $input
	 * @param int $size
	 * @return boolean
	 * @static
	 */
	public static function maxWidth($input, $size) {
		return self::dimensions($input, 'maxWidth', $size);
	}

	/**
	 * Validate input is not empty; zero's are not flagged as empty.
	 *
	 * @access public
	 * @param string $input
	 * @return boolean
	 * @static
	 */
	public static function notEmpty($input) {
		return (!empty($input) && $input != 0);
	}

	/**
	 * Validate input is numeric.
	 *
	 * @access public
	 * @param string $input
	 * @return boolean
	 * @static
	 */
	public static function numeric($input) {
		return is_numeric($input);
	}

	/**
	 * Validate input matches a phone number format.
	 *
	 * @access public
	 * @param string $input
	 * @return boolean
	 * @static
	 */
	public static function phone($input) {
		return self::custom($input, self::get('phone'));
	}

	/**
	 * Validate input matches a postal/zip code format.
	 *
	 * @access public
	 * @param string $input
	 * @return boolean
	 * @static
	 */
	public static function postalCode($input) {
		return self::custom($input, self::get('postalCode'));
	}

	/**
	 * Validate input matches a social security number (SSN) format.
	 *
	 * @access public
	 * @param string $input
	 * @return boolean
	 * @static
	 */
	public static function ssn($input) {
		return self::custom($input, self::get('ssn'));
	}

	/**
	 * Validate input is a UUID.
	 *
	 * @access public
	 * @param string $input
	 * @return boolean
	 * @static
	 */
	public static function uuid($input) {
		return preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $input);
	}

	/**
	 * Validate input is a URL / website address.
	 *
	 * @access public
	 * @param string $input
	 * @return boolean
	 * @static
	 */
	public static function website($input) {
		return (bool) filter_var($input, FILTER_VALIDATE_URL);
	}

	/**
	 * Validate an images width is exact.
	 *
	 * @access public
	 * @param array $input
	 * @param int $size
	 * @return boolean
	 * @static
	 */
	public static function width($input, $size) {
		return self::dimensions($input, 'width', $size);
	}

}