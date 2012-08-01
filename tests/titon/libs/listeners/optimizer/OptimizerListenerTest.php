<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\listeners\optimizer;

use titon\Titon;
use titon\tests\TestCase;
use titon\libs\listeners\optimizer\OptimizerListener;

/**
 * Test class for titon\libs\listeners\optimizer\OptimizerListener.
 */
class OptimizerListenerTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new OptimizerListener();
	}

	/**
	 * Test that disableGarbageCollection() turns GC off.
	 */
	public function testDisableGarbageCollection() {
		gc_enable();
		$this->assertTrue(gc_enabled());

		$this->object->disableGarbageCollection();
		$this->assertFalse(gc_enabled());
	}

	/**
	 * Test that enableGarbageCollection() turns GC on.
	 */
	public function testEnableGarbageCollection() {
		gc_disable();
		$this->assertFalse(gc_enabled());

		$this->object->enableGarbageCollection();
		$this->assertTrue(gc_enabled());
	}

	/**
	 * Test that enableGzipCompression() turns GZIP compression on.
	 */
	public function testEnableGzipCompression() {
		$this->assertEquals(-1, ini_get('zlib.output_compression_level'));

		$this->object->enableGzipCompression();
		$this->assertEquals(5, ini_get('zlib.output_compression_level'));
	}

}