<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\utility;

use titon\utility\String;
use titon\utility\UtilityException;

/**
 * Uuid handles the creation of compatible UUID's (unique universal identifier) in all versions.
 *
 * @package	titon.utility
 */
class Uuid {

	/**
	 * Creates UUID version 1.
	 *
	 * @access public
	 * @return string
	 * @throws \titon\utility\UtilityException
	 * @static
	 */
	public static function v1() {
		throw new UtilityException('UUID version 1 has not been implemented yet.');
	}

	/**
	 * Creates UUID version 2.
	 *
	 * @access public
	 * @return string
	 * @throws \titon\utility\UtilityException
	 * @static
	 */
	public static function v2() {
		throw new UtilityException('UUID version 2 has not been implemented yet.');
	}

	/**
	 * Creates UUID version 3: md5 based.
	 *
	 * @access public
	 * @return string
	 * @throws \titon\utility\UtilityException
	 * @static
	 */
	public static function v3() {
		throw new UtilityException('UUID version 3 has not been implemented yet.');
	}

	/**
	 * Creates UUID version 4: random number generation based.
	 *
	 * @access public
	 * @return string
	 * @throws \titon\utility\UtilityException
	 * @static
	 */
	public static function v4() {
		return sprintf('%s-%s-%s%s-%s%s-%s',
			String::generate(8, String::HEX), // 1
			String::generate(4, String::HEX), // 2
			4, // 3
			String::generate(3, String::HEX), // 3
			String::generate(1, '89AB'), // 4
			String::generate(3, String::HEX), // 4
			String::generate(12, String::HEX)); // 5
	}

	/**
	 * Creates UUID version 5: sha1 based.
	 *
	 * @access public
	 * @return string
	 * @throws \titon\utility\UtilityException
	 * @static
	 */
	public static function v5() {
		throw new UtilityException('UUID version 5 has not been implemented yet.');
	}

}