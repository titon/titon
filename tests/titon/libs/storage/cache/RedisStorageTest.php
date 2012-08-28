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
use titon\libs\storage\cache\RedisStorage;
use titon\tests\TestCase;

/**
 * Test class for titon\libs\storage\cache\RedisStorage.
 */
class RedisStorageTest extends TestCase {

	/**
	 * Initialize storage and create fake cache items.
	 */
	public function setUp() {
		parent::setUp();
		$this->skipIf(!Titon::load('redis'), 'Redis is not installed or configured properly.');

		$this->object = new RedisStorage(); // @todo
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