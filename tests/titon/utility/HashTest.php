<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\utility;

use titon\tests\TestCase;
use titon\utility\Hash;
use titon\utility\UtilityException;
use \stdClass;

/**
 * Test class for titon\utility\Hash.
 */
class HashTest extends TestCase {

	/**
	 * Multi-dimension array.
	 *
	 * @var array
	 */
	public $expanded = [
		'boolean' => true,
		'integer' => 123,
		'strings' => 'foobar',
		'numeric' => '1988',
		'empty' => [],
		'one' => [
			'depth' => 1,
			'two' => [
				'depth' => 2,
				'three' => [
					'depth' => 3,
					'false' => false,
					'true' => true,
					'null' => null,
					'zero' => 0,
					'four' => [
						'five' => [
							'six' => [
								'seven' => [
									'key' => 'We can go deeper!'
								]
							]
						]
					]
				]
			]
		]
	];

	/**
	 * Single-dimension array.
	 *
	 * @var array
	 */
	public $collapsed = [
		'boolean' => true,
		'integer' => 123,
		'strings' => 'foobar',
		'numeric' => '1988',
		'empty' => [],
		'one.depth' => 1,
		'one.two.depth' => 2,
		'one.two.three.depth' => 3,
		'one.two.three.false' => false,
		'one.two.three.true' => true,
		'one.two.three.null' => null,
		'one.two.three.zero' => 0,
		'one.two.three.four.five.six.seven.key' => 'We can go deeper!'
	];

	/**
	 * Test that depth() returns an integer for the number of tiers in the array.
	 */
	public function testDepth() {
		$data = $this->expanded;

		$data1 = $data;
		$data2 = $data;
		$data3 = $data;
		unset($data1['one']['two']['three']['four'], $data2['one']['two'], $data3['one']['two']['three']);

		$data4 = new stdClass();
		$data4->integer = 123;
		$data4->one = new stdClass();
		$data4->one->foo = 'bar';

		$this->assertEquals(0, Hash::depth([]));
		$this->assertEquals(8, Hash::depth($data));
		$this->assertEquals(4, Hash::depth($data1));
		$this->assertEquals(2, Hash::depth($data2));
		$this->assertEquals(3, Hash::depth($data3));
		$this->assertEquals(2, Hash::depth($data4));

		$this->assertEquals(0, Hash::depth([], true));
		$this->assertEquals(8, Hash::depth($data, true));
		$this->assertEquals(4, Hash::depth($data1, true));
		$this->assertEquals(2, Hash::depth($data2, true));
		$this->assertEquals(3, Hash::depth($data3, true));
		$this->assertEquals(2, Hash::depth($data4, true));

		foreach ([true, false, null, 123, 'foo'] as $type) {
			try {
				$this->assertEquals(0, Hash::depth($type));
			} catch (UtilityException $e) {
				$this->assertTrue(true);
			}
		}
	}

	/**
	 * Test that each() runs the callback on every value.
	 */
	public function testEach() {
		$data = [
			'integer' => 123,
			'number' => '456',
			'foo' => 'bar',
			'string' => 'test',
			'boolean' => true,
			'array' => [
				525,
				'boo'
			]
		];

		$this->assertEquals([
			'integer' => 246,
			'number' => 912,
			'foo' => 'barwtf',
			'string' => 'testwtf',
			'boolean' => true,
			'array' => [
				1050,
				'boowtf'
			]
		], Hash::each($data, function($value, $key) {
			if (is_numeric($value)) {
				return $value * 2;
			} else if (is_string($value)) {
				return $value . 'wtf';
			}

			return $value;
		}));

		$this->assertEquals([
			'integer' => 246,
			'number' => 912,
			'foo' => 'barwtf',
			'string' => 'testwtf',
			'boolean' => true,
			'array' => [
				525,
				'boo'
			]
		], Hash::each($data, function($value, $key) {
			if (is_numeric($value)) {
				return $value * 2;
			} else if (is_string($value)) {
				return $value . 'wtf';
			}

			return $value;
		}, false));
	}

	/**
	 * Test that every() returns true if all elements pass the callback.
	 */
	public function testEvery() {
		$callback = function($value, $key) {
			return is_int($value);
		};

		$this->assertTrue(Hash::every([ 123, 456 ], $callback));
		$this->assertFalse(Hash::every([ 123, '456' ], $callback));
		$this->assertFalse(Hash::every([ 123, '456', 'foo' ], $callback));
	}

