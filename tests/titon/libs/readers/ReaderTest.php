<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\readers;

use titon\tests\TestCase;
use titon\tests\fixtures\ReaderFixture;
use \Exception;

/**
 * Test class for titon\libs\readers\Reader.
 */
class ReaderTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new ReaderFixture();
	}

	/**
	 * Test that getExtension() returns the extension.
	 */
	public function testGetExtension() {
		$this->assertEquals('exp', $this->object->getExtension());
	}

	/**
	 * Test that read() throws exceptions under certain conditions.
	 */
	public function testRead() {
		// no path
		try {
			$this->object->read();
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// falsey value
		try {
			$this->object->read(false);
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// wrong extension
		try {
			$this->object->read('index.php');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// doesn't exist
		try {
			$this->object->read('index.exp');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

}