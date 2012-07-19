<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\utility;

use titon\tests\TestCase;
use titon\utility\Number;
use titon\utility\UtilityException;

/**
 * Test class for titon\utility\Number.
 */
class NumberTest extends TestCase {

	/**
	 * Test that bytesFrom() returns the numerical equivalent.
	 */
	public function testBytesFrom() {
		$this->assertEquals('1', Number::bytesFrom('1B'));
		$this->assertEquals('225', Number::bytesFrom('225B'));
		$this->assertEquals('100', Number::bytesFrom('100'));

		// kb
		$this->assertEquals('1024', Number::bytesFrom('1K'));
		$this->assertEquals('230400', Number::bytesFrom('225KB'));
		$this->assertEquals('102400', Number::bytesFrom('100KiB'));

		// mb
		$this->assertEquals('1048576', Number::bytesFrom('1M'));
		$this->assertEquals('235929600', Number::bytesFrom('225MB'));
		$this->assertEquals('104857600', Number::bytesFrom('100MiB'));

		// gb
		$this->assertEquals('1073741824', Number::bytesFrom('1G'));
		$this->assertEquals('241591910400', Number::bytesFrom('225GB'));
		$this->assertEquals('107374182400', Number::bytesFrom('100GiB'));

		// tb
		$this->assertEquals('1099511627776', Number::bytesFrom('1T'));
		$this->assertEquals('2.473901162496E+14', Number::bytesFrom('225TB'));
		$this->assertEquals('109951162777600', Number::bytesFrom('100TiB'));

		// PHPUnit blows up on higher numbers
	}

	/**
	 * Test that bytesTo() returns the string equivalent.
	 */
	public function testBytesTo() {
		$this->assertEquals('1B', Number::bytesTo('1'));
		$this->assertEquals('225.0B', Number::bytesTo('225', 1));
		$this->assertEquals('100.00B', Number::bytesTo('100', 2));

		// kb
		$this->assertEquals('1KB', Number::bytesTo('1024'));
		$this->assertEquals('225.0KB', Number::bytesTo('230400', 1));
		$this->assertEquals('100.00KB', Number::bytesTo('102400', 2));

		// mb
		$this->assertEquals('1MB', Number::bytesTo('1048576'));
		$this->assertEquals('225.0MB', Number::bytesTo('235929600', 1));
		$this->assertEquals('100.00MB', Number::bytesTo('104857600', 2));

		// gb
		$this->assertEquals('1GB', Number::bytesTo('1073741824'));
		$this->assertEquals('225.0GB', Number::bytesTo('241591910400', 1));
		$this->assertEquals('100.00GB', Number::bytesTo('107374182400', 2));

		// tb
		$this->assertEquals('1TB', Number::bytesTo('1099511627776'));
		$this->assertEquals('225.0TB', Number::bytesTo('2.473901162496E+14', 1));
		$this->assertEquals('100.00TB', Number::bytesTo('109951162777600', 2));

		// PHPUnit blows up on higher numbers
	}

	/**
	 * Test that currency() renders the money formats correctly.
	 */
	public function testCurrency() {
		$this->assertEquals('$12,345.34', Number::currency(12345.34));
		$this->assertEquals('$734.00', Number::currency(734));
		$this->assertEquals('$84,283.38', Number::currency(84283.384));
		$this->assertEquals('($3,483.23)', Number::currency(-3483.23));

		// cents
		$this->assertEquals('0.33&cent;', Number::currency(.33));
		$this->assertEquals('0.75&cent;', Number::currency(0.75));
		$this->assertEquals('(0.75&cent;)', Number::currency(-0.75));

		// options
		$this->assertEquals('USD 85 839,34', Number::currency(85839.34, [
			'use' => 'code',
			'thousands' => ' ',
			'decimals' => ','
		]));

		// formats
		$this->assertEquals('-$0.34', Number::currency(-0.34, [
			'negative' => '-#',
			'cents' => false
		]));
	}

