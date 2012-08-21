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
use \DateTime;
use \DateTimeZone;

/**
 * Time provides functionality for calculating date and time ranges and making timezones easy to use.
 *
 * @package	titon.utility
 */
class Time {

	/**
	 * Time constants represented as seconds.
	 */
	const WEEK = 604800;
	const DAY = 86400;
	const HOUR = 3600;
	const MINUTE = 60;

	/**
	 * Return a DateTime object based on the current time and timezone.
	 *
	 * @access public
	 * @param string|int $time
	 * @param string $timezone
	 * @return \DateTime
	 * @static
	 */
	public static function factory($time = null, $timezone = null) {
		return new DateTime(self::toUnix($time), self::timezone($timezone));
	}

	/**
	 * Returns true if date passed is today.
	 *
	 * @access public
	 * @param mixed $time
	 * @return boolean
	 * @static
	 */
	public static function isToday($time) {
		return (date('Ymd', self::toUnix($time)) === date('Ymd'));
	}

	/**
	 * Returns true if date passed is within this week.
	 *
	 * @access public
	 * @param mixed $time
	 * @return boolean
	 * @static
	 */
	public static function isThisWeek($time) {
		return (date('Wo', self::toUnix($time)) === date('Wo'));
	}

	/**
	 * Returns true if date passed is within this month.
	 *
	 * @access public
	 * @param mixed $time
	 * @return boolean
	 * @static
	 */
	public static function isThisMonth($time) {
		return (date('mY', self::toUnix($time)) === date('mY'));
	}

	/**
	 * Returns true if date passed is within this year.
	 *
	 * @access public
	 * @param mixed $time
	 * @return boolean
	 * @static
	 */
	public static function isThisYear($time) {
		return (date('Y', self::toUnix($time)) === date('Y'));
	}

	/**
	 * Returns true if date passed is tomorrow.
	 *
	 * @access public
	 * @param mixed $time
	 * @return boolean
	 * @static
	 */
	public static function isTomorrow($time) {
		return (date('Ymd', self::toUnix($time)) === date('Ymd', strtotime('tomorrow')));
	}

	/**
	 * Returns true if the date passed will be within the next timeframe span.
	 *
	 * @access public
	 * @param mixed $time
	 * @param int $span
	 * @return boolean
	 * static
	 */
	public static function isWithinNext($time, $span) {
		$time = self::toUnix($time);
		$span = self::toUnix($span);

		return ($time < $span && $time > time());
	}

	/**
	 * Return a DateTimeZone object based on the current timezone.
	 *
	 * @access public
	 * @param string $timezone
	 * @return \DateTimeZone
	 * @static
	 */
	public static function timezone($timezone = null) {
		if (!$timezone) {
			$timezone = date_default_timezone_get();
		}

		return new DateTimeZone($timezone);
	}

	/**
	 * Return a unix timestamp. If the time is a string convert it, else cast to int.
	 *
	 * @access public
	 * @param int|string $time
	 * @return int
	 * @static
	 */
	public static function toUnix($time) {
		if (!$time) {
			return time();

		} else if ($time instanceof DateTime) {
			$time = $time->format('U');
		}

		return is_string($time) ? strtotime($time) : (int) $time;
	}

	/**
	 * Returns true if date passed was within last week.
	 *
	 * @access public
	 * @param mixed $time
	 * @return boolean
	 * @static
	 */
	public static function wasLastWeek($time) {
		return (date('Wo', self::toUnix($time)) === date('Wo', strtotime('last week')));
	}

	/**
	 * Returns true if date passed was within last month.
	 *
	 * @access public
	 * @param mixed $time
	 * @return boolean
	 * @static
	 */
	public static function wasLastMonth($time) {
		return (date('mY', self::toUnix($time)) === date('mY', strtotime('last month')));
	}

	/**
	 * Returns true if date passed was within last year.
	 *
	 * @access public
	 * @param mixed $time
	 * @return boolean
	 * @static
	 */
	public static function wasLastYear($time) {
		return (date('Y', self::toUnix($time)) === date('Y', strtotime('last year')));
	}

	/**
	 * Returns true if date passed was yesterday.
	 *
	 * @access public
	 * @param mixed $time
	 * @return boolean
	 * @static
	 */
	public static function wasYesterday($time) {
		return (date('Ymd', self::toUnix($time)) === date('Ymd', strtotime('yesterday')));
	}

	/**
	 * Returns true if the date passed was within the last timeframe span.
	 *
	 * @access public
	 * @param mixed $time
	 * @param int $span
	 * @return boolean
	 * static
	 */
	public static function wasWithinLast($time, $span) {
		$time = self::toUnix($time);
		$span = self::toUnix($span);

		return ($time > $span && $time < time());
	}

}