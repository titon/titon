<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\utility;

use titon\Titon;
use titon\tests\TestCase;
use titon\utility\Time;
use titon\utility\UtilityException;
use \Exception;
use \DateTime;

/**
 * Test class for titon\utility\Time.
 */
class TimeTest extends TestCase {

	/**
	 * Test that difference() returns the difference of seconds between 2 dates.
	 */
	public function testDifference() {
		$this->assertEquals(-60, Time::difference(null, '+60 seconds'));
		$this->assertEquals(-3600, Time::difference(new DateTime(), '+1 hour'));
		$this->assertEquals(14400, Time::difference('+5 hours', '+1 hour'));
	}

	/**
	 * Test that factory() returns a DateTime object.
	 */
	public function testFactory() {
		$this->assertInstanceOf('\DateTime', Time::factory());
	}

	/**
	 * Test that isToday() returns true if the passed date is today.
	 */
	public function testIsToday() {
		$this->assertTrue(Time::isToday('+0 day'));
		$this->assertFalse(Time::isToday('+1 day'));
		$this->assertFalse(Time::isToday('-1 day'));
	}

	/**
	 * Test that isThisWeek() returns true if the passed date is this week.
	 */
	public function testIsThisWeek() {
		$this->assertTrue(Time::isThisWeek('+0 week'));
		$this->assertFalse(Time::isThisWeek('+1 week'));
		$this->assertFalse(Time::isThisWeek('-1 week'));
	}

	/**
	 * Test that isThisMonth() returns true if the passed date is this month.
	 */
	public function testIsThisMonth() {
		$this->assertTrue(Time::isThisMonth('+0 month'));
		$this->assertFalse(Time::isThisMonth('+1 month'));
		$this->assertFalse(Time::isThisMonth('-1 month'));
	}

	/**
	 * Test that isThisYear() returns true if the passed date is this year.
	 */
	public function testIsThisYear() {
		$this->assertTrue(Time::isThisYear('+0 year'));
		$this->assertFalse(Time::isThisYear('+1 year'));
		$this->assertFalse(Time::isThisYear('-1 year'));
	}

	/**
	 * Test that isTomorrow() returns true if the passed date is tomorrow
	 */
	public function testIsTomorrow() {
		$this->assertTrue(Time::isTomorrow('+1 day'));
		$this->assertFalse(Time::isTomorrow('+0 day'));
		$this->assertFalse(Time::isTomorrow('-1 day'));
	}

	/**
	 * Test that isWithinNext() returns true if the passed date is within the next time span.
	 */
	public function testIsWithinNext() {
		$this->assertTrue(Time::isWithinNext('+1 day', '+7 days'));
		$this->assertTrue(Time::isWithinNext('+37 hours', '+7 days'));

		$this->assertFalse(Time::isWithinNext('-1 day', '+7 days'));
		$this->assertFalse(Time::isWithinNext('+8 days', '+7 days'));
		$this->assertFalse(Time::isWithinNext('+1 week', '+7 days'));
	}

	/**
	 * Test that timezone() returns a DateTimeZone object.
	 */
	public function testTimezone() {
		$this->assertInstanceOf('\DateTimeZone', Time::timezone());
	}

	/**
	 * Test that toUnix() returns any time of argument as a unix timestamp.
	 */
	public function testToUnix() {
		$this->assertTrue(is_numeric(Time::toUnix(null)));
		$this->assertTrue(is_numeric(Time::toUnix(time())));
		$this->assertTrue(is_numeric(Time::toUnix('+1 week')));
		$this->assertTrue(is_numeric(Time::toUnix(new DateTime())));

		$this->assertFalse(is_numeric(Time::toUnix('string')));
	}

	/**
	 * Test that wasLastWeek() returns true if the passed date was last week.
	 */
	public function testWasLastWeek() {
		$this->assertTrue(Time::wasLastWeek('-1 week'));
		$this->assertFalse(Time::wasLastWeek('+1 week'));
		$this->assertFalse(Time::wasLastWeek('+0 week'));
	}

	/**
	 * Test that wasLastMonth() returns true if the passed date was last month.
	 */
	public function testWasLastMonth() {
		$this->assertTrue(Time::wasLastMonth('-1 month'));
		$this->assertFalse(Time::wasLastMonth('+1 month'));
		$this->assertFalse(Time::wasLastMonth('+0 month'));
	}

	/**
	 * Test that wasLastYear() returns true if the passed date was last year.
	 */
	public function testWasLastYear() {
		$this->assertTrue(Time::wasLastYear('-1 year'));
		$this->assertFalse(Time::wasLastYear('+1 year'));
		$this->assertFalse(Time::wasLastYear('+0 year'));
	}

	/**
	 * Test that wasYesterday() returns true if the passed date was yesterday.
	 */
	public function testWasYesterday() {
		$this->assertTrue(Time::wasYesterday('-1 day'));
		$this->assertFalse(Time::wasYesterday('+0 day'));
		$this->assertFalse(Time::wasYesterday('+1 day'));
	}

	/**
	 * Test that wasWithinLast() returns true if the passed date was within the last time span.
	 */
	public function testWasWithinLast() {
		$this->assertTrue(Time::wasWithinLast('-1 day', '-7 days'));
		$this->assertTrue(Time::wasWithinLast('-37 hours', '-7 days'));

		$this->assertFalse(Time::wasWithinLast('+1 day', '-7 days'));
		$this->assertFalse(Time::wasWithinLast('-8 days', '-7 days'));
		$this->assertFalse(Time::wasWithinLast('-1 week', '-7 days'));
	}

}