	/**
	 * Test that expand() will expand a single-dimension array into a multi-dimension.
	 */
	public function testExpand() {
		$this->assertEquals($this->expanded, Hash::expand($this->collapsed));

		foreach ([true, false, null, 123, 'foo'] as $type) {
			$this->assertEquals([], Hash::expand($type));
		}
	}

	/**
	 * Test that extract() will return a value based on the dot notated path.
	 */
	public function testExtract() {
		$data = $this->expanded;

		foreach ($this->collapsed as $key => $value) {
			$this->assertEquals($value, Hash::extract($data, $key));
		}

		$this->assertEquals(null, Hash::extract($data, null));
		$this->assertEquals(null, Hash::extract($data, 'fake.path'));
		$this->assertEquals($data['one']['two']['three'], Hash::extract($data, 'one.two.three'));
		$this->assertEquals($data['one']['two']['three']['four']['five']['six'], Hash::extract($data, 'one.two.three.four.five.six'));

		foreach ([true, false, null, 123, 'foo'] as $type) {
			$this->assertEquals(null, Hash::extract($type, 'boolean'));
			$this->assertEquals(null, Hash::extract($data, $type));
		}
	}

	/**
	 * Test that filter() removes empty values excluding zeroes.
	 */
	public function testFilter() {
		$data = $this->expanded;

		$match1 = $data;
		$match2 = $data;
		unset($match1['empty'], $match2['empty'], $match1['one']['two']['three']['false'], $match1['one']['two']['three']['null']);

		$this->assertEquals($match1, Hash::filter($data));
		$this->assertEquals($match2, Hash::filter($data, false));

		$data = [
			'true' => true,
			'false' => false,
			'null' => null,
			'zero' => 0,
			'stringZero' => '0',
			'empty' => [],
			'array' => [
				'false' => false,
				'null' => null,
				'empty' => []
			]
		];

		$this->assertEquals([
			'true' => true,
			'zero' => 0,
			'stringZero' => '0'
		], Hash::filter($data));
	}

	/**
	 * Test that flatten() will flatten a multi-dimension array into a single-dimension.
	 */
	public function testFlatten() {
		$match = $this->collapsed;
		$match['empty'] = null;

		$this->assertEquals($match, Hash::flatten($this->expanded));
	}

	/**
	 * Test that flip() will replace keys with their value, and the value with the key. Will remove empty values first.
	 */
	public function testFlip() {
		$data = [
			'true' => true,
			'false' => false,
			'null' => null,
			'zero' => 0,
			'stringZero' => '0',
			'empty' => [],
			'array' => [
				'false' => false,
				'null' => null,
				'empty' => []
			]
		];

		$this->assertEquals([
			1 => 'true',
			0 => 'stringZero',
			'empty' => [],
			'array' => [
				'empty' => []
			]
		], Hash::flip($data));

		$data = [
			'foo' => 'bar',
			1 => 'one',
			2 => 'two',
			true,
			false,
			null,
			'key' => 'value',
			'baz' => 'bar',
		];

		$this->assertEquals([
			'bar' => 'baz',
			'one' => '',
			'two' => '',
			1 => '',
			'value' => 'key'
		], Hash::flip($data));

		$this->assertEquals([
			1 => 'boolean',
			123 => 'integer',
			'foobar' => 'strings',
			1988 => 'numeric',
			'empty' => [],
			'one' => [
				1 => 'depth',
				'two' => [
					2 => 'depth',
					'three' => [
						3 => 'depth',
						1 => 'true',
						0 => 'zero',
						'four' => [
							'five' => [
								'six' => [
									'seven' => [
										'We can go deeper!' => 'key'
									]
								]
							]
						]
					]
				]
			]
		], Hash::flip($this->expanded));
	}

	/**
	 * Test that get() returns the full set, or the set value based on the dot notated path.
	 */
	public function testGet() {
		$data = $this->expanded;

		$this->assertEquals($data, Hash::get($data));
		$this->assertEquals(true, Hash::get($data, 'boolean'));
		$this->assertEquals($data['one']['two']['three'], Hash::get($data, 'one.two.three'));
		$this->assertEquals($data['one']['two']['three']['four']['five']['six'], Hash::get($data, 'one.two.three.four.five.six'));
	}

