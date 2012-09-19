<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\security;

use titon\Titon;
use titon\tests\TestCase;
use titon\tests\fixtures\ControllerFixture;
use titon\tests\fixtures\IdentifierFixture;
use titon\security\Auth;
use titon\net\Session;
use \Exception;

/**
 * Test class for titon\security\Auth.
 */
class AuthTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$_SESSION = [];
		$_SERVER['HTTP_USER_AGENT'] = 'browser';

		Titon::router()->initialize();

		$this->object = new Auth();
		$this->object->setSession(new Session());
		$this->object->setIdentifier(new IdentifierFixture());
	}

	/**
	 * Test that getIdentifier() throws exceptions if an identifier doesn't exist.
	 */
	public function testGetSetIdentifier() {
		$auth = new Auth();

		try {
			$auth->getIdentifier();
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		$auth->setIdentifier(new IdentifierFixture());

		try {
			$auth->getIdentifier();
			$this->assertTrue(true);
		} catch (Exception $e) {
			$this->assertTrue(false);
		}
	}

	/**
	 * Test that getSession() throws exceptions if a session object doesn't exist.
	 */
	public function testGetSetSession() {
		$auth = new Auth();

		try {
			$auth->getSession();
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		$auth->setSession(new Session());

		try {
			$auth->getSession();
			$this->assertTrue(true);
		} catch (Exception $e) {
			$this->assertTrue(false);
		}
	}

	/**
	 * Test that isAuthenticated() returns true if the auth session is active.
	 */
	public function testIsAuthenticated() {
		$this->assertFalse($this->object->isAuthenticated());

		$this->object->getSession()->set($this->object->config->sessionKey, array('username' => 'user'));
		$this->assertTrue($this->object->isAuthenticated());
	}

	/**
	 * Test that isAuthorized() returns true or false from the processing object.
	 */
	public function testIsAuthorized() {
		$this->assertTrue($this->object->isAuthorized($this));

		$this->assertFalse($this->object->isAuthorized(new ControllerFixture()));
	}

	/**
	 * Test that login() triggers the identifier and saves the user to the session.
	 */
	public function testLogin() {
		$this->assertFalse($this->object->isAuthenticated());
		$this->assertEquals(['username' => 'user', 'password' => 'pass'], $this->object->login());
		$this->assertTrue($this->object->isAuthenticated());
	}

	/**
	 * Test that logout() destroys the current auth session.
	 */
	public function testLogout() {
		$this->object->login();
		$this->assertTrue($this->object->isAuthenticated());

		$this->object->logout();
		$this->assertFalse($this->object->isAuthenticated());
	}

	/**
	 * Test that get() and user() return the session, while set() updates it.
	 */
	public function testGetSetUser() {
		$this->object->login();
		$this->assertEquals(['username' => 'user', 'password' => 'pass'], $this->object->user());

		$this->assertEquals('user', $this->object->get('username'));
		$this->assertEquals(null, $this->object->get('email'));

		$this->object->set('username', 'titon');
		$this->assertEquals('titon', $this->object->get('username'));
		$this->assertEquals(['username' => 'titon', 'password' => 'pass'], $this->object->user());
	}

}