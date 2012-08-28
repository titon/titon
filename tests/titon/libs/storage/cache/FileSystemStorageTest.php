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
use titon\libs\storage\cache\FileSystemStorage;
use titon\tests\TestCase;

/**
 * Test class for titon\libs\storage\cache\FileSystemStorage.
 */
class FileSystemStorageTest extends TestCase {

	/**
	 * Initialize storage and create fake cache items.
	 */
	public function setUp() {
		$this->object = new FileSystemStorage(array('storage' => 'file_system'));
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
		rmdir(APP_TEMP . 'cache/file_system/');
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
	 * Test that flush() deletes all files in the folder.
	 */
	public function testFlush() {
		$this->assertTrue(file_exists(APP_TEMP . 'cache/file_system/User.getById.1337.cache'));
		$this->object->flush();
		$this->assertFalse(file_exists(APP_TEMP . 'cache/file_system/User.getById.1337.cache'));
	}

	/**
	 * Test that get() returns the contents of a cache while respecting expiration times.
	 */
	public function testGet() {
		$this->assertEquals(['username' => 'Titon'], $this->object->get('User::getById-1337'));
		$this->assertEquals(1, $this->object->get('Comment::count'));

		$this->assertEquals(null, $this->object->get('Topic::getAll')); // Expired
		$this->assertEquals(null, $this->object->get('Post::getById-666'));
	}

	/**
	 * Test that has() returns true if the cache file exists.
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
	 * Test that remove() deletes the cache file.
	 */
	public function testRemove() {
		$this->assertTrue($this->object->has('User::getById-1337'));
		$this->assertTrue(file_exists(APP_TEMP . 'cache/file_system/User.getById.1337.cache'));

		$this->object->remove('User::getById-1337');
		$this->assertFalse($this->object->has('User::getById-1337'));
		$this->assertFalse(file_exists(APP_TEMP . 'cache/file_system/User.getById.1337.cache'));
	}

	/**
	 * Test that set() writes data to the cache file.
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