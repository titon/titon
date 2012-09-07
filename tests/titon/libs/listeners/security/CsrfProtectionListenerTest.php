<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\listeners\security;

use titon\Titon;
use titon\libs\listeners\security\CsrfProtectionListener;
use titon\tests\TestCase;
use titon\tests\fixtures\DispatcherFixture;
use \Exception;

/**
 * Test class for titon\libs\listeners\security\CsrfProtectionListener.
 */
class CsrfProtectionListenerTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$_SERVER['HTTP_USER_AGENT'] = 'browser';

		Titon::router()->initialize();

		$this->object = new CsrfProtectionListener();
		$this->object->startup();
	}

	/**
	 * Test that an exception is thrown if no previous token exists.
	 */
	public function testNoToken() {
		try {
			$this->object->preDispatch(new DispatcherFixture());
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that validation works on $_POST.
	 */
	public function testPostValidation() {
		$token = $this->object->session->get('Security.csrf.token');
		$this->object->startup();

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->object->request->post['csrf'] = 'invalid';

		try {
			$this->object->preDispatch(new DispatcherFixture());
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		$this->object->request->post['csrf'] = $token;

		try {
			$this->object->preDispatch(new DispatcherFixture());
			$this->assertTrue(true);
		} catch (Exception $e) {
			$this->assertTrue(false);
		}
	}

	/**
	 * Test that validation works on $_GET.
	 */
	public function testGetValidation() {
		$token = $this->object->session->get('Security.csrf.token');
		$this->object->startup();

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->object->request->get['csrf'] = 'invalid';

		try {
			$this->object->preDispatch(new DispatcherFixture());
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		$this->object->request->get['csrf'] = $token;

		try {
			$this->object->preDispatch(new DispatcherFixture());
			$this->assertTrue(true);
		} catch (Exception $e) {
			$this->assertTrue(false);
		}
	}

}