	/**
	 * Test that format() creates strings and masks with the passed number.
	 */
	public function testFormat() {
		$this->assertEquals('(123) 456', Number::format(1234567890, '(###) ###'));
		$this->assertEquals('(123) 456-7890', Number::format(1234567890, '(###) ###-####'));
		$this->assertEquals('(123) 456-####', Number::format(123456, '(###) ###-####'));

		$this->assertEquals('123.456', Number::format(1234567890, '###.###'));
		$this->assertEquals('123.456.7890', Number::format(1234567890, '###.###.####'));
		$this->assertEquals('123.456.####', Number::format(123456, '###.###.####'));

		// credit card
		$this->assertEquals('3772-3483-0461-4543', Number::format('3772348304614543', '####-####-####-####'));

		// credit card with mask
		$this->assertEquals('****-****-****-4543', Number::format('3772348304614543', '****-****-****-####'));

		// longer number
		$this->assertEquals('3772-3483-0461-4543', Number::format('377234830461454313', '####-####-####-####'));
	}

	/**
	 * Test that in() returns true if the number is within the range.
	 */
	public function testIn() {
		$this->assertTrue(Number::in(5, 1, 10));
		$this->assertTrue(Number::in('6', 1, 10));
		$this->assertTrue(Number::in(3.3, 1, 10));
		$this->assertFalse(Number::in(11, 1, 10));
		$this->assertFalse(Number::in(0, 1, 10));
	}

	/**
	 * Test that isEven() returns true if the number is even.
	 */
	public function testIsEven() {
		$this->assertTrue(Number::isEven(2));
		$this->assertTrue(Number::isEven(88));
		$this->assertTrue(Number::isEven(62.3));
		$this->assertFalse(Number::isEven(9));
		$this->assertFalse(Number::isEven(17));
		$this->assertFalse(Number::isEven(47.9));
	}

	/**
	 * Test that isNegative() returns true if the number is below 0.
	 */
	public function testIsNegative() {
		$this->assertTrue(Number::isNegative(-1));
		$this->assertTrue(Number::isNegative(-384));
		$this->assertFalse(Number::isNegative(0));
		$this->assertFalse(Number::isNegative(34));
	}

	/**
	 * Test that isOdd() returns true if the number is odd.
	 */
	public function testIsOdd() {
		$this->assertFalse(Number::isOdd(2));
		$this->assertFalse(Number::isOdd(88));
		$this->assertFalse(Number::isOdd(62.3));
		$this->assertTrue(Number::isOdd(9));
		$this->assertTrue(Number::isOdd(17));
		$this->assertTrue(Number::isOdd(47.9));
	}

	/**
	 * Test that isPositive() returns true if the number is above or equal 0.
	 */
	public function testIsPositive() {
		$this->assertTrue(Number::isPositive(343));
		$this->assertTrue(Number::isPositive(79));
		$this->assertTrue(Number::isPositive(0));
		$this->assertFalse(Number::isPositive(0, false)); // don't include 0
		$this->assertFalse(Number::isPositive(-1));
	}

	/**
	 * Test that limit() keeps the number within the range.
	 */
	public function testLimit() {
		$this->assertEquals(100, Number::limit(125, 50, 100));
		$this->assertEquals(50, Number::limit(45, 50, 100));
		$this->assertEquals(77, Number::limit(77, 50, 100));
	}

	/**
	 * Test that min() wont allow numbers below the threshold.
	 */
	public function testMin() {
		$this->assertEquals(50, Number::min(12, 50));
		$this->assertEquals(55, Number::min(55, 50));
		$this->assertEquals(123, Number::min(123, 50));
	}

	/**
	 * Test that max() wont allow numbers below the threshold.
	 */
	public function testMax() {
		$this->assertEquals(-15, Number::max(-15, 50));
		$this->assertEquals(12, Number::max(12, 50));
		$this->assertEquals(50, Number::max(55, 50));
		$this->assertEquals(50, Number::max(123, 50));
	}