	/**
	 * Test that has() returns a boolean if the key exists based on the dot notated path.
	 */
	public function testHas() {
		$data = $this->expanded;

		$this->assertTrue(Hash::has($data, 'boolean'));
		$this->assertTrue(Hash::has($data, 'empty'));
		$this->assertTrue(Hash::has($data, 'one.depth'));
		$this->assertTrue(Hash::has($data, 'one.two.depth'));
		$this->assertTrue(Hash::has($data, 'one.two.three.false'));
		$this->assertTrue(Hash::has($data, 'one.two.three.true'));
		$this->assertTrue(Hash::has($data, 'one.two.three.four.five.six.seven.key'));
		$this->assertTrue(Hash::has($data, 'one.two.three.null'));

		$this->assertFalse(Hash::has($data, 'one.two.three.some.really.deep.depth'));
		$this->assertFalse(Hash::has($data, 'foo'));
		$this->assertFalse(Hash::has($data, 'foo.bar'));
		$this->assertFalse(Hash::has($data, 'empty.key'));

		foreach ([true, false, null, 123, 'foo'] as $type) {
			$this->assertFalse(Hash::has($type, 'fake'));
			$this->assertFalse(Hash::has($type, null));
		}
	}

	/**
	 * Test that inject() will only add a value if the key doesn't exist.
	 */
	public function testInject() {
		$data = $this->expanded;
		$this->assertEquals($data, Hash::inject($data, 'one.depth', 2));

		$data['one']['foo'] = 'bar';
		$this->assertEquals($data, Hash::inject($data, 'one.foo', 'bar'));
	}

	/**
	 * Test that insert() adds data to the array based on the dot notated path.
	 */
	public function testInsert() {
		$data = [];

		foreach ($this->collapsed as $key => $value) {
			$data = Hash::insert($data, $key, $value);
		}

		$this->assertEquals($this->expanded, $data);
	}

	/**
	 * Test that isAlpha() returns true if all values are strings.
	 */
	public function testIsAlpha() {
		$this->assertTrue(Hash::isAlpha(['foo', 'bar']));
		$this->assertTrue(Hash::isAlpha(['foo' => 'bar', 'number' => '123'], false));
		$this->assertTrue(Hash::isAlpha(['bar', '123'], false));

		$this->assertFalse(Hash::isAlpha(['foo' => 'bar', 'number' => '123']));
		$this->assertFalse(Hash::isAlpha(['bar', '123']));
		$this->assertFalse(Hash::isAlpha(['foo' => 123]));
		$this->assertFalse(Hash::isAlpha([null]));
		$this->assertFalse(Hash::isAlpha([true]));
		$this->assertFalse(Hash::isAlpha([false]));
		$this->assertFalse(Hash::isAlpha([[]]));
		$this->assertFalse(Hash::isAlpha([new stdClass()]));
	}

	/**
	 * Test that isNumeric() returns true if all values are numbers.
	 */
	public function testIsNumeric() {
		$this->assertTrue(Hash::isNumeric(['123', 456]));
		$this->assertTrue(Hash::isNumeric(['foo' => 123, 'number' => '456']));

		$this->assertFalse(Hash::isNumeric(['foo', 'bar']));
		$this->assertFalse(Hash::isNumeric(['foo' => 'bar', 'number' => '123']));
		$this->assertFalse(Hash::isNumeric(['bar', '123']));
		$this->assertFalse(Hash::isNumeric([null]));
		$this->assertFalse(Hash::isNumeric([true]));
		$this->assertFalse(Hash::isNumeric([false]));
		$this->assertFalse(Hash::isNumeric([[]]));
		$this->assertFalse(Hash::isNumeric([new stdClass()]));
	}

	/**
	 * Test that keyOf() returns the key name based on the provided value; will drill down.
	 */
	public function testKeyOf() {
		$data = $this->expanded;

		$this->assertEquals(null, Hash::keyOf($data, 'fakeValue'));
		$this->assertEquals('boolean', Hash::keyOf($data, true));
		$this->assertEquals('one.two.three.depth', Hash::keyOf($data, 3));
	}

	/**
	 * Test that map() applies callback functions to all elements in the array.
	 */
	public function testMap() {
		$data = [
			'foo' => 'bar',
			'boolean' => true,
			'null' => null,
			'array' => [],
			'number' => 123
		];

		$this->assertEquals([
			'foo' => 'BAR',
			'boolean' => true,
			'null' => null,
			'array' => [],
			'number' => 123
		], Hash::map($data, 'strtoupper'));

		$this->assertEquals([
			'foo' => 0,
			'boolean' => 1,
			'null' => 0,
			'array' => [],
			'number' => 123
		], Hash::map($data, 'intval'));

		$this->assertEquals([
			'foo' => 'string',
			'boolean' => 'true',
			'null' => 'null',
			'array' => [],
			'number' => 'number'
		], Hash::map($data, function($value) {
			if (is_numeric($value)) {
				return 'number';
			} else if (is_bool($value)) {
				return $value ? 'true' : 'false';
			} else if (is_null($value)) {
				return 'null';
			} else if (is_string($value)) {
				return 'string';
			} else {
				return $value;
			}
		}));
	}

