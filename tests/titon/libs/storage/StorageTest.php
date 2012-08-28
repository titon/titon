<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\storage;

use titon\Titon;
use titon\tests\TestCase;
use titon\tests\fixtures\StorageFixture;

/**
 * Test class for titon\libs\storage\Storage.
 */
class StorageTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new StorageFixture([
			'expires' => '+3 days',
			'serialize' => false
		]);
	}

	/**
	 * Test that expires() returns a future timestamp.
	 */
	public function testExpires() {
		$this->assertEquals(strtotime('+3 days'), $this->object->expires(null));
		$this->assertEquals(strtotime('+1 day'), $this->object->expires('+1 day'));
		$this->assertEquals(time(), $this->object->expires(time()));
	}

	/**
	 * Test that key() formats a cache key.
	 */
	public function testKey() {
		$this->assertEquals('Model.someMethod', $this->object->key('Model::someMethod()'));
		$this->assertEquals('Model.someMethod.123456', $this->object->key('Model::someMethod-123456'));
		$this->assertEquals('Model.someMethod.abc.123456', $this->object->key('Model::someMethod()-abc-123456'));
		$this->assertEquals('some.name.space.Model.someMethod', $this->object->key('some\name\space\Model::someMethod()'));
		$this->assertEquals('cache.key', $this->object->key('cache-key'));
		$this->assertEquals('cache.key.123', $this->object->key(['cache', 'key', 123]));
	}

	/**
	 * Test that encode() and decode() serialize data correctly.
	 */
	public function testEncodeDecode() {
		$value = $this->object->encode(['key' => 'value']);
		$this->assertEquals(['key' => 'value'], $value);
		$this->assertEquals(['key' => 'value'], $this->object->decode($value));

		$this->object->config->serialize = true;
		$value = $this->object->encode(['key' => 'value']);
		$this->assertEquals('a:1:{s:3:"key";s:5:"value";}', $value);
		$this->assertEquals(['key' => 'value'], $this->object->decode($value));
	}

}