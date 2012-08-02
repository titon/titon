<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\net;

use titon\tests\TestCase;
use titon\net\Cookie;
use titon\utility\Crypt;

/**
 * Test class for titon\net\Cookie.
 */
class CookieTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$_COOKIE = [];
	}

	/**
	 * Test that set() will set cookies into the Response object.
	 */
	public function testSetAndResponse() {
		$cookie = new Cookie(['encrypt' => false]); // turn encryption off for easy testing
		$cookie->set('foo', 'bar');

		$this->assertEquals([
			'domain' => '',
			'expires' => strtotime('+1 week'),
			'path' => '/',
			'secure' => false,
			'httpOnly' => true,
			'encrypt' => false,
			'initialize' => true,
			'value' => 'czozOiJiYXIiOw=='
		], $cookie->response->getCookie('foo'));

		// with custom config
		$cookie->set('foo', 'bar', [
			'domain' => 'titon.com',
			'secure' => true
		]);

		$this->assertEquals([
			'domain' => 'titon.com',
			'expires' => strtotime('+1 week'),
			'path' => '/',
			'secure' => true,
			'httpOnly' => true,
			'encrypt' => false,
			'initialize' => true,
			'value' => 'czozOiJiYXIiOw=='
		], $cookie->response->getCookie('foo'));

		// setting array data
		$cookie->set('foo', ['foo' => 'bar']);

		$this->assertEquals([
			'domain' => '',
			'expires' => strtotime('+1 week'),
			'path' => '/',
			'secure' => false,
			'httpOnly' => true,
			'encrypt' => false,
			'initialize' => true,
			'value' => 'YToxOntzOjM6ImZvbyI7czozOiJiYXIiO30='
		], $cookie->response->getCookie('foo'));
	}

	/**
	 * Test that all methods work correctly with encryption.
	 */
	public function testAllWithEncryption() {
		$cookie = new Cookie(['encrypt' => Crypt::RIJNDAEL]);

		// setting then getting
		$cookie->set('key', 'value');
		$this->assertTrue($cookie->has('key'));
		$this->assertEquals('value', $cookie->get('key'));
		$this->assertNotEquals('value', $_COOKIE['key']);

		// getting then setting then getting
		$this->assertFalse($cookie->has('foo'));
		$this->assertEquals(null, $cookie->get('foo'));

		$cookie->set('foo', 'bar');
		$this->assertEquals('bar', $cookie->get('foo'));
		$this->assertNotEquals('bar', $_COOKIE['foo']);

		$cookie->set('foo', 'baz');
		$this->assertEquals('baz', $cookie->get('foo'));

		// removing a value
		$cookie->remove('foo');
		$this->assertEquals(null, $cookie->get('foo'));
	}

	/**
	 * Test that all methods work correctly without encryption (still uses base64).
	 */
	public function testAllWithNoEncryption() {
		$cookie = new Cookie(['encrypt' => false]);

		// setting then getting
		$cookie->set('key', 'value');
		$this->assertEquals('value', $cookie->get('key'));
		$this->assertEquals('czo1OiJ2YWx1ZSI7', $_COOKIE['key']);

		// getting then setting then getting
		$cookie->set('foo', 'bar');
		$this->assertEquals('bar', $cookie->get('foo'));
		$this->assertEquals('czozOiJiYXIiOw==', $_COOKIE['foo']);

		// removing a value
		$cookie->remove('foo');
		$this->assertEquals(null, $cookie->get('foo'));
	}

}