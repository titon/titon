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
use titon\libs\storage\cache\MemcacheStorage;
use titon\tests\TestCase;

/**
 * Test class for titon\libs\storage\cache\MemcacheStorage.
 */
class MemcacheStorageTest extends TestCase {

	/**
	 * Initialize storage and create fake cache items.
	 */
	public function setUp() {
		parent::setUp();
		$this->skipIf(!Titon::load('memcache'), 'Memcache is not installed or configured properly.');

		$this->object = new MemcacheStorage(); // @todo
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

}