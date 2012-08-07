<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon;

use titon\log\Benchmark;
use titon\tests\TestCase;

/**
 * Test class for titon\Titon.
 */
class ExprBenchmarkTest extends TestCase {

	/**
	 * Test string expression and evaluation to see which approach is faster.
	 */
	public function testStringExpr() {
		$emptyString = '';
		$valueString = 'Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet.';

		// Testing empty strings
		Benchmark::start('String.empty');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if ($emptyString) {
				$count++;
			}
		}

		$this->assertEquals(0, $count);
		Benchmark::stop('String.empty');

		$this->out(Benchmark::output('String.empty'));

		// Testing empty strings using empty()
		Benchmark::start('String.empty.notEmpty');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (!empty($emptyString)) {
				$count++;
			}
		}

		$this->assertEquals(0, $count);
		Benchmark::stop('String.empty.notEmpty');

		$this->out(Benchmark::output('String.empty.notEmpty'));

		// Testing empty strings using !==
		Benchmark::start('String.empty.notEqual');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if ($emptyString !== '') {
				$count++;
			}
		}

		$this->assertEquals(0, $count);
		Benchmark::stop('String.empty.notEqual');

		$this->out(Benchmark::output('String.empty.notEqual'));

		// Testing empty strings using strlen()
		Benchmark::start('String.empty.length');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (strlen($emptyString) > 0) {
				$count++;
			}
		}

		$this->assertEquals(0, $count);
		Benchmark::stop('String.empty.length');

		$this->out(Benchmark::output('String.empty.length'));

		// Testing value strings
		Benchmark::start('String.value');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if ($valueString) {
				$count++;
			}
		}

		$this->assertEquals(100000, $count);
		Benchmark::stop('String.value');

		$this->out(Benchmark::output('String.value'));

		// Testing value strings using empty()
		Benchmark::start('String.value.notEmpty');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (!empty($valueString)) {
				$count++;
			}
		}

		$this->assertEquals(100000, $count);
		Benchmark::stop('String.value.notEmpty');

		$this->out(Benchmark::output('String.value.notEmpty'));

		// Testing value strings using !==
		Benchmark::start('String.value.notEqual');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if ($valueString !== '') {
				$count++;
			}
		}

		$this->assertEquals(100000, $count);
		Benchmark::stop('String.value.notEqual');

		$this->out(Benchmark::output('String.value.notEqual'));

		// Testing value strings using strlen()
		Benchmark::start('String.value.length');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (strlen($valueString) > 0) {
				$count++;
			}
		}

		$this->assertEquals(100000, $count);
		Benchmark::stop('String.value.length');

		$this->out(Benchmark::output('String.value.length'));

		$this->out();
	}

	/**
	 * Test array expression and evaluation to see which approach is faster.
	 */
	public function testArrayExpr() {
		$emptyArray = [];
		$valueArray = ['Lorem ipsum dolor sit amet.', 'Lorem ipsum dolor sit amet.', 'Lorem ipsum dolor sit amet.', ['Lorem ipsum dolor sit amet.']];

		// Testing empty arrays
		Benchmark::start('Array.empty');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if ($emptyArray) {
				$count++;
			}
		}

		$this->assertEquals(0, $count);
		Benchmark::stop('Array.empty');

		$this->out(Benchmark::output('Array.empty'));

		// Testing empty arrays using empty()
		Benchmark::start('Array.empty.notEmpty');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (!empty($emptyArray)) {
				$count++;
			}
		}

		$this->assertEquals(0, $count);
		Benchmark::stop('Array.empty.notEmpty');

		$this->out(Benchmark::output('Array.empty.notEmpty'));

		// Testing empty arrays using count()
		Benchmark::start('Array.empty.count');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (count($emptyArray) > 0) {
				$count++;
			}
		}

		$this->assertEquals(0, $count);
		Benchmark::stop('Array.empty.count');

		$this->out(Benchmark::output('Array.empty.count'));

		// Testing value arrays
		Benchmark::start('Array.value');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if ($valueArray) {
				$count++;
			}
		}

		$this->assertEquals(100000, $count);
		Benchmark::stop('Array.value');

		$this->out(Benchmark::output('Array.value'));

		// Testing value arrays using empty()
		Benchmark::start('Array.value.notEmpty');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (!empty($valueArray)) {
				$count++;
			}
		}

		$this->assertEquals(100000, $count);
		Benchmark::stop('Array.value.notEmpty');

		$this->out(Benchmark::output('Array.value.notEmpty'));

		// Testing value array using count()
		Benchmark::start('Array.value.count');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (count($valueArray) > 0) {
				$count++;
			}
		}

		$this->assertEquals(100000, $count);
		Benchmark::stop('Array.value.count');

		$this->out(Benchmark::output('Array.value.count'));

		$this->out();
	}

	/**
	 * Test which approach is faster for checking an array key.
	 */
	public function testArrayIndex() {
		$array = [
			'key' => 'value',
			'tier' => [
				'tier' => ['key' => 'value']
			]
		];

		// Test using isset()
		Benchmark::start('ArrayIndex.isset');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (isset($array['key'])) {
				$count++;
			}
		}

		$this->assertEquals(100000, $count);
		Benchmark::stop('ArrayIndex.isset');

		$this->out(Benchmark::output('ArrayIndex.isset'));

		// Test using isset() deep nested
		Benchmark::start('ArrayIndex.isset.deep');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (isset($array['tier']['tier']['key'])) {
				$count++;
			}
		}

		$this->assertEquals(100000, $count);
		Benchmark::stop('ArrayIndex.isset.deep');

		$this->out(Benchmark::output('ArrayIndex.isset.deep'));

		// Test using isset() on an invalid index
		Benchmark::start('ArrayIndex.isset.empty');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (isset($array['empty'])) {
				$count++;
			}
		}

		$this->assertEquals(0, $count);
		Benchmark::stop('ArrayIndex.isset.empty');

		$this->out(Benchmark::output('ArrayIndex.isset.empty'));

		// Test using array_key_exists()
		Benchmark::start('ArrayIndex.key');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (array_key_exists('key', $array)) {
				$count++;
			}
		}

		$this->assertEquals(100000, $count);
		Benchmark::stop('ArrayIndex.key');

		$this->out(Benchmark::output('ArrayIndex.key'));

		// Test using array_key_exists() deep nested
		Benchmark::start('ArrayIndex.key.deep');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (array_key_exists('key', $array['tier']['tier'])) {
				$count++;
			}
		}

		$this->assertEquals(100000, $count);
		Benchmark::stop('ArrayIndex.key.deep');

		$this->out(Benchmark::output('ArrayIndex.key.deep'));

		// Test using array_key_exists() on an invalid index
		Benchmark::start('ArrayIndex.key.empty');
		$count = 0;

		for ($i = 1; $i <= 100000; $i++) {
			if (array_key_exists('empty', $array)) {
				$count++;
			}
		}

		$this->assertEquals(0, $count);
		Benchmark::stop('ArrayIndex.key.empty');

		$this->out(Benchmark::output('ArrayIndex.key.empty'));

		$this->out();
	}

}