	/**
	 * Test that percentage() returns a number formatted string with a % sign.
	 */
	public function testPercentage() {
		$this->assertEquals('123%', Number::percentage(123));
		$this->assertEquals('4,546%', Number::percentage(4546));
		$this->assertEquals('92,378,453%', Number::percentage(92378453));
		$this->assertEquals('287,349,238,432%', Number::percentage('287349238432'));
		$this->assertEquals('3,843.45%', Number::percentage(3843.4450, 2));
		$this->assertEquals('93,789.34%', Number::percentage(93789.34, 2));

		// options
		$this->assertEquals('92 378 453,94%', Number::percentage(92378453.9438, [
			'thousands' => ' ',
			'decimals' => ',',
			'places' => 2
		]));
	}

	/**
	 * Test that precision() rounds the correct amount of decimal places.
	 */
	public function testPrecision() {
		$this->assertEquals('345654.00', Number::precision(345654));
		$this->assertEquals('345654.0', Number::precision(345654, 1));
		$this->assertEquals('345654', Number::precision(345654, 0));

		$this->assertEquals('7483.99', Number::precision(7483.9858));
		$this->assertEquals('7484', Number::precision(7483.9858, 1));
		$this->assertEquals('7483.986', Number::precision(7483.9858, 3));
	}

	/**
	 * Test that signum() returns -1 for negative, 0 for 0, and 1 for positive.
	 */
	public function testSignum() {
		$this->assertEquals(-1, Number::signum(-1));
		$this->assertEquals(-1, Number::signum(-34));
		$this->assertEquals(-1, Number::signum(-9459334));
		$this->assertEquals(-1, Number::signum(-342343));

		$this->assertEquals(0, Number::signum(0));
		$this->assertEquals(0, Number::signum('0'));

		$this->assertEquals(1, Number::signum(1));
		$this->assertEquals(1, Number::signum(1234));
		$this->assertEquals(1, Number::signum(5432334));
		$this->assertEquals(1, Number::signum(45454));
	}

	/**
	 * Test that toBinary() converts multiple bases to binary.
	 */
	public function testToBinary() {
		$this->assertEquals(10011010010, Number::toBinary(10011010010, Number::BINARY));
		$this->assertEquals(10011010010, Number::toBinary(2322, Number::OCTAL));
		$this->assertEquals(10011010010, Number::toBinary(1234, Number::DECIMAL));
		$this->assertEquals(10011010010, Number::toBinary('4d2', Number::HEX));
	}

	/**
	 * Test that toDecimal() converts multiple bases to decimal.
	 */
	public function testToDecimal() {
		$this->assertEquals(1234, Number::toDecimal(10011010010, Number::BINARY));
		$this->assertEquals(1234, Number::toDecimal(2322, Number::OCTAL));
		$this->assertEquals(1234, Number::toDecimal(1234, Number::DECIMAL));
		$this->assertEquals(1234, Number::toDecimal('4d2', Number::HEX));
	}

	/**
	 * Test that toHex() converts multiple bases to hexadecimal.
	 */
	public function testToHex() {
		$this->assertEquals('4d2', Number::toHex(10011010010, Number::BINARY));
		$this->assertEquals('4d2', Number::toHex(2322, Number::OCTAL));
		$this->assertEquals('4d2', Number::toHex(1234, Number::DECIMAL));
		$this->assertEquals('4d2', Number::toHex('4d2', Number::HEX));
	}

	/**
	 * Test that toOctal() converts multiple bases to octal.
	 */
	public function testToOctal() {
		$this->assertEquals(2322, Number::toOctal(10011010010, Number::BINARY));
		$this->assertEquals(2322, Number::toOctal(2322, Number::OCTAL));
		$this->assertEquals(2322, Number::toOctal(1234, Number::DECIMAL));
		$this->assertEquals(2322, Number::toOctal('4d2', Number::HEX));
	}

}