<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\base;

use titon\base\Base;
use titon\tests\TestCase;

/**
 * Test class for titon\base\Base.
 */
class BaseTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new Base();
	}

	/**
	 * Test that augments are being loaded.
	 */
	public function testAugments() {
		$this->assertInstanceOf('titon\libs\augments\ConfigAugment', $this->object->config);
		$this->assertInstanceOf('titon\libs\augments\InfoAugment', $this->object->info);
	}

	/**
	 * Test that serialize() returns the config serialized.
	 */
	public function testSerialize() {
		$this->assertEquals('a:1:{s:10:"initialize";b:1;}', $this->object->serialize());
	}

	/**
	 * Test that toString() returns the class name as a string.
	 */
	public function testToString() {
		$this->assertEquals('titon\base\Base', $this->object->toString());
		$this->assertEquals('titon\base\Base', (string) $this->object);
	}

	/**
	 * Test that unserialize() will unserialize and set the config.
	 */
	public function testUnserialize() {
		$this->object->unserialize('a:1:{s:10:"initialize";b:1;}');

		$this->assertEquals(['initialize' => true], $this->object->config->get());
	}

}