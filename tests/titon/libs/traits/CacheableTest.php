<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\bundles;

use titon\tests\TestCase;
use titon\tests\fixtures\TraitFixture;

/**
 * Test class for titon\libs\traits\Cacheable.
 */
class CacheableTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new TraitFixture();
		$this->object->setCache('key', 'value');
	}

	/**
	 * Test that cache() will get and set data; can use a closure.
	 */
	public function testCache() {
		$this->object->cache('foo', 'bar');
		$this->assertEquals('bar', $this->object->getCache('foo'));
		$this->object->cache('foo', 'baz'); // doesn't overwrite
		$this->assertEquals('bar', $this->object->getCache('foo'));

		$this->object->cache('number', 12345);
		$this->assertEquals(12345, $this->object->getCache('number'));

		$this->object->cache('closure', function() {
			return (100 * 22);
		});
		$this->assertEquals(2200, $this->object->getCache('closure'));

	}

	/**
	 * Test that createCacheKey() generates a cache key string.
	 */
	public function testCreateCacheKey() {
		$this->assertEquals('foo', $this->object->createCacheKey('foo'));
		$this->assertEquals('foo-bar', $this->object->createCacheKey(['foo', 'bar']));
		$this->assertEquals('foo-12345-bar', $this->object->createCacheKey(['foo', 12345, 'bar']));
		$this->assertEquals('foo-12345-bar-2282d912cecf739da50a2e91d071b5cc', $this->object->createCacheKey(['foo', 12345, 'bar', ['nested', 'array']]));
	}

	/**
	 * Test that flushCache() empties the cache.
	 */
	public function testFlushCache() {
		$this->assertEquals(['key' => 'value'], $this->object->getCache());

		$this->object->flushCache();
		$this->assertEquals([], $this->object->getCache());
	}

	/**
	 * Test that getCache() returns values defined by key.
	 */
	public function testGetCache() {
		$this->assertEquals('value', $this->object->getCache('key'));
		$this->assertEquals(null, $this->object->getCache('foo'));

		$this->object->setCache('foo', 'bar');
		$this->assertEquals('bar', $this->object->getCache('foo'));

		$this->object->toggleCache(false);
		$this->assertEquals(null, $this->object->getCache('foo'));

		$this->object->toggleCache(true);
		$this->assertEquals([
			'key' => 'value',
			'foo' => 'bar'
		], $this->object->getCache());
	}

	/**
	 * Test that hasCache() returns true if the cache key exists.
	 */
	public function testHasCache() {
		$this->assertTrue($this->object->hasCache('key'));
		$this->assertFalse($this->object->hasCache('foo'));
	}

	/**
	 * Test that removeCache() removes data from the cache.
	 */
	public function testRemoveCache() {
		$this->assertTrue($this->object->removeCache('key'));
		$this->assertFalse($this->object->removeCache('foo'));

		$this->assertEquals([], $this->object->getCache());
	}

	/**
	 * Test that setCache() adds data to the cache.
	 */
	public function testSetCache() {
		$this->assertEquals('bar', $this->object->setCache('foo', 'bar'));
		$this->assertEquals(12345, $this->object->setCache('key', 12345));

		$this->assertEquals([
			'key' => 12345,
			'foo' => 'bar'
		], $this->object->getCache());
	}

	/**
	 * Test that toggleCache() enables and disables caching.
	 */
	public function testToggleCache() {
		$this->assertEquals('value', $this->object->getCache('key'));

		$this->object->toggleCache(false);
		$this->assertEquals(null, $this->object->getCache('key'));

		$this->object->toggleCache(true);
		$this->assertEquals('value', $this->object->getCache('key'));
	}

}