<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests;

use titon\Titon;

/**
 * Primary class that all test cases should extend.
 *
 * @package	titon.tests
 */
class TestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * Object to be used for the duration of the test case.
	 *
	 * @access protected
	 * @var object
	 */
	protected $object;

	/**
	 * Startup Titon.
	 */
	protected function setUp() {
		$_POST = [];
		$_GET = [];
		$_COOKIE = [];
		$_SESSION = [];
		$_REQUEST = [];

		Titon::shutdown(false);
		Titon::initialize();
	}

	/**
	 * Shutdown Titon.
	 */
	protected function tearDown() {
	}

	/**
	 * Assert that two array values are equal, disregarding the order.
	 *
	 * @access public
	 * @param array $expected
	 * @param array $actual
	 * @param boolean $keySort
	 * @return boolean
	 */
	public function assertArraysEqual(array $expected, array $actual, $keySort = false) {
		if ($keySort) {
			ksort($actual);
			ksort($expected);
		} else {
			sort($actual);
			sort($expected);
		}

		return $this->assertEquals($expected, $actual);
	}

}