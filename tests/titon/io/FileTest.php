<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon;

use titon\io\File;
use titon\tests\TestCase;
use \Exception;

/**
 * Test class for titon\io\File.
 */
class FileTest extends TestCase {

	/**
	 * Temp file for testing.
	 */
	public $temp;

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		@unlink(__DIR__ . '/test.tmp');

		$this->object = new File(__FILE__);
		$this->temp = new File(__DIR__ . '/test.tmp', false);
	}

	public function testAccessTime() {
		$this->assertTrue(is_int($this->object->accessTime()));
		$this->assertTrue(is_null($this->temp->accessTime()));
	}

	public function testChangeTime() {
		$this->assertTrue(is_int($this->object->changeTime()));
		$this->assertTrue(is_null($this->temp->changeTime()));
	}

	public function testChgrp() {

	}

	public function testChmod() {

	}

	public function testChown() {

	}

}