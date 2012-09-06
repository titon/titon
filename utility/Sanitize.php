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
 * Makes dirty values clean! Sanitize will process an input and return a safe output depending on the scope of the cleaner.
 *
 * @package	titon.utility
 */
class Sanitize {

	/**
	 * Sanitize an email by removing all characters except letters, digits and !#$%&'*+-/=?^_`{|}~@.[].
	 *
	 * @access public
	 * @param string $value
	 * @return string
	 * @static
	 */
	public static function email($value) {
		return filter_var($value, FILTER_SANITIZE_EMAIL);
	}

	/**
	 * Escape a string using the apps encoding.
	 *
	 * @access public
	 * @param string $string
	 * @param array $options
	 * 		encoding	- (string) Character encoding set; defaults to UTF-8
	 * 		flags		- (int) Encoding flags; defaults to ENT_QUOTES
	 * 		double		- (bool) Will convert existing entities
	 * @return string
	 * @static
	 */
	public static function escape($value, array $options = []) {
		$options = $options + [
			'encoding' => Titon::config()->encoding(),
			'flags' => ENT_QUOTES,
			'double' => true
		];

		return htmlentities($value, $options['flags'], $options['encoding'], $options['double']);
	}

	/**
	 * Sanitize a float by removing all characters except digits, +- and .,eE.
	 *
	 * @access public
	 * @param string $value
	 * @return string
	 * @static
	 */
	public static function float($value) {
		return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND | FILTER_FLAG_ALLOW_SCIENTIFIC);
	}

	/**
	 * Sanitize a string by removing xor escaping HTML characters and entities.
	 *
	 * @access public
	 * @param string $value
	 * @param array $options
	 * 		strip		- (bool) Will remove HTML tags
	 * 		whitelist	- (string) List of tags to not strip
	 * @return string
	 * @static
	 */
	public static function html($value, array $options = []) {
		$options = $options + [
			'strip' => true,
			'whitelist' => ''
		];

		if ($options['strip']) {
			$value = strip_tags($value, $options['whitelist']);
		}

		return self::escape($value, $options);
	}

	/**
	 * Sanitize an integer by removing all characters except digits, plus and minus sign.
	 *
	 * @access public
	 * @param string $value
	 * @return string
	 * @static
	 */
	public static function integer($value) {
		return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
	}

	/**
	 * Sanitize a string by removing excess CRLF characters.
	 *
	 * @access public
	 * @param string $value
	 * @param array $options
	 * 		cr		- (bool) Will remove carriage returns
	 * 		lf		- (bool) Will remove line feeds
	 * 		limit	- (int) The start limit to remove extraneous characters
	 * @return string
	 * @static
	 */
	public static function newlines($value, array $options = []) {
		$options = $options + [
			'cr' => true,
			'lf' => true,
			'limit' => 2
		];

		if (!$options['cr'] && !$options['lf']) {
			return $value;
		}

		$newlines = '';

		if ($options['cr']) {
			$newlines .= '\r';
		}

		if ($options['lf']) {
			$newlines .= '\n';
		}

		if ($options['limit']) {
			$pattern = sprintf('/[%s]{%s,}/u', $newlines, $options['limit']);
		} else {
			$pattern = sprintf('/[%s]+/u', $newlines);
		}

		return preg_replace($pattern, '', $value);
	}

	/**
	 * Sanitize a URL by removing all characters except letters, digits and $-_.+!*'(),{}|\\^~[]`<>#%";/?:@&=.
	 *
	 * @access public
	 * @param string $value
	 * @return string
	 * @static
	 */
	public static function url($value) {
		return filter_var($value, FILTER_SANITIZE_URL);
	}

	/**
	 * Sanitize a string by removing excess whitespace and tab characters.
	 *
	 * @access public
	 * @param string $value
	 * @param array $options
	 * 		space	- (bool) Will remove white space
	 * 		tab		- (bool) Will remove tabs
	 * 		limit	- (int) The start limit to remove extraneous characters
	 * 		strip	- (bool) Will remove non-standard white space character
	 * @return string
	 * @static
	 */
	public static function whitespace($value, array $options = []) {
		$options = $options + [
			'space' => true,
			'tab' => false,
			'limit' => 2,
			'strip' => true
		];

		if (!$options['space'] && !$options['tab']) {
			return $value;
		}

		$newlines = '';

		if ($options['space']) {
			$newlines .= '\s';
		}

		if ($options['tab']) {
			$newlines .= '\t';
		}

		if ($options['strip']) {
			$value = str_replace(chr(0xCA), '', $value);
		}

		if ($options['limit']) {
			$pattern = sprintf('/[%s]{%s,}/u', $newlines, $options['limit']);
		} else {
			$pattern = sprintf('/[%s]+/u', $newlines);
		}

		return preg_replace($pattern, '', $value);
	}

}