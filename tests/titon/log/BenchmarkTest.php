<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\net;

use titon\log\Benchmark;
use titon\tests\TestCase;

/**
 * Test class for titon\log\Benchmark.
 */
class BenchmarkTest extends TestCase {

	/**
	 * Test that setting and getting benchmarks work.
	 */
	public function testBenchmarking() {
		// starting
		Benchmark::start('test');
		$this->assertArrayHasKey('startTime', Benchmark::get('test'));
		$this->assertArrayHasKey('startMemory', Benchmark::get('test'));
		$this->assertArrayNotHasKey('endTime', Benchmark::get('test'));
		$this->assertArrayNotHasKey('endMemory', Benchmark::get('test'));

		// stopping
		Benchmark::stop('test');
		$this->assertArrayHasKey('endTime', Benchmark::get('test'));
		$this->assertArrayHasKey('endMemory', Benchmark::get('test'));

		// getting
		$this->assertEquals(null, Benchmark::get('fake'));
		$this->assertTrue(is_array(Benchmark::get('test')));
		$this->assertTrue(count(Benchmark::get()) === 1);

		// output
		$test = Benchmark::get('test');
		$this->assertEquals(null, Benchmark::output('fake'));
		$this->assertEquals(sprintf('Benchmark [test] - Time: %s - Memory: %s (Max: %s)', number_format($test['avgTime'], 4), $test['avgMemory'], $test['peakMemory']), Benchmark::output('test'));
	}

}