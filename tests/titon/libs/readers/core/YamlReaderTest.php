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
use titon\libs\readers\core\YamlReader;
use \Exception;

/**
 * Test class for titon\libs\readers\core\YamlReader.
 */
class YamlReaderTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new YamlReader(APP_CONFIG . 'sets/yaml.yaml');
	}

	/**
	 * Test that parse() returns an array of data from the file.
	 */
	public function testParse() {
		$this->assertArraysEqual([
			'integer' => 1234567890,
			'number' => '1234567890',
			'string' => 'abcdefg',
			'object' => [
				'one' => true,
				'two' => false,
			],
			'array' => ['foo', 'bar'],
			'zero' => 0
		], $this->object->parse(), true);
	}

}