	/**
	 * Test that matches() returns true if 2 arrays are strict equal.
	 */
	public function testMatches() {
		$this->assertTrue(Hash::matches($this->expanded, $this->expanded));
		$this->assertTrue(Hash::matches([
			'foo' => 123,
			'bar' => 'baz',
			'array' => []
		], [
			'foo' => 123,
			'bar' => 'baz',
			'array' => []
		]));

		$this->assertFalse(Hash::matches($this->expanded, $this->collapsed));
		$this->assertFalse(Hash::matches([
			'foo' => '123',
			'bar' => 'baz',
			'array' => []
		], [
			'foo' => 123,
			'bar' => 'baz',
			'array' => []
		]));

		foreach ([true, false, null, 123, 'foo'] as $type) {
			$this->assertFalse(Hash::matches($this->expanded, $type));
		}
	}

	/**
	 * Test that merge() will correctly merge nested arrays.
	 */
	public function testMerge() {
		$data1 = [
			'foo' => 'bar',
			'boolean' => true,
			'string' => 'abc',
			'number' => 123,
			'one'
		];

		$data2 = [
			'foo' => 'baz',
			'boolean' => false,
			'string' => 'xyz',
			'number' => 456,
			'two'
		];

		$this->assertEquals([
			'foo' => 'baz',
			'boolean' => false,
			'string' => 'xyz',
			'number' => 456,
			'one',
			'two'
		], Hash::merge($data1, $data2));

		$data1['array'] = [
			'key' => 'value',
			123,
			true
		];

		$data2['array'] = [];

		$this->assertEquals([
			'foo' => 'baz',
			'boolean' => false,
			'string' => 'xyz',
			'number' => 456,
			'one',
			'two',
			'array' => [
				'key' => 'value',
				123,
				true
			]
		], Hash::merge($data1, $data2));

		$data2['array'] = [
			'key' => 'base',
			'foo' => 'bar',
			123
		];

		$this->assertEquals([
			'foo' => 'bar',
			'boolean' => true,
			'string' => 'abc',
			'number' => 123,
			'two',
			'array' => [
				'key' => 'value',
				'foo' => 'bar',
				123,
				123,
				true
			],
			'one'
		], Hash::merge($data2, $data1));
	}

	/**
	 * Test that overwrite() will only overwrite values that share the same keys.
	 */
	public function testOverwrite() {
		$data1 = [
			'foo' => 'bar',
			123,
			'array' => [
				'boolean' => true,
				'left' => 'left'
			]
		];

		$data2 = [
			'foo' => 'baz',
			456,
			'array' => [
				'boolean' => false,
				'right' => 'right'
			]
		];

		$this->assertEquals([
			'foo' => 'baz',
			456,
			'array' => [
				'boolean' => false,
				'left' => 'left'
			]
		], Hash::overwrite($data1, $data2));
	}

	/**
	 * Test that pluck() generates an array of all values found within path.
	 */
	public function testPluck() {
		$data = [
			[ 'name' => 'Miles', 'user' => [ 'id' => 1 ] ],
			[ 'name' => 'Foo', 'user' => [ 'id' => 2] ],
			[ 'key' => 'value', 'user' => [ 'id' => 3 ] ],
			[ 'name' => 'Bar', 'user' => [ 'id' => 4 ] ],
			[ 'name' => 'Baz', 'user' => [ 'id' => 5 ] ],
		];

		$this->assertEquals(['Miles', 'Foo', 'Bar', 'Baz'], Hash::pluck($data, 'name'));
		$this->assertEquals([1, 2, 3, 4, 5], Hash::pluck($data, 'user.id'));
	}

	/**
	 * Test that range() generates an array of numbers based on the start and stop values.
	 */
	public function testRange() {
		$this->assertEquals([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10], Hash::range(0, 10));
		$this->assertEquals([0 => 0, 2 => 2, 4 => 4, 6 => 6, 8 => 8, 10 => 10], Hash::range(0, 10, 2));
		$this->assertEquals([0 => 0, 3 => 3, 6 => 6, 9 => 9], Hash::range(0, 10, 3));
		$this->assertEquals([0 => 0, 13 => 13, 26 => 26, 39 => 39, 52 => 52, 65 => 65, 78 => 78, 91 => 91], Hash::range(0, 100, 13));
		$this->assertEquals([23 => 23, 29 => 29, 35 => 35, 41 => 41, 47 => 47, 53 => 53, 59 => 59, 65 => 65], Hash::range(23, 66, 6));

		$this->assertEquals([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10], Hash::range(0, 10, 1, false));
		$this->assertEquals([0, 2, 4, 6, 8, 10], Hash::range(0, 10, 2, false));
		$this->assertEquals([0, 3, 6, 9], Hash::range(0, 10, 3, false));
		$this->assertEquals([0, 13, 26, 39, 52, 65, 78, 91], Hash::range(0, 100, 13, false));
		$this->assertEquals([23, 29, 35, 41, 47, 53, 59, 65], Hash::range(23, 66, 6, false));
	}

