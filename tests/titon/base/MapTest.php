<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\base;

use titon\base\Map;
use titon\tests\TestCase;

/**
 * Test class for titon\base\Map.
 */
class MapTest extends TestCase {

	/**
	 * Example data.
	 */
	public $map = [
		'integer' => 12345,
		'number' => '67890',
		'string' => 'Foobar',
		'boolean' => true,
		'null' => null,
		'zero' => 0,
		'empty' => '',
		'map' => ['foo' => 'bar'],
		'array' => ['foo', 'bar']
	];

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new Map($this->map);
	}

	/**
	 * Test that append() adds items to the bottom of the array.
	 */
	public function testAppend() {
		$this->object->append('append')->append(['append-array', 'append-array']);

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'null' => null,
			'zero' => 0,
			'empty' => '',
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar'],
			'append',
			'append-array',
			'append-array'
		], $this->object->value());
	}

	/**
	 * Test that chunk() returns the array split into chunks.
	 */
	public function testChunk() {
		$this->assertEquals([
			[
				'integer' => 12345,
				'number' => '67890',
				'string' => 'Foobar',
			], [
				'boolean' => true,
				'null' => null,
				'zero' => 0,
			], [
				'empty' => '',
				'map' => ['foo' => 'bar'],
				'array' => ['foo', 'bar'],
			]
		], $this->object->chunk(3));
	}

	/**
	 * Test that clean() removes empty or false values, while preserving zeros.
	 */
	public function testClean() {
		$this->object->clean();

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'zero' => 0,
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar']
		], $this->object->value());
	}

	/**
	 * Test that compare() returns an array of values, if the value is found in both arrays.
	 */
	public function testCompare() {
		$compare = $this->object->compare(['Foobar'], ['strict' => false]);

		$this->assertEquals(['string' => 'Foobar'], $compare);
	}

	/**
	 * Test that compare() returns an array of values, if the value is found in both arrays and passes the callback.
	 */
	public function testCompareWithValueCallback() {
		$compare = $this->object->compare(['string' => 'FOOBAR', 'integer' => 53463], [
			'strict' => false,
			'valueCallback' => function ($k1, $k2) {
				return strcasecmp($k1, $k2);
			}
		]);

		$this->assertEquals(['string' => 'Foobar'], $compare);
	}

	/**
	 * Test that compare() returns an array of values, if the value and matching key is found in both arrays.
	 */
	public function testCompareStrict() {
		$compare = $this->object->compare(['string' => 'Foobar', 'integer' => 67890], ['strict' => true]);

		$this->assertEquals(['string' => 'Foobar'], $compare);
	}

	/**
	 * Test that compare() returns an array of values, if the value and matching key is found in both arrays and passes the callback.
	 */
	public function testCompareStrictWithCallback() {
		$compare = $this->object->compare(['STRING' => 'Foobar', 'integer' => 67890], [
			'strict' => true,
			'callback' => null
		]);

		$this->assertEquals([], $compare);

		$compare = $this->object->compare(['STRING' => 'Foobar', 'integer' => 67890], [
			'strict' => true,
			'callback' => function ($k1, $k2) {
				return strcasecmp($k1, $k2);
			}
		]);

		$this->assertEquals(['string' => 'Foobar'], $compare);
	}

	/**
	 * Test that compare() returns an array of values, if the value and matching key is found in both arrays and passes the callback.
	 */
	public function testCompareStrictWithCallbackAndValueCallback() {
		$compare = $this->object->compare(['STRING' => 'FOOBAR', 'integer' => 67890], [
			'strict' => true,
			'callback' => function ($k1, $k2) {
				return strcasecmp($k1, $k2);
			},
			'valueCallback' => function ($k1, $k2) {
				return strcasecmp($k1, $k2);
			}
		]);

		$this->assertEquals(['string' => 'Foobar'], $compare);
	}

	/**
	 * Test that compare() returns an array of values, if the value and matching key is found in both arrays and passes the callback.
	 */
	public function testCompareStrictWithValueCallback() {
		$compare = $this->object->compare(['string' => 'FOOBAR', 'integer' => 67890], [
			'strict' => true,
			'valueCallback' => function ($k1, $k2) {
				return strcasecmp($k1, $k2);
			}
		]);

		$this->assertEquals(['string' => 'Foobar'], $compare);
	}

	/**
	 * Test that compare() returns an array of values where the keys are found in both arrays.
	 */
	public function testCompareAgainstKeys() {
		$compare = $this->object->compare(['integer' => 67890, 'string' => 'Test'], ['on' => 'keys']);

		$this->assertEquals([
			'integer' => 12345,
			'string' => 'Foobar'
		], $compare);
	}

	/**
	 * Test that compare() returns an array of values where the keys are found in both arrays and ran through a callback.
	 */
	public function testCompareAgainstKeysWithCallback() {
		$compare = $this->object->compare(['integer' => 67890, 'boolean' => false], [
			'on' => 'keys',
			'callback' => function($k1, $k2) {
				if ($k1 == $k2) {
					return 0;
				} else if ($k1 > $k2) {
					return 1;
				} else {
					return -1;
				}
			}
		]);

		$this->assertEquals([
			'integer' => 12345,
			'boolean' => true
		], $compare);
	}

	/**
	 * Test that concat() returns a new Map with the arrays merged.
	 */
	public function testConcat() {
		$map = $this->object->concat(['concat' => 'append']);

		$this->assertInstanceOf('\titon\base\Map', $map);
		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'null' => null,
			'zero' => 0,
			'empty' => '',
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar'],
			'concat' => 'append'
		], $map->value());
	}

	/**
	 * Test that contains() returns true if the value exists in the array.
	 */
	public function testContains() {
		$this->assertTrue($this->object->contains(12345));
		$this->assertTrue($this->object->contains('Foobar'));
		$this->assertTrue($this->object->contains(null));
		$this->assertFalse($this->object->contains(63453435));
		$this->assertFalse($this->object->contains('Barbaz'));
		$this->assertFalse($this->object->contains(false));
	}

	/**
	 * Test that countValues() returns a count of how many times a value is found.
	 */
	public function testCountValues() {
		$this->object->append(['count', 'count']);

		$this->assertEquals([
			'12345' => 1,
			'67890' => 1,
			'Foobar' => 1,
			'0' => 1,
			'' => 1,
			'count' => 2
		], $this->object->countValues());
	}

	/**
	 * Test that depth() returns the max array depth.
	 */
	public function testDepth() {
		$this->assertEquals(2, $this->object->depth());

		$this->object->set('deep.deep.deep.depth', 1);
		$this->assertEquals(4, $this->object->depth());
	}

	/**
	 * Test that difference() returns all elements not found in second array.
	 */
	public function testDifference() {
		$diff = $this->object->difference([
			'integer' => 12345,
			'empty' => '',
			'string' => 'Foobar',
			'boolean' => true
		], ['strict' => false]);

		$this->assertEquals([
			'number' => '67890',
			'zero' => 0
		], $diff);
	}

	/**
	 * Test that difference() returns all elements not found in second array by comparing with a callback.
	 */
	public function testDifferenceWithValueCallback() {
		$diff = $this->object->difference([
			'integer' => 12345,
			'empty' => '',
			'string' => 'FOOBAR',
			'boolean' => true
		], [
			'strict' => false,
			'valueCallback' => function ($k1, $k2) {
				return strcasecmp($k1, $k2);
			}
		]);

		$this->assertEquals([
			'number' => '67890',
			'zero' => 0
		], $diff);
	}

	/**
	 * Test that difference() returns all elements not found in second array by comparing keys and values.
	 */
	public function testDifferenceStrict() {
		$diff = $this->object->difference([
			'integer' => 12345,
			'empty' => '',
			'string' => 'Barbaz',
			'boolean' => true
		], ['strict' => true]);

		$this->assertEquals([
			'number' => '67890',
			'string' => 'Foobar',
			'null' => null,
			'zero' => 0
		], $diff);
	}

	/**
	 * Test that difference() returns all elements not found in second array by comparing keys and values.
	 */
	public function testDifferenceStrictWithCallback() {
		$diff = $this->object->difference([
			'integer' => 56789,
			'empty' => '',
			'STRING' => 'FOOBAR',
			'boolean' => true
		], [
			'strict' => true
		]);

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'null' => null,
			'zero' => 0
		], $diff);

		$diff = $this->object->difference([
			'integer' => 12345,
			'empty' => '',
			'string' => 'Barbaz',
			'boolean' => true
		], [
			'strict' => true,
			'callback' => function ($k1, $k2) {
				return strcasecmp($k1, $k2);
			}
		]);

		$this->assertEquals([
			'number' => '67890',
			'string' => 'Foobar',
			'null' => null,
			'zero' => 0
		], $diff);
	}

	/**
	 * Test that difference() returns all elements not found in second array by comparing keys, values and with a callback.
	 */
	public function testDifferenceStrictWithCallbackAndValueCallback() {
		$diff = $this->object->difference([
			'INTEGER' => 12345,
			'fake' => '',
			'string' => 'FOOBAR',
			'BOOLEAN' => true
		], [
			'strict' => true,
			'callback' => function ($k1, $k2) {
				return strcasecmp($k1, $k2);
			},
			'valueCallback' => function ($k1, $k2) {
				return strcasecmp($k1, $k2);
			}
		]);

		$this->assertEquals([
			'number' => '67890',
			'zero' => 0,
			'null' => null,
			'empty' => ''
		], $diff);
	}

	/**
	 * Test that difference() returns all elements not found in second array by comparing keys, values and with a callback.
	 */
	public function testDifferenceStrictWithValueCallback() {
		$diff = $this->object->difference([
			'integer' => 12345,
			'fake' => '',
			'string' => 'FOOBAR',
			'boolean' => true
		], [
			'strict' => true,
			'valueCallback' => function ($k1, $k2) {
				return strcasecmp($k1, $k2);
			}
		]);

		$this->assertEquals([
			'number' => '67890',
			'zero' => 0,
			'null' => null,
			'empty' => ''
		], $diff);
	}

	/**
	 * Test that difference() returns all elements not found in each array by key.
	 */
	public function testDifferenceAgainstKeys() {
		$diff = $this->object->difference([
			'integer' => 12345,
			'empty' => 'lies',
			'string' => 'Barbaz',
			'boolean' => false,
			'map' => ['key' => 'value']
		], [
			'on' => 'keys'
		]);

		$this->assertEquals([
			'number' => '67890',
			'null' => null,
			'zero' => 0
		], $diff);
	}

	/**
	 * Test that difference() returns all elements not found in each array by key with callback.
	 */
	public function testDifferenceAgainstKeysWithCallback() {
		$diff = $this->object->difference([
			'integer' => 12345,
			'empty' => 'lies',
			'string' => 'Barbaz',
			//'boolean' => false,
			'map' => ['key' => 'value']
		], [
			'on' => 'keys',
			'callback' => function($k1, $k2) {
				if ($k1 == $k2) {
					return 0;
				} else if ($k1 > $k2) {
					return 1;
				} else {
					return -1;
				}
			}
		]);

		$this->assertEquals([
			'number' => '67890',
			'boolean' => true,
			'null' => null,
			'zero' => 0
		], $diff);
	}

	/**
	 * Test that each() runs a callback on every item.
	 */
	public function testEach() {
		$this->object->each(function($value, $key) {
			if (is_string($value)) {
				return $value . '---';
			}

			return $value;
		}, false);

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890---',
			'string' => 'Foobar---',
			'boolean' => true,
			'null' => null,
			'zero' => 0,
			'empty' => '---',
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar']
		], $this->object->value());

		// recursive
		$this->object->each(function($value, $key) {
			if (is_string($value)) {
				return $value . '+++';
			}

			return $value;
		}, true);

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890---+++',
			'string' => 'Foobar---+++',
			'boolean' => true,
			'null' => null,
			'zero' => 0,
			'empty' => '---+++',
			'map' => ['foo' => 'bar+++'],
			'array' => ['foo+++', 'bar+++']
		], $this->object->value());
	}

	/**
	 * Test that equals() returns true if the array passed matches the current array.
	 */
	public function testEquals() {
		$this->assertFalse($this->object->equals([]));
		$this->assertTrue($this->object->equals($this->map));
	}

	/**
	 * Test that erase() removes items by value.
	 */
	public function testErase() {
		$this->object->erase(null)->erase('Foobar');

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'boolean' => true,
			'zero' => 0,
			'empty' => '',
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar']
		], $this->object->value());
	}

	/**
	 * Test that every() returns true if every element passes the callback.
	 */
	public function testEvery() {
		$this->assertTrue($this->object->every(function($value, $key) {
			return true;
		}));

		$this->assertFalse($this->object->every(function($value, $key) {
			return is_numeric($value);
		}));
	}

	/**
	 * Test that extract() returns a value based on dot notated key.
	 */
	public function testExtract() {
		$this->assertEquals(12345, $this->object->extract('integer'));
		$this->assertEquals(0, $this->object->extract('zero'));
		$this->assertEquals(null, $this->object->extract('null'));
		$this->assertEquals('bar', $this->object->extract('map.foo'));
	}

	/**
	 * Test that filter() removes falsey values.
	 */
	public function testFilter() {
		$this->object->filter();

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'zero' => 0,
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar']
		], $this->object->value());
	}

	/**
	 * Test that filter() works with a callback.
	 */
	public function testFilterCallback() {
		$this->object->filter(function($value) {
			return is_string($value);
		});

		$this->assertEquals([
			'number' => '67890',
			'string' => 'Foobar',
			'empty' => ''
		], $this->object->value());
	}

	/**
	 * Test that first() returns the first value.
	 */
	public function testFirst() {
		$this->object->append('last')->prepend('first');

		$this->assertEquals('first', $this->object->first());
		$this->assertEquals([
			'first',
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'null' => null,
			'zero' => 0,
			'empty' => '',
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar'],
			'last'
		], $this->object->value());
	}

	/**
	 * Test that flatten() squashes all nested arrays.
	 */
	public function testFlatten() {
		$this->object->flatten();

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'null' => null,
			'zero' => 0,
			'empty' => '',
			'map.foo' => 'bar',
			'array.0' => 'foo',
			'array.1' => 'bar'
		], $this->object->value());
	}

	/**
	 * Test that flip() switches values with keys.
	 */
	public function testFlip() {
		$this->object->flip();

		$this->assertEquals([
			'12345' => 'integer',
			'67890' => 'number',
			'Foobar' => 'string',
			1 => 'boolean',
			0 => 'zero',
			'map' => ['bar' => 'foo'],
			'array' => ['foo' => '', 'bar' => '']
		], $this->object->value());
	}

	/**
	 * Test that flush() empties the array.
	 */
	public function testFlush() {
		$this->object->flush();

		$this->assertEquals([], $this->object->value());
	}

	/**
	 * Test that get() returns a value based on key.
	 */
	public function testGet() {
		$this->assertEquals(12345, $this->object->get('integer'));
		$this->assertEquals('Foobar', $this->object->get('string'));
		$this->assertEquals(null, $this->object->get('null'));
		$this->assertEquals(null, $this->object->get('fakeKey'));
	}

	/**
	 * Test that has() returns true if the key exists.
	 */
	public function testHas() {
		$this->assertTrue($this->object->has('integer'));
		$this->assertTrue($this->object->has('string'));
		$this->assertTrue($this->object->has('map.foo'));
		$this->assertFalse($this->object->has('fakeKey'));
	}

	/**
	 * Test that indexOf() returns the numerical index of the key.
	 */
	public function testIndexOf() {
		$this->assertEquals(0, $this->object->indexOf('integer'));
		$this->assertEquals(2, $this->object->indexOf('string'));
		$this->assertEquals(7, $this->object->indexOf('map'));
		$this->assertEquals(-1, $this->object->indexOf('fakeKey'));
	}

	/**
	 * Test that isEmpty() returns true if the array is empty.
	 */
	public function testIsEmpty() {
		$this->assertFalse($this->object->isEmpty());

		$this->object->flush();
		$this->assertTrue($this->object->isEmpty());
	}

	/**
	 * Test that isNotEmpty() returns true if the array isn't empty.
	 */
	public function testIsNotEmpty() {
		$this->assertTrue($this->object->isNotEmpty());

		$this->object->flush();
		$this->assertFalse($this->object->isNotEmpty());
	}

	/**
	 * Test that keys() returns all the keys as an array.
	 */
	public function testKeys() {
		$this->assertEquals(['integer', 'number', 'string', 'boolean', 'null', 'zero', 'empty', 'map', 'array'], $this->object->keys());
	}

	/**
	 * Test that last() returns the last value in the array.
	 */
	public function testLast() {
		$this->object->append('last')->prepend('first');

		$this->assertEquals('last', $this->object->last());
		$this->assertEquals([
			'first',
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'null' => null,
			'zero' => 0,
			'empty' => '',
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar'],
			'last'
		], $this->object->value());
	}

	/**
	 * Test that length() returns the size of the array.
	 */
	public function testLength() {
		$this->assertEquals(9, $this->object->length());

		$this->object->append([1, 2]);
		$this->assertEquals(11, $this->object->length());
	}

	/**
	 * Test that map() applies a callback to every element.
	 */
	public function testMap() {
		$this->object->map(function($value) {
			if (!is_numeric($value)) {
				return 1;
			}

			return (int) $value;
		});

		$this->assertEquals([
			'integer' => 12345,
			'number' => 67890,
			'string' => 1,
			'boolean' => 1,
			'null' => 1,
			'zero' => 0,
			'empty' => 1,
			'map' => 1,
			'array' => 1
		], $this->object->value());
	}

	/**
	 * Test that merge() merges an array with the current one.
	 */
	public function testMerge() {
		$this->object->merge([
			'string' => 'Barbaz',
			'zero' => 1,
			'boolean' => false,
			'new' => 'key',
			'map' => ['foo' => 'baz']
		]);

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Barbaz',
			'boolean' => false,
			'null' => null,
			'zero' => 1,
			'empty' => '',
			'map' => ['foo' => 'baz'],
			'array' => ['foo', 'bar'],
			'new' => 'key'
		], $this->object->value());
	}

	/**
	 * Test that prepend() adds elements to the beginning of the array.
	 */
	public function testPrepend() {
		$this->object->prepend('prepend')->prepend(['prepend-array', 'prepend-array']);

		$this->assertEquals([
			'prepend-array',
			'prepend-array',
			'prepend',
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'null' => null,
			'zero' => 0,
			'empty' => '',
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar']
		], $this->object->value());
	}

	/**
	 * Test that product() returns the product of all the values.
	 */
	public function testProduct() {
		$this->assertEquals(0, $this->object->product());
	}

	/**
	 * Test that random() returns a random value.
	 */
	public function testRandom() {
		$this->assertNotEquals('random', $this->object->random());
	}

	/**
	 * Test that reduce() reduces the array to a number based on the values.
	 */
	public function testReduce() {
		$this->assertEquals(80235, $this->object->reduce(function($result, $value) {
			if (is_numeric($value)) {
				return $result + $value;
			}

			return $result;
		}));
	}

	/**
	 * Test that remove() unsets a key.
	 */
	public function testRemove() {
		$this->object->remove('null')->remove('map.foo');

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'zero' => 0,
			'empty' => '',
			'map' => [],
			'array' => ['foo', 'bar']
		], $this->object->value());
	}

	/**
	 * Test that reverse() swaps the order.
	 */
	public function testReverse() {
		$this->object->reverse();

		$this->assertEquals([
			'array' => ['foo', 'bar'],
			'map' => ['foo' => 'bar'],
			'empty' => '',
			'zero' => 0,
			'null' => null,
			'boolean' => true,
			'string' => 'Foobar',
			'number' => '67890',
			'integer' => 12345
		], $this->object->value());
	}

	/**
	 * Test that shuffle() randomizes the order.
	 */
	public function testShuffle() {
		$this->object->shuffle();

		$this->assertNotEquals($this->map, $this->object->value());
	}

	/**
	 * Test that slice() extracts a range of keys/values.
	 */
	public function testSlice() {
		$this->assertEquals(['integer' => 12345], $this->object->slice(0, 1));

		$this->assertEquals([
			'zero' => 0,
			'empty' => '',
			'map' => ['foo' => 'bar']
		], $this->object->slice(5, 3));

		$this->assertEquals([
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar']
		], $this->object->slice(7));
	}

	/**
	 * Test that some() returns true if some of the values pass the callback.
	 */
	public function testSome() {
		$this->assertTrue($this->object->some(function($value, $key) {
			return ($value === true);
		}));

		$this->assertTrue($this->object->some(function($value, $key) {
			return ($value !== false);
		}));
	}

	/**
	 * Test that sort() will arrange in array in order, preserving (or not) keys, and reversing.
	 */
	public function testSort() {
		$map = new Map([
			'a' => 5,
			'b' => 3,
			'c' => 4,
			'd' => 1,
			'e' => 2
		]);

		// by value
		$map->sort();

		$this->assertEquals([
			'd' => 1,
			'e' => 2,
			'b' => 3,
			'c' => 4,
			'a' => 5
		], $map->value());

		// reverse it
		$map->sort(true);

		$this->assertEquals([
			'a' => 5,
			'c' => 4,
			'b' => 3,
			'e' => 2,
			'd' => 1,
		], $map->value());

		// by value with no preserving
		$map->sort(['preserve' => false]);

		$this->assertEquals([1, 2, 3, 4, 5], $map->value());

		// reverse it
		$map->sort(['preserve' => false, 'reverse' => true]);

		$this->assertEquals([5, 4, 3, 2, 1], $map->value());
	}

	/**
	 * Test that sort() will arrange in array in order, preserving (or not) keys, and reversing while checking a callback.
	 */
	public function testSortWithCallback() {
		$map = new Map([
			'a' => 5,
			'b' => 3,
			'c' => 4,
			'd' => 1,
			'e' => 2
		]);

		$callback = function($a, $b) {
			if ($a == $b) {
				return 0;
			} else if ($a > $b) {
				return 1;
			} else {
				return -1;
			}
		};

		// by value with callback
		$map->sort($callback);

		$this->assertEquals([
			'd' => 1,
			'e' => 2,
			'b' => 3,
			'c' => 4,
			'a' => 5
		], $map->value());

		// reverse it
		$map->sort(true);

		$this->assertEquals([
			'a' => 5,
			'c' => 4,
			'b' => 3,
			'e' => 2,
			'd' => 1,
		], $map->value());

		// by value with no preserving
		$map->sort(['preserve' => false, 'callback' => $callback]);

		$this->assertEquals([1, 2, 3, 4, 5], $map->value());

		// reverse it
		$map->sort(['preserve' => false, 'reverse' => true]);

		$this->assertEquals([5, 4, 3, 2, 1], $map->value());
	}

	/**
	 * Test that sort() will arrange an array by keys.
	 */
	public function testSortKeys() {
		$map = new Map([
			'a' => 5,
			'b' => 3,
			'c' => 4,
			'd' => 1,
			'e' => 2
		]);

		// by key
		$map->sort(['on' => 'keys']);

		$this->assertEquals([
			'a' => 5,
			'b' => 3,
			'c' => 4,
			'd' => 1,
			'e' => 2,
		], $map->value());

		// reverse it
		$map->sort(true);

		$this->assertEquals([
			'd' => 1,
			'e' => 2,
			'c' => 4,
			'b' => 3,
			'a' => 5,
		], $map->value());
	}

	/**
	 * Test that sort() will arrange an array by keys while checking a callback.
	 */
	public function testSortKeysWithCallback() {
		$map = new Map([
			'a' => 5,
			'b' => 3,
			'c' => 4,
			'd' => 1,
			'e' => 2
		]);

		$callback = function($a, $b) {
			if ($a == $b) {
				return 0;
			} else if ($a > $b) {
				return 1;
			} else {
				return -1;
			}
		};

		// by key
		$map->sort(['on' => 'keys', 'callback' => $callback]);

		$this->assertEquals([
			'a' => 5,
			'b' => 3,
			'c' => 4,
			'd' => 1,
			'e' => 2,
		], $map->value());

		// reverse it
		$map->sort(['on' => 'keys', 'reverse' => true, 'callback' => $callback]);

		$this->assertEquals([
			'd' => 1,
			'e' => 2,
			'c' => 4,
			'b' => 3,
			'a' => 5,
		], $map->value());
	}

	/**
	 * Test that sortNatural() arranges the items in a natural order preserving keys.
	 */
	public function testSortNatural() {
		$map = new Map([
			'item 109',
			'apple',
			'item 1',
			'item 5',
			'orange',
			'ITEM 10',
			'ITEM 55',
			'banana'
		]);

		// case-insensitive
		$map->sortNatural();

		$this->assertEquals([
			1 => 'apple',
			7 => 'banana',
			2 => 'item 1',
			3 => 'item 5',
			5 => 'ITEM 10',
			6 => 'ITEM 55',
			0 => 'item 109',
			4 => 'orange'
		], $map->value());

		// case-sensitive
		$map->sortNatural(true);

		$this->assertEquals([
			1 => 'apple',
			7 => 'banana',
			5 => 'ITEM 10',
			6 => 'ITEM 55',
			2 => 'item 1',
			3 => 'item 5',
			0 => 'item 109',
			4 => 'orange'
		], $map->value());
	}

	/**
	 * Test that splice() replaces a range of elements with a new array, while preserving keys.
	 */
	public function testSplice() {
		$splice = $this->object->splice(1, 5, ['spliced' => true]);

		$this->assertEquals([
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'null' => null,
			'zero' => 0,
		], $splice);

		$this->assertEquals([
			'integer' => 12345,
			'spliced' => true,
			'empty' => '',
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar']
		], $this->object->value());
	}

	/**
	 * Test that sum() adds up all the numeric values.
	 */
	public function testSum() {
		$this->assertEquals(80236, $this->object->sum());
	}

	/**
	 * Test that toString() returns a serialized form.
	 */
	public function testToString() {
		$serialized = 'a:9:{s:7:"integer";i:12345;s:6:"number";s:5:"67890";s:6:"string";s:6:"Foobar";s:7:"boolean";b:1;s:4:"null";N;s:4:"zero";i:0;s:5:"empty";s:0:"";s:3:"map";a:1:{s:3:"foo";s:3:"bar";}s:5:"array";a:2:{i:0;s:3:"foo";i:1;s:3:"bar";}}';

		$this->assertEquals($serialized, $this->object->toString());
		$this->assertEquals($serialized, (string) $this->object);
	}

	/**
	 * Test that unique() removes duplicate values.
	 */
	public function testUnique() {
		$this->object->unique();

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'null' => null,
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar']
		], $this->object->value());
	}

	/**
	 * Test that values() returns all values without keys.
	 */
	public function testValues() {
		$this->assertEquals([
			12345,
			'67890',
			'Foobar',
			true,
			null,
			0,
			'',
			['foo' => 'bar'],
			['foo', 'bar']
		], $this->object->values());
	}

	/**
	 * Test that set() can add and overwrite keys/values.
	 */
	public function testSet() {
		$this->object->set('key', 'value')->set('map.foo', 'overwritten');

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'null' => null,
			'zero' => 0,
			'empty' => '',
			'map' => ['foo' => 'overwritten'],
			'array' => ['foo', 'bar'],
			'key' => 'value'
		], $this->object->value());
	}

	/**
	 * Test that Map can be accessed like a normal array.
	 */
	public function testArrayAccess() {
		$this->assertTrue(isset($this->object['integer']));
		$this->assertTrue(isset($this->object['map']));
		$this->assertFalse(isset($this->object['fakeKey']));

		$this->assertEquals(true, $this->object['boolean']);
		$this->assertEquals('', $this->object['empty']);
		$this->assertEquals('bar', $this->object['map']['foo']);
		$this->assertEquals(null, $this->object['fakeKey']);

		$this->object[] = 'no-key';
		$this->object['key'] = 'value';

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'null' => null,
			'zero' => 0,
			'empty' => '',
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar'],
			0 => 'no-key',
			'key' => 'value'
		], $this->object->value());

		unset($this->object['number'], $this->object['string']);

		$this->assertEquals([
			'integer' => 12345,
			'boolean' => true,
			'null' => null,
			'zero' => 0,
			'empty' => '',
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar'],
			0 => 'no-key',
			'key' => 'value'
		], $this->object->value());
	}

	/**
	 * Test that Map can be iterated over like a normal array.
	 */
	public function testIterator() {
		$values = [];

		foreach ($this->object as $value) {
			$values[] = $value;
		}

		$this->assertEquals([
			12345,
			'67890',
			'Foobar',
			true,
			null,
			0,
			'',
			['foo' => 'bar'],
			['foo', 'bar']
		], $this->object->values());
	}

	/**
	 * Test that chaining methods works like a boss.
	 */
	public function testMethodChaining() {
		$map = new Map();
		$map->append('append')->prepend('prepend')->set('set.nested', 'value')->append([1, 2, 3, 4, 5]);

		$this->assertEquals([
			'prepend',
			'append',
			'set' => ['nested' => 'value'],
			1,
			2,
			3,
			4,
			5
		], $map->value());

		$map->prepend(['foo', 'bar'])->set('zero', '0')->set('null', null)->set('boolean', false)->each(function($value, $key) {
			if (is_numeric($value)) {
				return $value * 10;
			}

			return $value;
		});

		$this->assertEquals([
			'bar',
			'foo',
			'prepend',
			'append',
			'set' => ['nested' => 'value'],
			10,
			20,
			30,
			40,
			50,
			'zero' => 0,
			'null' => null,
			'boolean' => false
		], $map->value());

		$map->filter()->reverse(false);

		$this->assertEquals([
			'zero' => 0,
			50,
			40,
			30,
			20,
			10,
			'set' => ['nested' => 'value'],
			'append',
			'prepend',
			'foo',
			'bar'
		], $map->value());
	}

}