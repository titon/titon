<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\core;

use titon\Titon;
use titon\tests\TestCase;
use titon\libs\storage\cache\MemoryStorage;
use \Exception;

/**
 * Test class for titon\core\Cache.
 */
class CacheTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$default = new MemoryStorage();
		$default->set('key', 'foo');

		$custom = new MemoryStorage();
		$custom->set('key', 'bar');

		$this->object = Titon::cache();
		$this->object->setup('default', $default);
		$this->object->setup('custom', $custom);
	}

	/**
	 * Test that decrement() lowers a number in the specific storage.
	 */
	public function testDecrement() {
		$this->assertEquals(null, $this->object->get('decrement'));
		$this->assertEquals(null, $this->object->get('decrement', 'custom'));

		$this->object->decrement('decrement', 1);
		$this->object->decrement('decrement', 5, 'custom');

		$this->assertEquals(-1, $this->object->get('decrement'));
		$this->assertEquals(-5, $this->object->get('decrement', 'custom'));
	}

	/**
	 * Test that flush() deletes all cache in a specific storage, or all storages.
	 */
	public function	testFlush() {
		$this->object->set('test', 123);
		$this->assertTrue($this->object->has('key'));
		$this->assertTrue($this->object->has('test'));

		$this->object->set('test', 123, null, 'custom');
		$this->assertTrue($this->object->has('key', 'custom'));
		$this->assertTrue($this->object->has('test', 'custom'));

		// flush only by name
		$this->object->flush('default');
		$this->assertFalse($this->object->has('key'));
		$this->assertFalse($this->object->has('test'));
		$this->assertTrue($this->object->has('key', 'custom'));
		$this->assertTrue($this->object->has('test', 'custom'));

		// flush all
		$this->object->flush();
		$this->assertFalse($this->object->has('key'));
		$this->assertFalse($this->object->has('test'));
		$this->assertFalse($this->object->has('key', 'custom'));
		$this->assertFalse($this->object->has('test', 'custom'));
	}

	/**
	 * Test that get() returns a cache from a specific storage.
	 */
	public function testGet() {
		$this->assertEquals(null, $this->object->get('fakeKey'));
		$this->assertEquals('foo', $this->object->get('key'));

		$this->assertEquals(null, $this->object->get('fakeKey', 'custom'));
		$this->assertEquals('bar', $this->object->get('key', 'custom'));
	}

	/**
	 * Test that has() returns true if the cache exists for a specific storage.
	 */
	public function testHas() {
		$this->assertTrue($this->object->has('key'));
		$this->assertFalse($this->object->has('fakeKey'));

		$this->assertTrue($this->object->has('key', 'custom'));
		$this->assertFalse($this->object->has('fakeKey', 'custom'));
	}

	/**
	 * Test that increment() raises a number in the specific storage.
	 */
	public function testIncrement() {
		$this->assertEquals(null, $this->object->get('increment'));
		$this->assertEquals(null, $this->object->get('increment', 'custom'));

		$this->object->increment('increment', 4);
		$this->object->increment('increment', 2, 'custom');

		$this->assertEquals(4, $this->object->get('increment'));
		$this->assertEquals(2, $this->object->get('increment', 'custom'));
	}

	/**
	 * Test that remove() deletes a cache from a specific storage.
	 */
	public function testRemove() {
		$this->assertEquals('foo', $this->object->get('key'));
		$this->object->remove('key');
		$this->assertEquals(null, $this->object->get('key'));

		$this->assertEquals('bar', $this->object->get('key', 'custom'));
		$this->object->remove('key', 'custom');
		$this->assertEquals(null, $this->object->get('key', 'custom'));
	}

	/**
	 * Test that set() adds to the cache for a specific storage.
	 */
	public function testSet() {
		$this->assertEquals('foo', $this->object->get('key'));
		$this->object->set('key', 'bar', '+1 hour');
		$this->assertEquals('bar', $this->object->get('key'));

		$this->assertEquals('bar', $this->object->get('key', 'custom'));
		$this->object->set('key', 'baz', null, 'custom');
		$this->assertEquals('baz', $this->object->get('key', 'custom'));
	}

	/**
	 * Test that setup() installs storage engines and storage() returns them.
	 */
	public function testSetupAndStorage() {
		try {
			$this->object->storage('test');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		$this->object->setup('test', new MemoryStorage());

		$this->assertInstanceOf('titon\libs\storage\Storage', $this->object->storage('test'));
		$this->assertEquals('test', $this->object->storage('test')->config->storage);
	}

}