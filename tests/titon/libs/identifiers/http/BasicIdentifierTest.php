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
use titon\libs\identifiers\http\BasicIdentifier;

/**
 * Test class for titon\libs\identifiers\http\BasicIdentifier.
 */
class BasicIdentifierTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new BasicIdentifier(['realm' => 'Test Realm'], ['user' => 'pass']);
		$this->object->response->config->debug = true;
	}

	/**
	 * Test that authenticate() returns an array if successful, or null if failure.
	 */
	public function testAuthenticate() {
		$this->assertEquals(null, $this->object->authenticate());

		$_SERVER['PHP_AUTH_USER'] = 'user';
		$this->assertEquals(null, $this->object->authenticate());

		$_SERVER['PHP_AUTH_PW'] = 'pass';
		$this->assertEquals([
			'username' => 'user',
			'password' => '08190fa3f21905adf96d01567c31cdd8'
		], $this->object->authenticate());
	}

	/**
	 * Test that login() sets the correct WWW-Authenticate header.
	 */
	public function testLogin() {
		$this->object->login();
		$this->assertEquals('Basic realm="Test Realm"', $this->object->response->getHeader('WWW-Authenticate', false));
	}

	/**
	 * Test that logout() removes the env global.
	 */
	public function testLogout() {
		$_SERVER['PHP_AUTH_USER'] = 'user';
		$this->assertTrue(isset($_SERVER['PHP_AUTH_USER']));

		$this->object->logout();
		$this->assertFalse(isset($_SERVER['PHP_AUTH_USER']));
	}

}