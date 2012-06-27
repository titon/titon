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
 * Encyrption and Decryption. Applies encryption techniques and algorythms to specific strings and data.
 * Has the ability to decrypt certain strings according to the specific algorythm.
 *
 * @package	titon.utility
 */
class Encrypt {

	/**
	 * Scrambles the source of a string.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function obfuscate($string) {
		$length = strlen($string);
		$scrambled = '';

		if ($length > 0) {
			for ($i = 0; $i < $length; ++$i) {
				$scrambled .= '&#' . ord(substr($string, $i, 1)) . ';';
			}
		}

		return $scrambled;
	}

}