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
 * Crypt provides methods for encrypting and decrypting data using popular algorithms like Rijndael, Blowfish and DES.
 * Also provides convenience methods for basic hashing and obfuscation that utilizes configuration settings like salts.
 *
 * @package	titon.utility
 * @link	http://php.net/manual/book.mcrypt.php
 */
class Crypt {

	/**
	 * Default ciphers.
	 */
	const BLOWFISH = MCRYPT_BLOWFISH;
	const DES = MCRYPT_DES;
	const RIJNDAEL = MCRYPT_RIJNDAEL_128; // AES compliant
	const TRIPLEDES = MCRYPT_3DES;

	/**
	 * Operations.
	 */
	const DECRYPT = MCRYPT_DECRYPT;
	const ENCRYPT = MCRYPT_ENCRYPT;

	/**
	 * Framework salt.
	 */
	const SALT = 'dDFUMG4gZlI0bTNXMFJr';

	/**
	 * Encrypt and decrypt a string using the Blowfish algorithm with CBC mode.
	 *
	 * @access public
	 * @param string $string
	 * @param string $key
	 * @param int $operation
	 * @return string
	 * @static
	 */
	public static function blowfish($string, $key, $operation = self::ENCRYPT) {
		if ($operation === self::ENCRYPT) {
			return self::encrypt($string, $key, self::BLOWFISH);
		} else {
			return self::decrypt($string, $key, self::BLOWFISH);
		}
	}

	/**
	 * Decrypt an encrypted string using the passed cipher and mode.
	 * Additional types of ciphers and modes can be used that aren't constants of this class.
	 *
	 * @access public
	 * @param string $string
	 * @param string $key
	 * @param string $cipher
	 * @param string $mode
	 * @return string
	 * @static
	 */
	public static function decrypt($string, $key, $cipher, $mode = MCRYPT_MODE_CBC) {
		list($key, $iv) = self::_prepare($key, $cipher, $mode);

		return rtrim(mcrypt_decrypt($cipher, $key, $string, $mode, $iv), "\0");
	}

	/**
	 * Encrypt and decrypt a string using the DES (data encryption standard) algorithm with CBC mode.
	 *
	 * @access public
	 * @param string $string
	 * @param string $key
	 * @param int $operation
	 * @return string
	 * @static
	 */
	public static function des($string, $key, $operation = self::ENCRYPT) {
		if ($operation === self::ENCRYPT) {
			return self::encrypt($string, $key, self::DES);
		} else {
			return self::decrypt($string, $key, self::DES);
		}
	}

	/**
	 * Encrypt string using the passed cipher and mode.
	 * Additional types of ciphers and modes can be used that aren't constants of this class.
	 *
	 * @access public
	 * @param string $string
	 * @param string $key
	 * @param string $cipher
	 * @param string $mode
	 * @return string
	 * @static
	 */
	public static function encrypt($string, $key, $cipher, $mode = MCRYPT_MODE_CBC) {
		list($key, $iv) = self::_prepare($key, $cipher, $mode);

		return mcrypt_encrypt($cipher, $key, $string, $mode, $iv);
	}

	/**
	 * Return a hashed string using one of the built in ciphers (md5, sha1, sha256, etc) and use the config salt if it has been set.
	 * Can also supply an optional second salt for increased security.
	 *
	 * @access public
	 * @param string $cipher
	 * @param string $string
	 * @param string $salt
	 * @return string
	 * @static
	 */
	public static function hash($cipher, $string, $salt = self::SALT) {
		return hash_hmac($cipher, $string, $salt . Titon::config()->salt());
	}

	/**
	 * Scrambles the source of a string.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function obfuscate($string) {
		$string = (string) $string;
		$length = mb_strlen($string);
		$scrambled = '';

		if ($length > 0) {
			for ($i = 0; $i < $length; $i++) {
				$scrambled .= '&#' . ord($string[$i]) . ';';
			}
		}

		return $scrambled;
	}

	/**
	 * Encrypt and decrypt a string using the Rijndael 128 algorithm with CBC mode.
	 *
	 * @access public
	 * @param string $string
	 * @param string $key
	 * @param int $operation
	 * @return string
	 * @static
	 */
	public static function rijndael($string, $key, $operation = self::ENCRYPT) {
		if ($operation === self::ENCRYPT) {
			return self::encrypt($string, $key, self::RIJNDAEL);
		} else {
			return self::decrypt($string, $key, self::RIJNDAEL);
		}
	}

	/**
	 * Encrypt and decrypt a string using the 3DES (triple data encryption standard) algorithm with CBC mode.
	 *
	 * @access public
	 * @param string $string
	 * @param string $key
	 * @param int $operation
	 * @return string
	 * @static
	 */
	public static function tripledes($string, $key, $operation = self::ENCRYPT) {
		if ($operation === self::ENCRYPT) {
			return self::encrypt($string, $key, self::TRIPLEDES);
		} else {
			return self::decrypt($string, $key, self::TRIPLEDES);
		}
	}

	/**
	 * Prepare for en/decryption by generating persistent keys and IVs.
	 *
	 * @access public
	 * @param string $key
	 * @param string $cipher
	 * @param string $mode
	 * @return array
	 * @static
	 */
	protected static function _prepare($key, $cipher, $mode) {
		$keySize = mcrypt_get_key_size($cipher, $mode);
		$key = str_pad(self::hash('md5', $key), $keySize, mb_substr($cipher, -1), STR_PAD_BOTH);

		if (mb_strlen($key) > $keySize) {
			$key = mb_substr($key, 0, $keySize);
		}

		$ivSize = mcrypt_get_iv_size($cipher, $mode);
		$iv = str_pad(self::hash('md5', $key, $mode), $ivSize, mb_substr($cipher, 0, 1), STR_PAD_BOTH);

		if (mb_strlen($iv) > $ivSize) {
			$iv = mb_substr($iv, 0, $ivSize);
		}

		return array($key, $iv);
	}

}