	/**
	 * Test that remove() deletes elements in an array based on the dot notated path.
	 */
	public function testRemove() {
		$data = $this->expanded;
		$match = $data;

		unset($match['boolean']);
		$data = Hash::remove($data, 'boolean');
		$this->assertEquals($match, $data);

		unset($match['one']['depth']);
		$data = Hash::remove($data, 'one.depth');
		$this->assertEquals($match, $data);

		unset($match['one']['two']['depth']);
		$data = Hash::remove($data, 'one.two.depth');
		$this->assertEquals($match, $data);

		unset($match['one']['two']['three']['depth'], $match['one']['two']['three']['zero'], $match['one']['two']['three']['null']);
		$data = Hash::remove($data, 'one.two.three.depth');
		$data = Hash::remove($data, 'one.two.three.zero');
		$data = Hash::remove($data, 'one.two.three.null');
		$this->assertEquals($match, $data);

		unset($match['one']['two']['three']['four']['five']['six']['seven']['key']);
		$data = Hash::remove($data, 'one.two.three.four.five.six.seven.key');
		$this->assertEquals($match, $data);

		foreach ([true, false, null, 123, 'foo'] as $type) {
			$data = Hash::remove($data, $type);
			$this->assertEquals($match, $data);
		}
	}

	/**
	 * Test that set() inserts data into the set based on the dot notated path; an array can also be passed.
	 */
	public function testSet() {
		$data = $this->expanded;
		$match = $data;

		$data = Hash::set($data, 'key', 'value');
		$match['key'] = 'value';
		$this->assertEquals($match, $data);

		$data = Hash::set($data, 'key.key', 'value');
		$match['key'] = ['key' => 'value'];
		$this->assertEquals($match, $data);

		$data = Hash::set($data, array(
			'key.key.key' => 'value',
			'true' => true,
			'one.false' => false
		));
		$match['key']['key'] = ['key' => 'value'];
		$match['true'] = true;
		$match['one']['false'] = false;
		$this->assertEquals($match, $data);
	}

	/**
	 * Test that some() returns true if one value matches the callback condition.
	 */
	public function testSome() {
		$this->assertTrue(Hash::some([ 123, 'abc', true, null ], function($value, $key) {
			return is_string($value);
		}));

		$this->assertFalse(Hash::some([ 123, true, null ], function($value, $key) {
			return is_string($value);
		}));
	}

	/**
	 * Test that toArray() will convert nested objects to arrays.
	 */
	public function testToArray() {
		$object = new stdClass();
		$object->string = 'abc';
		$object->integer = 123;
		$object->boolean = true;
		$object->nested = new stdClass();
		$object->nested->foo = 'bar';

		$array = [
			'string' => 'abc',
			'integer' => 123,
			'boolean' => true,
			'nested' => [
				'foo' => 'bar'
			]
		];

		$this->assertEquals($array, Hash::toArray($object));

		foreach ([true, false, null, 123, 'foo'] as $type) {
			try {
				$this->assertEquals($array, Hash::toArray($type));
			} catch (UtilityException $e) {
				$this->assertTrue(true);
			}
		}
	}

	/**
	 * Test that toObject() will convert nested arrays to objects.
	 */
	public function testToObject() {
		$object = new stdClass();
		$object->string = 'abc';
		$object->integer = 123;
		$object->boolean = true;
		$object->nested = new stdClass();
		$object->nested->foo = 'bar';

		$array = [
			'string' => 'abc',
			'integer' => 123,
			'boolean' => true,
			'nested' => [
				'foo' => 'bar'
			]
		];

		$this->assertEquals($object, Hash::toObject($array));

		foreach ([true, false, null, 123, 'foo'] as $type) {
			try {
				$this->assertEquals($object, Hash::toObject($type));
			} catch (UtilityException $e) {
				$this->assertTrue(true);
			}
		}
	}

}