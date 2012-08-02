<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\readers\gettext;

use titon\tests\TestCase;
use titon\libs\readers\gettext\PoReader;
use \Exception;

/**
 * Test class for titon\libs\readers\gettext\PoReader.
 */
class PoReaderTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new PoReader(APP_CONFIG . 'sets/po.po');
	}

	/**
	 * Test that parse() returns an array of data from the file.
	 */
	public function testParse() {
		$this->assertArraysEqual([
			'integer' => 1234567890,
			'string' => 'abcdefg',
			'plurals' => ['plural', 'plurals'],
			'multiline' => 'Multiline message' . PHP_EOL . 'More message here' . PHP_EOL . 'And more message again'
		], $this->object->parse(), true);
	}

}