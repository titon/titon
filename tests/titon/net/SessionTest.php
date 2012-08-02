<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\net;

use titon\Titon;
use titon\tests\TestCase;
use titon\net\Session;

/**
 * Test class for titon\net\Session.
 */
class SessionTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$_SERVER['HTTP_USER_AGENT'] = 'browser';

		Titon::router()->initialize();
	}

	/**
	 * Test that initialize() applies session ini settings.
	 */
	public function testInitialize() {
		$this->assertEquals('PHPSESSID', ini_get('session.name'));
		$this->assertEquals(0, ini_get('session.cookie_lifetime'));
		$this->assertEquals(false, ini_get('session.cookie_secure'));
		$this->assertEquals('', ini_get('session.cookie_domain'));
		$this->assertEquals('', ini_get('session.referer_check'));

		$session = new Session([
			'initialize' => true,
			'lifetime' => 360,
			'ini' => [
				'session.cookie_secure' => true
			]
		]);

		$this->assertEquals('Titon', ini_get('session.name'));
		$this->assertEquals(360, ini_get('session.cookie_lifetime'));
		$this->assertEquals(true, ini_get('session.cookie_secure'));
		$this->assertEquals('localhost', ini_get('session.cookie_domain'));
		$this->assertEquals('localhost', ini_get('session.referer_check'));

		// Check security was set
		$this->assertTrue($session->has('Session'));
	}

	/**
	 * Test that get() returns data, while set() adds data.
	 */
	public function testGetSet() {
		$session = new Session(['initialize' => false]);

		$session->set('key', 'value');
		$this->assertEquals('value', $session->get('key'));

		$session->set('foo', 'bar');
		$this->assertEquals('bar', $session->get('foo'));

		$session->set('boolean', true);
		$this->assertEquals(true, $session->get('boolean'));

		$session->set('integer', 12345);
		$this->assertEquals(12345, $session->get('integer'));

		// fake key
		$this->assertEquals(null, $session->get('fakeKey'));

		// overwrite
		$session->set('boolean', false);
		$this->assertEquals(false, $session->get('boolean'));

		// nested
		$session->set('array.key', 'value');
		$this->assertEquals(['key' => 'value'], $session->get('array'));

		// get all
		$session = $session->get();
		unset($session['Session']);

		$this->assertEquals([
			'key' => 'value',
			'foo' => 'bar',
			'boolean' => false,
			'integer' => 12345,
			'array' => ['key' => 'value']
		], $session);
	}

	/**
	 * Test that has() returns true if the index exists.
	 */
	public function testHas() {
		$session = new Session(['initialize' => false]);

		$this->assertFalse($session->has('key'));
		$this->assertFalse($session->has('foo.bar'));

		$session->set('key', 'value')->set('foo.bar', true);

		$this->assertTrue($session->has('key'));
		$this->assertTrue($session->has('foo.bar'));
	}

	/**
	 * Test that remove() removes data from the session.
	 */
	public function testRemove() {
		$session = new Session(['initialize' => false]);
		$session->set('key', 'value')->set('foo.bar', true);

		$this->assertTrue($session->has('key'));
		$this->assertTrue($session->has('foo.bar'));

		$session->remove('foo.bar');

		$this->assertTrue($session->has('foo'));
		$this->assertFalse($session->has('foo.bar'));

		$session->remove('foo');

		$this->assertFalse($session->has('foo'));
		$this->assertFalse($session->has('foo.bar'));
	}

	/**
	 * Test that destroy() will flush the session.
	 */
	public function testDestroy() {
		$session = new Session(['initialize' => false]);
		$session->set('key', 'value');

		$this->assertEquals(['key' => 'value'], $session->get());

		$session->destroy();
		$this->assertEquals([], $session->get());
	}

	/**
	 * Test that validate() will destroy or regenerate the session depending on specific conditions.
	 */
	public function testValidateAndStartup() {
		$session = new Session();
		$token = $session->get('Session');

		// sleep so that we can cause time changes
		sleep(1);

		// changing browser should cause a destroy
		$_SERVER['HTTP_USER_AGENT'] = 'browser changed, hacking attempt!';
		$session->validate();

		$this->assertNotEquals($token['time'], $session->get('Session.time'));
		$this->assertNotEquals($token['agent'], $session->get('Session.agent'));

		// try again with no agent check
		$session = new Session(['checkUserAgent' => false]);
		$token = $session->get('Session');

		sleep(1);

		$_SERVER['HTTP_USER_AGENT'] = 'browser changed, hacking attempt!';
		$session->validate();

		// should be equal since we dont care if user agent changed
		$this->assertEquals($token['time'], $session->get('Session.time'));
		$this->assertEquals($token['agent'], $session->get('Session.agent'));
	}

}