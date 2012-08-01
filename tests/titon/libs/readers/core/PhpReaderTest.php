<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\readers\core;

use titon\tests\TestCase;
use titon\libs\readers\core\PhpReader;
use \Exception;

/**
 * Test class for titon\libs\readers\core\PhpReader.
 */
class PhpReaderTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new PhpReader(APP_CONFIG . 'sets/php.php');
	}

	/**
	 * Test that parse() returns an array of data from the file.
	 */
	public function testParse() {
		$this->assertArraysEqual([
			'integer' => 1234567890,
			'number' => '1234567890',
			'string' => 'abcdefg',
			'emptyArray' => [],
			'array' => [
				'one' => true,
				'two' => false,
			],
			'false' => false,
			'true' => true,
			'null' => null,
			'zero' => 0
		], $this->object->parse(), true);
	}

}