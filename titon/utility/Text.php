<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\utility;

/**
 * String Manipulation. Specific methods that deal with string manipulation, truncation, formation, etc.
 *
 * @package	titon.source.utility
 */
class Text {

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
	 * Creates a comma seperated list with the last item having an "and".
	 *
	 * @access public
	 * @param array $items
	 * @param string $and
	 * @return string
	 * @static
	 */
	public static function listing($items, $and = 'and') {
		if (is_array($items)) {
			$lastItem = array_pop($items);
			$items = implode(', ', $items);
			$items = $items .' '. $and .' '. $lastItem;
		}

		return $items;
	}

	/**
	 * If a string is too long, shorten it in the middle.
	 *
	 * @access public
	 * @param string $text
	 * @param int $limit
	 * @return string
	 * @static
	 */
	public static function shorten($text, $limit = 25) {
		if (mb_strlen($text) > $limit) {
			$pre = mb_substr($text, 0, ($limit / 2));
			$suf = mb_substr($text, -($limit / 2));
			$text = $pre .' ... '. $suf;
		}

		return $text;
	}

	/**
	 * Truncates a string to a certain length.
	 *
	 * @access public
	 * @param string $text
	 * @param int $limit
	 * @param string $ending
	 * @return string
	 * @static
	 */
	public static function truncate($text, $limit = 25, $ending = '...') {
		if (mb_strlen($text) > $limit) {
			$text = strip_tags($text);
			$text = mb_substr($text, 0, $limit);
			$text = mb_substr($text, 0, -(mb_strlen(mb_strrchr($text, ' '))));
			$text = $text . $ending;
		}

		return $text;
	}

}