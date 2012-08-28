<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\constant;

use titon\Titon;
use titon\constant\Http;
use titon\tests\TestCase;
use \Exception;

/**
 * Test class for titon\constant\Http.
 */
class HttpTest extends TestCase {

	/**
	 * Test getHeaderTypes() returns all the header types.
	 */
	public function testGetHeaderTypes() {
		$this->assertEquals(Http::$headerTypes, Http::getHeaderTypes());
	}

	/**
	 * Test getMethodTypes() returns all the method types.
	 */
	public function testGetMethodTypes() {
		$this->assertEquals(Http::$methodTypes, Http::getMethodTypes());
	}

	/**
	 * Test getStatusCodes() returns all the status codes.
	 */
	public function testGetStatusCodes() {
		$this->assertEquals(Http::$statusCodes, Http::getStatusCodes());
	}

	/**
	 * Test getStatusCode() returns the value of the code, or throws an exception.
	 */
	public function testGetStatusCode() {
		$this->assertEquals('OK', Http::getStatusCode(200));

		try {
			Http::getStatusCode(999);
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

}