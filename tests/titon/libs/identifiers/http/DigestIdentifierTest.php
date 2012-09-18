<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\identifiers\http;

use titon\Titon;
use titon\tests\TestCase;
use titon\libs\identifiers\http\DigestIdentifier;

/**
 * Test class for titon\libs\identifiers\http\DigestIdentifier.
 */
class DigestIdentifierTest extends TestCase {

	/**
	 * Example digest string.
	 */
	public $digest;

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new DigestIdentifier(['realm' => 'Test Realm', 'nonce' => 'NONCE'], ['user' => 'pass']);
		$this->object->response->config->debug = true;

		$this->digest = 'Digest username="user",realm="Test Realm",nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093",uri="/",qop=auth,nc=00000001,cnonce="0a4f113b",response="6d0f023f94bc902744f63b9753cce104",opaque="5ccc069c403ebaf9f0171e9517f40e41"';
	}

	/**
	 * Test that authenticate() returns an array if successful, or null if failure.
	 */
	public function testAuthenticate() {
		$this->assertEquals(null, $this->object->authenticate());

		$_SERVER['PHP_AUTH_DIGEST'] = $this->digest;
		$this->assertEquals([
			'username' => 'user',
			'password' => 'pass'
		], $this->object->authenticate());
	}

	/**
	 * Test that login() sets the correct WWW-Authenticate header.
	 */
	public function testLogin() {
		$this->object->login();
		$this->assertEquals('Digest realm="Test Realm",qop="auth",nonce="NONCE",opaque="e8cfd71a4faf5d739029330aa5509f46"', $this->object->response->getHeader('WWW-Authenticate', false));
	}

	/**
	 * Test that logout() removes the env global.
	 */
	public function testLogout() {
		$_SERVER['PHP_AUTH_DIGEST'] = $this->digest;
		$this->assertTrue(isset($_SERVER['PHP_AUTH_DIGEST']));

		$this->object->logout();
		$this->assertFalse(isset($_SERVER['PHP_AUTH_DIGEST']));
	}

	/**
	 * Test that parseDigest() returns an array of digest info, or returns null if failure.
	 */
	public function testParseDigest() {
		$this->assertEquals([
			'username' => 'user',
			'nonce' => 'dcd98b7102dd2f0e8b11d0f600bfb0c093',
			'uri' => '/',
			'qop' => 'auth',
			'nc' => '00000001',
			'cnonce' => '0a4f113b',
			'response' => '6d0f023f94bc902744f63b9753cce104',
		], $this->object->parseDigest($this->digest));

		$this->assertEquals(null, $this->object->parseDigest('Digest username="user"'));
	}

}