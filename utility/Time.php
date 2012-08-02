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
use titon\utility\UtilityException;

/**
 * @todo
 *
 * @package	titon.utility
 */
class Time {

	/**
	 * Return a unix timestamp. If the time is a string convert it, else cast to int.
	 *
	 * @access public
	 * @param int|string $time
	 * @return int
	 * @static
	 */
	public static function toUnix($time) {
		return is_string($time) ? strtotime($time) : (int) $time;
	}

}