<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\fixtures;

use titon\libs\storage\StorageAbstract;

/**
 * Fixture for titon\libs\storage\StorageAbstract.
 *
 * @package	titon.tests.fixtures
 */
class StorageFixture extends StorageAbstract {

	public function decrement($key, $step = 1) {}
	public function flush() {}
	public function get($key) {}
	public function has($key) {}
	public function increment($key, $step = 1) {}
	public function remove($key) {}
	public function set($key, $value, $expires = null) {}

}