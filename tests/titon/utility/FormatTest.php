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
use titon\utility\Format;
use titon\utility\UtilityException;

/**
 * Test class for titon\utility\Format.
 */
class FormatTest extends TestCase {

	/**
	 * Prepare G11n.
	 */
	protected function setUp() {
		parent::setUp();

		Titon::g11n()->setup('en')->setup('en-us')->setup('no')->set('en');
	}

	/**
	 * Test that atom() formats a timestamp to an Atom feed format.
	 */
	public function testAtom() {
		$time = mktime(16, 35, 0, 2, 26, 1988);

		$this->assertEquals('1988-02-26T16:35:00-05:00', Format::atom($time));
	}

	/**
	 * Test that date() formats a timestamp to a date.
	 */
	public function testDate() {
		$time = mktime(16, 35, 0, 2, 26, 1988);

		// uses en locale
		$this->assertEquals('02/26/1988', Format::date($time));
		$this->assertEquals('02/26/1988', Format::date('1988-02-26'));

		// now try with no locale, will use fallback
		Titon::g11n()->set('no');
		$this->assertEquals('Feb 26 1988', Format::date($time, '%b %d %Y'));
	}

	/**
	 * Test that datetime() formats a timestamp to a date and time.
	 */
	public function testDatetime() {
		$time = mktime(16, 35, 0, 2, 26, 1988);

		// uses en locale
		$this->assertEquals('02/26/1988 04:35PM', Format::datetime($time));
		$this->assertEquals('02/26/1988 04:35PM', Format::datetime('1988-02-26 16:35:00'));

		// now try with no locale, will use fallback
		Titon::g11n()->set('no');
		$this->assertEquals('Feb 26 1988, 04:35PM', Format::datetime($time, '%h %d %Y, %I:%M%p'));
	}

	/**
	 * Test that format() creates strings and masks with the passed number.
	 */
	public function testFormat() {
		$this->assertEquals('(123) 456', Format::format(1234567890, '(###) ###'));
		$this->assertEquals('(123) 456-7890', Format::format(1234567890, '(###) ###-####'));
		$this->assertEquals('(123) 456-####', Format::format(123456, '(###) ###-####'));

		$this->assertEquals('123.456', Format::format(1234567890, '###.###'));
		$this->assertEquals('123.456.7890', Format::format(1234567890, '###.###.####'));
		$this->assertEquals('123.456.####', Format::format(123456, '###.###.####'));

		// credit card
		$this->assertEquals('3772-3483-0461-4543', Format::format('3772348304614543', '####-####-####-####'));

		// credit card with mask
		$this->assertEquals('****-****-****-4543', Format::format('3772348304614543', '****-****-****-####'));

		// longer number
		$this->assertEquals('3772-3483-0461-4543', Format::format('377234830461454313', '####-####-####-####'));
	}

	/**
	 * Test that http() formats a timestamp to an HTTP format.
	 */
	public function testHttp() {
		$time = mktime(16, 35, 0, 2, 26, 1988);

		$this->assertEquals('Fri, 26 Feb 1988 21:35:00 GMT', Format::http($time));
	}

	/**
	 * Test that phone() formats a number to a phone number.
	 */
	public function testPhone() {

		// uses en locale
		$this->assertEquals('666-1337', Format::phone(6661337));
		$this->assertEquals('(888) 666-1337', Format::phone('8886661337'));
		$this->assertEquals('1 (888) 666-1337', Format::phone('+1 8886661337'));

		// now try with no locale, will use fallback
		Titon::g11n()->set('no');

		$this->assertEquals('666-1337', Format::phone(6661337, '###-####'));

		$this->assertEquals('888-666-1337', Format::phone(8886661337, [
			10 => '###-###-####'
		]));

		$this->assertEquals('+1-888-666-1337', Format::phone(18886661337, [
			11 => '+#-###-###-####'
		]));
	}

	/**
	 * Test that rss() formats a timestamp to an RSS feed format.
	 */
	public function testRss() {
		$time = mktime(16, 35, 0, 2, 26, 1988);

		$this->assertEquals('Fri, 26 Feb 1988 16:35:00 -0500', Format::rss($time));
	}

	/**
	 * Test that ssn() formats a number to a social security number.
	 */
	public function testSsn() {
		$ssn = '998293841';

		// uses en locale
		$this->assertEquals('998-29-3841', Format::ssn($ssn));

		// now try with no locale, will use fallback
		Titon::g11n()->set('no');
		$this->assertEquals('998.29.3841', Format::ssn($ssn, '###.##.####'));
	}

	/**
	 * Test that time() formats a timestamp to time.
	 */
	public function testTime() {
		$time = mktime(16, 35, 0, 2, 26, 1988);

		// uses en locale
		$this->assertEquals('04:35PM', Format::time($time));
		$this->assertEquals('04:35PM', Format::time('1988-02-26 16:35:00'));

		// now try with no locale, will use fallback
		Titon::g11n()->set('no');
		$this->assertEquals('04:35:00 PM', Format::time($time, '%r'));
	}

}