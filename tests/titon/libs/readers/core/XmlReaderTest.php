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
use titon\libs\readers\core\XmlReader;
use \Exception;

/**
 * Test class for titon\libs\readers\core\XmlReader.
 */
class XmlReaderTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new XmlReader(APP_CONFIG . 'sets/xml.xml');
	}

	/**
	 * Test that parse() returns an array of data from the file.
	 */
	public function testParse() {
		$this->assertArraysEqual([
			'integer' => 1234567890,
			'number' => '1234567890',
			'string' => [
				'value' => 'abcdefg',
				'key' => 'value'
			],
			'emptyArray' => [],
			'array' => [
				'one' => true,
				'two' => false,
			],
			'attrArray' => [
				'child' => ['Foo', 'Bar'],
				'key' => 'value'
			],
			'false' => false,
			'true' => true,
			'zero' => 0
		], $this->object->parse(), true);
	}

}