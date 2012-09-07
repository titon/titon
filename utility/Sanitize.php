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
		return (string) filter_var($value, FILTER_SANITIZE_EMAIL);
	}

	/**
	 * Escape a string using the apps encoding.
	 *
	 * @access public
	 * @param string $value
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
			'double' => false
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
		return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND | FILTER_FLAG_ALLOW_SCIENTIFIC);
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
		return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
	}

	/**
	 * Sanitize a string by removing excess CRLF characters.
	 *
	 * @access public
	 * @param string $value
	 * @param array $options
	 * 		cr		- (bool) Will remove carriage returns \r
	 * 		lf		- (bool) Will remove line feeds \n
	 * 		crlf	- (bool) Will remove CRLF \r\n
	 * 		limit	- (int) The start limit to remove extraneous characters
	 * 		trim	- (bool) Will remove whitespace and newlines around the edges
	 * @return string
	 * @static
	 */
	public static function newlines($value, array $options = []) {
		$options = $options + [
			'cr' => true,
			'lf' => true,
			'crlf' => true,
			'limit' => 2,
			'trim' => true
		];

		if ($options['limit']) {
			$pattern = '/(?:%s){' . $options['limit'] . ',}/u';

		} else {
			$pattern = '/(?:%s)+/u';
			$replace = '';
		}

		if ($options['crlf']) {
			$value = preg_replace(sprintf($pattern, '\r\n'), (isset($replace) ? $replace : "\r\n"), $value);
		}

		if ($options['cr']) {
			$value = preg_replace(sprintf($pattern, '\r'), (isset($replace) ? $replace : "\r"), $value);
		}

		if ($options['lf']) {
			$value = preg_replace(sprintf($pattern, '\n'), (isset($replace) ? $replace : "\n"), $value);
		}

		if ($options['trim']) {
			$value = trim($value);
		}

		return $value;
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
	 * 		trim	- (bool) Will remove whitespace and newlines around the edges
	 * @return string
	 * @static
	 */
	public static function whitespace($value, array $options = []) {
		$options = $options + [
			'space' => true,
			'tab' => true,
			'limit' => 2,
			'strip' => true,
			'trim' => true
		];

		if ($options['limit']) {
			$pattern = '/%s{' . $options['limit'] . ',}/u';

		} else {
			$pattern = '/%s+/u';
			$replace = '';
		}

		if ($options['tab']) {
			$value = preg_replace(sprintf($pattern, '\t'), (isset($replace) ? $replace : "\t"), $value);
		}

		if ($options['space']) {
			$value = preg_replace(sprintf($pattern, ' '), (isset($replace) ? $replace : ' '), $value); // \s replaces other whitespace characters
		}

		if ($options['strip']) {
			$value = str_replace(chr(0xCA), ' ', $value);
		}

		if ($options['trim']) {
			$value = trim($value);
		}

		return $value;
	}

	/**
	 * Sanitize a string by removing any XSS attack vectors.
	 * Will bubble up to html() and escape().
	 *
	 * @access public
	 * @param string $value
	 * @param array $options
	 * @return string
	 * @static
	 */
	public static function xss($value, array $options = []) {
		$options = $options + ['strip' => true];

		$value = str_replace("\0", '', $value);

		if (!$options['strip']) {

			// Remove any attribute starting with on or xmlns
			$value = preg_replace('/\s?(?:on[a-z]+|xmlns)\s?=\s?"(?:.*?)"/isu', '', $value);

			// Remove namespaced elements
			$value = preg_replace('/<\/?\w+:\w+(?:.*?)>/isu', '', $value);

			// Remove really unwanted tags
			do {
				$old = $value;
				$value = preg_replace('/<\/?(?:applet|base|bgsound|embed|frame|frameset|iframe|layer|link|meta|object|script|style|title|xml|audio|video)(?:.*?)>/isu', '', $value);
			} while ($old !== $value);
		}

		return self::html($value, $options);
	}

}