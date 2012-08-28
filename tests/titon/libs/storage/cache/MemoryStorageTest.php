<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\storage\cache;

use titon\Titon;
use titon\libs\storage\cache\MemoryStorage;
use titon\tests\TestCase;

/**
 * Test class for titon\libs\storage\cache\MemoryStorage.
 */
class MemoryStorageTest extends TestCase {

	/**
	 * Initialize storage and create fake cache items.
	 */
	public function setUp() {
		parent::setUp();

		$this->object = new MemoryStorage();
		$this->object->set('User::getById-1337', ['username' => 'Titon']);
		$this->object->set('Topic::getAll', [['id' => 1], ['id' => 2]], '-1 day'); // expired
		$this->object->set('Comment::count', 1);
	}

	/**
	 * Delete all cache items and folders.
	 */
	protected function tearDown() {
		$this->object->flush();
		unset($this->object);
	}

	/**
	 * Test that decrement() lowers the cache content number.
	 */
	public function testDecrement() {
		$this->assertEquals(1, $this->object->get('Comment::count'));

		$this->object->decrement('Comment::count', 1);
		$this->assertEquals(0, $this->object->get('Comment::count'));

		$this->object->decrement('Comment::count', 5);
		$this->assertEquals(-5, $this->object->get('Comment::count'));
	}

	/**
	 * Test that flush() deletes all cache.
	 */
	public function testFlush() {
		$this->assertTrue($this->object->has('User::getById-1337'));
		$this->object->flush();
		$this->assertFalse($this->object->has('User::getById-1337'));
	}

	/**
	 * Test that get() returns the contents of a cache.
	 */
	public function testGet() {
		$this->assertEquals(['username' => 'Titon'], $this->object->get('User::getById-1337'));
		$this->assertEquals(1, $this->object->get('Comment::count'));
		$this->assertEquals([['id' => 1], ['id' => 2]], $this->object->get('Topic::getAll'));

		$this->assertEquals(null, $this->object->get('Post::getById-666'));
	}

	/**
	 * Test that has() returns true if the cache exists.
	 */
	public function testHas() {
		$this->assertTrue($this->object->has('User::getById-1337'));
		$this->assertFalse($this->object->has('Post::getById-666'));
	}

	/**
	 * Test that increment() raises the cache content number.
	 */
	public function testIncrement() {
		$this->assertEquals(1, $this->object->get('Comment::count'));

		$this->object->increment('Comment::count', 1);
		$this->assertEquals(2, $this->object->get('Comment::count'));

		$this->object->increment('Comment::count', 5);
		$this->assertEquals(7, $this->object->get('Comment::count'));
	}

	/**
	 * Test that remove() deletes the cache.
	 */
	public function testRemove() {
		$this->assertTrue($this->object->has('User::getById-1337'));
		$this->object->remove('User::getById-1337');
		$this->assertFalse($this->object->has('User::getById-1337'));
	}

	/**
	 * Test that set() writes data to the cache.
	 */
	public function testSet() {
		$this->assertEquals(['username' => 'Titon'], $this->object->get('User::getById-1337'));
		$this->object->set('User::getById-1337', ['username' => 'Titon Framework']);
		$this->assertEquals(['username' => 'Titon Framework'], $this->object->get('User::getById-1337'));

		$this->assertEquals(null, $this->object->get('Post::getById-666'));
		$this->object->set('Post::getById-666', ['username' => 'Miles']);
		$this->assertEquals(['username' => 'Miles'], $this->object->get('Post::getById-666'));
	}

}