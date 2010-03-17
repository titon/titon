<?php
/**
 * Encyrption and Decryption. Applies encryption techniques and algorythms to specific strings and data.
 * Has the ability to decrypt certain strings according to the specific algorythm.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\utility;

/**
 * Encrypt Class
 *
 * @package		Titon
 * @subpackage	Titon.Utility
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
        $length = mb_strlen($string);
        $scrambled = '';

        if ($length > 0) {
            for ($i = 0; $i < $length; ++$i) {
                $scrambled .= '&#' . ord(mb_substr($string, $i, 1)) . ';';
            }
        }

        return $scrambled;
    }

}