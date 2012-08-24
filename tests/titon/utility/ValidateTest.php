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
use titon\utility\Validate;
use titon\utility\UtilityException;
use \Exception;

/**
 * Test class for titon\utility\Validate.
 */
class ValidateTest extends TestCase {

	/**
	 * Test image path. 200x267
	 */
	public $image;

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->image = APP_TEMP . 'image.jpg';

		Titon::g11n()->setup('en')->setup('en-us')->setup('no')->set('en');
	}

	/**
	 * Test that alpha() returns true if the value is all alphabetical characters.
	 */
	public function testAlpha() {
		$this->assertTrue(Validate::alpha('ahjsNKHAShksdnASQfgd'));
		$this->assertTrue(Validate::alpha('asdnasdsd.dfsdfdfsdfs;', ['.', ';']));

		$this->assertFalse(Validate::alpha('asdnasdsd.dfsdfdfsdfs;'));
		$this->assertFalse(Validate::alpha('asdjn1803201'));
		$this->assertFalse(Validate::alpha('pkmpwij*39@0'));
	}

	/**
	 * Test that alphaNumeric() returns true if the value is numbers or characters.
	 */
	public function testAlphaNumeric() {
		$this->assertTrue(Validate::alphaNumeric('ahjsNKHAShksdnASQfgd'));
		$this->assertTrue(Validate::alphaNumeric('asdnasdsd.dfsdfdfsdfs;', ['.', ';']));
		$this->assertTrue(Validate::alphaNumeric('asdjn1803201'));

		$this->assertFalse(Validate::alphaNumeric('asdnasdsd.dfsdfdfsdfs;'));
		$this->assertFalse(Validate::alphaNumeric('pkmpwij*39@0'));
	}

	/**
	 * Test that between() returns true if a string length is within the boundaries.
	 */
	public function testBetween() {
		$this->assertTrue(Validate::between('This is just the right length', 10, 30));

		$this->assertFalse(Validate::between('This is far too long because its more than 30 characters', 10, 30));
		$this->assertFalse(Validate::between('Too short', 10, 30));
	}

	/**
	 * Test that boolean() returns true if the value is boolean-like.
	 */
	public function testBoolean() {
		$this->assertTrue(Validate::boolean(true));
		$this->assertTrue(Validate::boolean(false));
		$this->assertTrue(Validate::boolean(0));
		$this->assertTrue(Validate::boolean(1));
		$this->assertTrue(Validate::boolean('yes'));
		$this->assertTrue(Validate::boolean('off'));

		$this->assertFalse(Validate::boolean(null));
		$this->assertFalse(Validate::boolean(''));
		$this->assertFalse(Validate::boolean(123));
		$this->assertFalse(Validate::boolean('abc'));
	}

	/**
	 * Test that comparison() returns true if the value passes the expression.
	 */
	public function testComparison() {
		$this->assertTrue(Validate::comparison(15, 10, '>'));
		$this->assertFalse(Validate::comparison(5, 10, 'gt'));

		$this->assertTrue(Validate::comparison(10, 10, '>='));
		$this->assertFalse(Validate::comparison(5, 10, 'gte'));

		$this->assertTrue(Validate::comparison(5, 10, '<'));
		$this->assertFalse(Validate::comparison(15, 10, 'lt'));

		$this->assertTrue(Validate::comparison(10, 10, '<='));
		$this->assertFalse(Validate::comparison(15, 10, 'lte'));

		$this->assertTrue(Validate::comparison(10, 10, '=='));
		$this->assertFalse(Validate::comparison(15, 10, 'eq'));

		$this->assertTrue(Validate::comparison(5, 10, '!='));
		$this->assertFalse(Validate::comparison(10, 10, 'ne'));

		try {
			$this->assertTrue(Validate::comparison(10, 10, '><'));
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that creditCard() returns true if the value is a valid CC number.
	 */
	public function testCreditCard() {
		// American express
		$this->assertTrue(Validate::creditCard('377147515754475', Validate::AMERICAN_EXPRESS));
		$this->assertTrue(Validate::creditCard('375239372816422', Validate::AMERICAN_EXPRESS));
		// Bankcard
		$this->assertTrue(Validate::creditCard('5602248780118788', Validate::BANKCARD));
		$this->assertTrue(Validate::creditCard('5610631567676765', Validate::BANKCARD));
		// Diners club 14
		$this->assertTrue(Validate::creditCard('30158334709185', Validate::DINERS_CLUB));
		$this->assertTrue(Validate::creditCard('30195413721186', Validate::DINERS_CLUB));
		$this->assertTrue(Validate::creditCard('5577265786122391', Validate::DINERS_CLUB));
		$this->assertTrue(Validate::creditCard('5534061404676989', Validate::DINERS_CLUB));
		// Discover
		$this->assertTrue(Validate::creditCard('6509735979634270', Validate::DISCOVER));
		$this->assertTrue(Validate::creditCard('6011422366775856', Validate::DISCOVER));
		// enRoute
		$this->assertTrue(Validate::creditCard('214945833739665', Validate::ENROUTE));
		$this->assertTrue(Validate::creditCard('214982692491187', Validate::ENROUTE));
		// JCB
		$this->assertTrue(Validate::creditCard('180031358949367', Validate::JCB));
		$this->assertTrue(Validate::creditCard('180033802147846', Validate::JCB));
		$this->assertTrue(Validate::creditCard('3158671691305165', Validate::JCB));
		$this->assertTrue(Validate::creditCard('3528523028771093', Validate::JCB));
		// Maestro
		$this->assertTrue(Validate::creditCard('5020412965470224', Validate::MAESTRO));
		$this->assertTrue(Validate::creditCard('5020129740944022', Validate::MAESTRO));
		// Mastercard
		$this->assertTrue(Validate::creditCard('5538725892618432', Validate::MASTERCARD));
		$this->assertTrue(Validate::creditCard('5119543573129778', Validate::MASTERCARD));
		// Solo
		$this->assertTrue(Validate::creditCard('6334768185398134', Validate::SOLO_DEBIT));
		$this->assertTrue(Validate::creditCard('633487484858610484', Validate::SOLO_DEBIT));
		$this->assertTrue(Validate::creditCard('6767838565218340113', Validate::SOLO_DEBIT));
		// Switch
		$this->assertTrue(Validate::creditCard('4936295218139423', Validate::SWITCH_DEBIT));
		$this->assertTrue(Validate::creditCard('493691609704348548', Validate::SWITCH_DEBIT));
		$this->assertTrue(Validate::creditCard('4936510653566569547', Validate::SWITCH_DEBIT));
		// Visa
		$this->assertTrue(Validate::creditCard('4916933155767', Validate::VISA));
		$this->assertTrue(Validate::creditCard('4024007159672', Validate::VISA));
		$this->assertTrue(Validate::creditCard('4481007485188614', Validate::VISA));
		$this->assertTrue(Validate::creditCard('4716533372139623', Validate::VISA));
		// Visa electron
		$this->assertTrue(Validate::creditCard('4175005028142917', Validate::VISA_ELECTRON));
		// Voyager
		$this->assertTrue(Validate::creditCard('869934523596112', Validate::VOYAGER));
		$this->assertTrue(Validate::creditCard('869958670174621', Validate::VOYAGER));

		// Test multiple
		$this->assertTrue(Validate::creditCard('375239372816422', [Validate::AMERICAN_EXPRESS, Validate::VISA])); // = amex
		$this->assertFalse(Validate::creditCard('869934523596112', [Validate::AMERICAN_EXPRESS, Validate::VISA])); // = voyager

		// Test length
		$this->assertFalse(Validate::creditCard('2346533', Validate::MASTERCARD));

		// Test exception
		try {
			$this->assertTrue(Validate::creditCard('6334768185398134', 'fakeCard'));
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that currency() validates against the locales currency rule.
	 */
	public function testCurrency() {
		$this->assertTrue(Validate::currency('$1,000.00'));
		$this->assertTrue(Validate::currency('$343'));
		$this->assertTrue(Validate::currency('$193,482.33'));

		$this->assertFalse(Validate::currency('2,392.23'));
		$this->assertFalse(Validate::currency('2325'));
		$this->assertFalse(Validate::currency('$ten'));
		$this->assertFalse(Validate::currency('$1923.2'));
	}

	/**
	 * Test that custom() validates custom regex patterns.
	 */
	public function testCustom() {
		$this->assertTrue(Validate::custom('abcdef', '/^abc/'));
		$this->assertFalse(Validate::custom('abcdef', '/abc$/'));
	}

	/**
	 * Test that date() validates timestamps in any format.
	 */
	public function testDate() {
		$this->assertTrue(Validate::date('2012-05-25'));
		$this->assertTrue(Validate::date('1946-09-11 12:03:43'));
		$this->assertTrue(Validate::date('March 25th 1893'));

		$this->assertFalse(Validate::date('02-32-2011'));
		$this->assertFalse(Validate::date('May 40th 2054'));
	}

	/**
	 * Test that decimal() validates values with certain places length.
	 */
	public function testDecimal() {
		$this->assertTrue(Validate::decimal('2923.23'));
		$this->assertTrue(Validate::decimal('1454.04'));
		$this->assertTrue(Validate::decimal('849383.938', 3));
		$this->assertTrue(Validate::decimal('+1234.54321', 5));
		$this->assertTrue(Validate::decimal('+0123.45e6', 0));
		$this->assertTrue(Validate::decimal('-0123.45e6', 0));
		$this->assertTrue(Validate::decimal('0123.45e6', 0));
		$this->assertTrue(Validate::decimal(1234.56));
		$this->assertTrue(Validate::decimal(1234.01));

		$this->assertFalse(Validate::decimal('2923'));
		$this->assertFalse(Validate::decimal('1454.0'));
		$this->assertFalse(Validate::decimal('849383.74235', 3));
	}

	/**
	 * Test that dimensions() validates image dimensions.
	 */
	public function testDimensions() {
		$this->assertTrue(Validate::dimensions($this->image, 'width', 200));
		$this->assertTrue(Validate::dimensions($this->image, 'height', 267));
	}

	/**
	 * Test that email() returns true for a valid email.
	 */
	public function testEmail() {
		$this->assertTrue(Validate::email('email@titon.com', false));
		$this->assertTrue(Validate::email('email@sub.titon.com', false));
		$this->assertTrue(Validate::email('email+group@titon.com', false));
		$this->assertTrue(Validate::email('email+group@titon.co.uk', false));
		$this->assertTrue(Validate::email('email.dot@go-titon.com', false));
		$this->assertTrue(Validate::email('example@gmail.com', true));

		$this->assertFalse(Validate::email('email@titon', false));
		$this->assertFalse(Validate::email('email@titon.com.', false));
		$this->assertFalse(Validate::email('email@sub/titon.com', false));
		$this->assertFalse(Validate::email('email@sub:titon.com', false));
		$this->assertFalse(Validate::email('email@sub_titon.com', false));
		$this->assertFalse(Validate::email('email@somereallyfakedomain.com', true));
	}

	/**
	 * Test that equal() returns true if 2 values are equal, disregarding type.
	 */
	public function testEqual() {
		$this->assertTrue(Validate::equal('1', 1));
		$this->assertTrue(Validate::equal('abc', 'abc'));
		$this->assertTrue(Validate::equal(true, 1));

		$this->assertFalse(Validate::equal('1', 9));
		$this->assertFalse(Validate::equal('abc', 'foo'));
		$this->assertFalse(Validate::equal(true, false));
	}

	/**
	 * Test that exact() returns true if 2 values are exact while checking type.
	 */
	public function testExact() {
		$this->assertTrue(Validate::exact(1, 1));
		$this->assertTrue(Validate::exact('abc', 'abc'));
		$this->assertTrue(Validate::exact(true, true));

		$this->assertFalse(Validate::exact('1', 1));
		$this->assertFalse(Validate::exact(true, 1));
	}

	/**
	 * Test that ext() returns true if the file extension is within the whitelist.
	 */
	public function testExt() {
		$this->assertTrue(Validate::ext('image.gif'));
		$this->assertTrue(Validate::ext('image.jpeg'));
		$this->assertTrue(Validate::ext('doc.pdf', 'pdf'));
		$this->assertTrue(Validate::ext('web.HTML', ['html', 'xhtml']));

		$this->assertFalse(Validate::ext('image.bmp'));
		$this->assertFalse(Validate::ext('doc.doc', 'pdf'));
		$this->assertFalse(Validate::ext('web.XML', ['html', 'xhtml']));
	}

	/**
	 * Test that file() returns true if for valid file uploads.
	 */
	public function testFile() {
		$this->assertTrue(Validate::file([
			'name' => 'file1.jpg',
			'type' => 'image/jpeg',
			'tmp_name' => '/tmp/phpUkYTB5',
			'error' => 0,
			'size' => 307808
		]));

		$this->assertFalse(Validate::file('file1.jpg'));
		$this->assertFalse(Validate::file([
			'name' => 'file1.jpg',
			'type' => 'image/jpeg',
			'tmp_name' => '/tmp/phpUkYTB5',
			'error' => UPLOAD_ERR_CANT_WRITE,
			'size' => 307808
		]));
	}

	/**
	 * Test that get() returns locale Validate rules.
	 */
	public function testGet() {
		try {
			Validate::get('phone');
			$this->assertTrue(true);
		} catch (Exception $e) {
			$this->assertTrue(false);
		}

		try {
			Validate::get('fakeKey');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that height() validates the height of an image is exact.
	 */
	public function testHeight() {
		$this->assertTrue(Validate::height($this->image, 267));
		$this->assertFalse(Validate::height($this->image, 233));
	}

	/**
	 * Test that inList() returns true if the value is in the whitelist.
	 */
	public function testInList() {
		$this->assertTrue(Validate::inList(1, [1, '1', 'c']));
		$this->assertTrue(Validate::inList('foo', ['foo', 'BAR', 'wtf']));

		$this->assertFalse(Validate::inList('b', [1, '1', 'c']));
		$this->assertFalse(Validate::inList('test', ['foo', 'BAR', 'wtf']));
	}

	/**
	 * Test that inRange() returns true if the number is within the defined range.
	 */
	public function testInRange() {
		$this->assertTrue(Validate::inRange(20, 10, 30));

		$this->assertFalse(Validate::inRange(35, 10, 30));
		$this->assertFalse(Validate::inRange(5, 10, 30));
	}

	/**
	 * Test that ip() validates v4 and v6 IPs.
	 */
	public function testIp() {
		// both v4 and v6
		$this->assertTrue(Validate::ip('0.0.0.0'));
		$this->assertTrue(Validate::ip('192.168.1.156'));
		$this->assertTrue(Validate::ip('255.255.255.255'));
		$this->assertTrue(Validate::ip('2001:0db8:85a3:0000:0000:8a2e:0370:7334'));
		$this->assertFalse(Validate::ip('127.0.0'));
		$this->assertFalse(Validate::ip('127.0.0.a'));
		$this->assertFalse(Validate::ip('127.0.0.256'));
		$this->assertFalse(Validate::ip('2001:db8:85a3::8a2e:37023:7334'));

		// v4
		$this->assertTrue(Validate::ip('0.0.0.0', Validate::IPV4));
		$this->assertTrue(Validate::ip('192.168.1.156', Validate::IPV4));
		$this->assertTrue(Validate::ip('255.255.255.255', Validate::IPV4));
		$this->assertFalse(Validate::ip('127.0.0', Validate::IPV4));
		$this->assertFalse(Validate::ip('127.0.0.a', Validate::IPV4));
		$this->assertFalse(Validate::ip('127.0.0.256', Validate::IPV4));
		$this->assertFalse(Validate::ip('2001:0db8:85a3:0000:0000:8a2e:0370:7334', Validate::IPV4));

		// v6
		$this->assertTrue(Validate::ip('2001:0db8:85a3:0000:0000:8a2e:0370:7334', Validate::IPV6));
		$this->assertTrue(Validate::ip('2001:db8:85a3:0:0:8a2e:370:7334', Validate::IPV6));
		$this->assertTrue(Validate::ip('2001:db8:85a3::8a2e:370:7334', Validate::IPV6));
		$this->assertTrue(Validate::ip('2001:0db8:0000:0000:0000:0000:1428:57ab', Validate::IPV6));
		$this->assertTrue(Validate::ip('2001:0db8:0000:0000:0000::1428:57ab', Validate::IPV6));
		$this->assertTrue(Validate::ip('2001:0db8:0:0:0:0:1428:57ab', Validate::IPV6));
		$this->assertTrue(Validate::ip('2001:0db8:0:0::1428:57ab', Validate::IPV6));
		$this->assertTrue(Validate::ip('2001:0db8::1428:57ab', Validate::IPV6));
		$this->assertTrue(Validate::ip('2001:db8::1428:57ab', Validate::IPV6));
		$this->assertTrue(Validate::ip('0000:0000:0000:0000:0000:0000:0000:0001', Validate::IPV6));
		$this->assertTrue(Validate::ip('::1', Validate::IPV6));
		$this->assertTrue(Validate::ip('::ffff:12.34.56.78', Validate::IPV6));
		$this->assertTrue(Validate::ip('::ffff:0c22:384e', Validate::IPV6));
		$this->assertTrue(Validate::ip('2001:0db8:1234:0000:0000:0000:0000:0000', Validate::IPV6));
		$this->assertTrue(Validate::ip('2001:0db8:1234:ffff:ffff:ffff:ffff:ffff', Validate::IPV6));
		$this->assertTrue(Validate::ip('2001:db8:a::123', Validate::IPV6));
		$this->assertTrue(Validate::ip('fe80::', Validate::IPV6));
		$this->assertTrue(Validate::ip('::ffff:192.0.2.128', Validate::IPV6));
		$this->assertTrue(Validate::ip('::ffff:c000:280', Validate::IPV6));
		$this->assertFalse(Validate::ip('123', Validate::IPV6));
		$this->assertFalse(Validate::ip('ldkfj', Validate::IPV6));
		$this->assertFalse(Validate::ip('2001::FFD3::57ab', Validate::IPV6));
		$this->assertFalse(Validate::ip('2001:db8:85a3::8a2e:37023:7334', Validate::IPV6));
		$this->assertFalse(Validate::ip('2001:db8:85a3::8a2e:370k:7334', Validate::IPV6));
		$this->assertFalse(Validate::ip('1:2:3:4:5:6:7:8:9', Validate::IPV6));
		$this->assertFalse(Validate::ip('1::2::3', Validate::IPV6));
		$this->assertFalse(Validate::ip('1:::3:4:5', Validate::IPV6));
		$this->assertFalse(Validate::ip('1:2:3::4:5:6:7:8:9', Validate::IPV6));
		$this->assertFalse(Validate::ip('::ffff:2.3.4', Validate::IPV6));
		$this->assertFalse(Validate::ip('::ffff:257.1.2.3', Validate::IPV6));
		$this->assertFalse(Validate::ip('255.255.255.255', Validate::IPV6));
	}

	/**
	 * Test that luhn() returns true if a number passes the luhn algorithm.
	 */
	public function testLuhn() {
		$this->assertTrue(Validate::luhn('370482756063980')); // American Express
		$this->assertTrue(Validate::luhn('5610745867413420')); // BankCard
		$this->assertTrue(Validate::luhn('30155483651028')); // Diners Club 14
		$this->assertTrue(Validate::luhn('36747701998969')); // 2004 MasterCard/Diners Club Alliance International 14
		$this->assertTrue(Validate::luhn('5597511346169950')); // 2004 MasterCard/Diners Club Alliance US & Canada 16
		$this->assertTrue(Validate::luhn('6011802876467237')); // Discover
		$this->assertTrue(Validate::luhn('201496944158937')); // enRoute
		$this->assertTrue(Validate::luhn('210034762247893')); // JCB 15 digit
		$this->assertTrue(Validate::luhn('3096806857839939')); // JCB 16 digit
		$this->assertTrue(Validate::luhn('5020147409985219')); // Maestro (debit card)
		$this->assertTrue(Validate::luhn('5580424361774366')); // Mastercard
		$this->assertTrue(Validate::luhn('6767432107064987')); // Solo 16
		$this->assertTrue(Validate::luhn('676714834398858593')); // Solo 18
		$this->assertTrue(Validate::luhn('6767838565218340113')); // Solo 19
		$this->assertTrue(Validate::luhn('5641829171515733')); // Switch 16
		$this->assertTrue(Validate::luhn('493622764224625174')); // Switch 18
		$this->assertTrue(Validate::luhn('6759603460617628716')); // Switch 19
		$this->assertTrue(Validate::luhn('4024007174754')); // VISA 13 digit
		$this->assertTrue(Validate::luhn('4916375389940009')); // VISA 16 digit
		$this->assertTrue(Validate::luhn('4175003346287100')); // Visa Electron
		$this->assertTrue(Validate::luhn('869940697287073')); // Voyager

		$this->assertFalse(Validate::luhn('0000000000000000'));
		$this->assertFalse(Validate::luhn('869940697287173'));
	}

	/**
	 * Test that mimeType() returns true if the mimetype of a file is within the whitelist.
	 */
	public function testMimeType() {
		$this->assertTrue(Validate::mimeType($this->image, ['image/jpeg', 'image/jpg']));
		$this->assertFalse(Validate::mimeType($this->image, ['image/gif']));
	}

	/**
	 * Test that minFilesize() returns true if the files size meets the minimum requirement.
	 */
	public function testMinFilesize() {
		$this->assertTrue(Validate::minFilesize($this->image, 13437));
		$this->assertTrue(Validate::minFilesize($this->image, 10000));
		$this->assertFalse(Validate::minFilesize($this->image, 15000));
		$this->assertFalse(Validate::minFilesize($this->image, 13458));
	}

	/**
	 * Test that minHeight() returns true if the files height meets the minimum requirement.
	 */
	public function testMinHeight() {
		$this->assertTrue(Validate::minHeight($this->image, 267));
		$this->assertTrue(Validate::minHeight($this->image, 255));
		$this->assertFalse(Validate::minHeight($this->image, 300));
		$this->assertFalse(Validate::minHeight($this->image, 268));
	}

	/**
	 * Test that minLength() returns true if the strings length meets the minimum requirement.
	 */
	public function testMinLength() {
		$this->assertTrue(Validate::minLength('This string is enough', 20));
		$this->assertFalse(Validate::minLength('This is too short', 20));
	}

	/**
	 * Test that minWidth() returns true if the files width meets the minimum requirement.
	 */
	public function testMinWidth() {
		$this->assertTrue(Validate::minWidth($this->image, 200));
		$this->assertTrue(Validate::minWidth($this->image, 155));
		$this->assertFalse(Validate::minWidth($this->image, 215));
		$this->assertFalse(Validate::minWidth($this->image, 355));
	}

	/**
	 * Test that maxFilesize() returns true if the files size meets the maximum requirement.
	 */
	public function testMaxFilesize() {
		$this->assertTrue(Validate::maxFilesize($this->image, 13437));
		$this->assertTrue(Validate::maxFilesize($this->image, 15000));
		$this->assertFalse(Validate::maxFilesize($this->image, 13000));
		$this->assertFalse(Validate::maxFilesize($this->image, 12233));
	}

	/**
	 * Test that maxHeight() returns true if the files height meets the maximum requirement.
	 */
	public function testMaxHeight() {
		$this->assertTrue(Validate::maxHeight($this->image, 267));
		$this->assertTrue(Validate::maxHeight($this->image, 300));
		$this->assertFalse(Validate::maxHeight($this->image, 265));
		$this->assertFalse(Validate::maxHeight($this->image, 144));
	}

	/**
	 * Test that maxLength() returns true if the strings length meets the maximum requirement.
	 */
	public function testMaxLength() {
		$this->assertTrue(Validate::maxLength('This is just right', 20));
		$this->assertFalse(Validate::maxLength('This is too far too long', 20));
	}

	/**
	 * Test that maxWidth() returns true if the files width meets the maximum requirement.
	 */
	public function testMaxWidth() {
		$this->assertTrue(Validate::maxWidth($this->image, 200));
		$this->assertTrue(Validate::maxWidth($this->image, 255));
		$this->assertFalse(Validate::maxWidth($this->image, 100));
		$this->assertFalse(Validate::maxWidth($this->image, 199));
	}

	/**
	 * Test that notEmpty() returns true if a value isn't empty (allows zeros).
	 */
	public function testNotEmpty() {
		$this->assertTrue(Validate::notEmpty('abc'));
		$this->assertTrue(Validate::notEmpty(123));
		$this->assertTrue(Validate::notEmpty(['foo', 'bar']));
		$this->assertTrue(Validate::notEmpty(true));
		$this->assertTrue(Validate::notEmpty(0));
		$this->assertTrue(Validate::notEmpty('0'));

		$this->assertFalse(Validate::notEmpty(''));
		$this->assertFalse(Validate::notEmpty(false));
		$this->assertFalse(Validate::notEmpty(null));
	}

	/**
	 * Test that numeric() returns true if a value is numeric.
	 */
	public function testNumeric() {
		$this->assertTrue(Validate::numeric('1234'));
		$this->assertTrue(Validate::numeric(456));
		$this->assertFalse(Validate::numeric('abc34f'));
	}

	/**
	 * Test that phone() validates against the locales phone rule.
	 */
	public function testPhone() {
		$this->assertTrue(Validate::phone('666-1337'));
		$this->assertTrue(Validate::phone('(888)666-1337'));
		$this->assertTrue(Validate::phone('(888) 666-1337'));
		$this->assertTrue(Validate::phone('1 (888) 666-1337'));
		$this->assertTrue(Validate::phone('+1 (888) 666-1337'));

		$this->assertFalse(Validate::phone('666.1337'));
		$this->assertFalse(Validate::phone('888-666.1337'));
		$this->assertFalse(Validate::phone('1 888.666.1337'));
		$this->assertFalse(Validate::phone('666-ABMS'));
	}

	/**
	 * Test that postalCode() validates against the locales postal code rule.
	 */
	public function testPostalCode() {
		$this->assertTrue(Validate::postalCode('38842'));
		$this->assertTrue(Validate::postalCode('38842-0384'));

		$this->assertFalse(Validate::postalCode('3842'));
		$this->assertFalse(Validate::postalCode('38842.0384'));
		$this->assertFalse(Validate::postalCode('AksiS-0384'));
	}

	/**
	 * Test that ssn() validates against the locales ssn rule.
	 */
	public function testSsn() {
		$this->assertTrue(Validate::ssn('666-10-1337'));
		$this->assertTrue(Validate::ssn('384-29-3481'));

		$this->assertFalse(Validate::ssn('66-10-1337'));
		$this->assertFalse(Validate::ssn('384-29-341'));
		$this->assertFalse(Validate::ssn('666.10.1337'));
		$this->assertFalse(Validate::ssn('AHE-29-34P1'));
	}

	/**
	 * Test that uuid() validates a v4 UUID.
	 */
	public function testUuid() {
		$this->assertTrue(Validate::uuid('a8293fde-ce92-9abe-83de-7294ab29cd03'));

		$this->assertFalse(Validate::uuid('a8293fde-ce92-83de-7294ab29cd03'));
		$this->assertFalse(Validate::uuid('a8293de-ce92-9abe-83de-7294ab29cd'));
		$this->assertFalse(Validate::uuid('a8293kq-ce92-9abe-83de-729pdu29cd03'));
	}

	/**
	 * Test that url() returns true for valid website URLs.
	 */
	public function testUrl() {
		$this->assertTrue(Validate::url('http://titon'));
		$this->assertTrue(Validate::url('http://titon.com'));
		$this->assertTrue(Validate::url('http://titon.com?query=string'));
		$this->assertTrue(Validate::url('http://titon.com?query=string&key=value'));
		$this->assertTrue(Validate::url('http://titon.com?query=string&key=value#fragment'));
		$this->assertTrue(Validate::url('http://titon.com:8080?query=string&key=value#fragment'));
		$this->assertTrue(Validate::url('http://sub.titon.com:8080?query=string&key=value#fragment'));
		$this->assertTrue(Validate::url('https://sub.titon.com:8080?query=string&key=value#fragment'));
		$this->assertTrue(Validate::url('http://titon.com/some/path'));
		$this->assertTrue(Validate::url('http://go-titon.com'));
		$this->assertTrue(Validate::url('http://127.29.12.34/some/path'));
		$this->assertTrue(Validate::url('ftp://user:password@titon.com:22'));
		$this->assertTrue(Validate::url('ftp://127.29.12.34'));
		$this->assertTrue(Validate::url('ftp://127.29.12.34/some/path'));

		$this->assertFalse(Validate::url('http://go_titon.com'));
		$this->assertFalse(Validate::url('titon.com'));
		$this->assertFalse(Validate::url('www.titon.com'));
	}

	/**
	 * Test that width() validates the width of an image is exact.
	 */
	public function testWidth() {
		$this->assertTrue(Validate::width($this->image, 200));
		$this->assertFalse(Validate::width($this->image, 100));
	}

}