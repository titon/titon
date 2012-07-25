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
		$this->object = new Map($this->map);
	}

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

	public function testCompare() {

	}

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

	public function testContains() {
		$this->assertTrue($this->object->contains(12345));
		$this->assertTrue($this->object->contains('Foobar'));
		$this->assertTrue($this->object->contains(null));
		$this->assertFalse($this->object->contains(63453435));
		$this->assertFalse($this->object->contains('Barbaz'));
		$this->assertFalse($this->object->contains(false));
	}

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

	public function testDepth() {
		$this->assertEquals(2, $this->object->depth());

		$this->object->set('deep.deep.deep.depth', 1);
		$this->assertEquals(4, $this->object->depth());
	}

	public function testDifference() {

	}

	public function testEquals() {
		$this->assertFalse($this->object->equals([]));
		$this->assertTrue($this->object->equals($this->map));
	}

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

	public function testEvery() {
		$this->assertTrue($this->object->every(function($value, $key) {
			return true;
		}));

		$this->assertFalse($this->object->every(function($value, $key) {
			return is_numeric($value);
		}));
	}

	public function testExtract() {
		$this->assertEquals(12345, $this->object->extract('integer'));
		$this->assertEquals(0, $this->object->extract('zero'));
		$this->assertEquals(null, $this->object->extract('null'));
		$this->assertEquals('bar', $this->object->extract('map.foo'));
	}

	public function testFilter() {
		$this->object->filter();

		$this->assertEquals([
			'integer' => 12345,
			'number' => '67890',
			'string' => 'Foobar',
			'boolean' => true,
			'map' => ['foo' => 'bar'],
			'array' => ['foo', 'bar']
		], $this->object->value());
	}

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

	public function testFlush() {
		$this->object->flush();

		$this->assertEquals([], $this->object->value());
	}

	public function testGet() {
		$this->assertEquals(12345, $this->object->get('integer'));
		$this->assertEquals('Foobar', $this->object->get('string'));
		$this->assertEquals(null, $this->object->get('null'));
		$this->assertEquals(null, $this->object->get('fakeKey'));

		// array access
		$this->assertEquals(12345, $this->object['integer']);
		$this->assertEquals('Foobar', $this->object['string']);
		$this->assertEquals(null, $this->object['null']);
		$this->assertEquals(null, $this->object['fakeKey']);
	}

	public function testHas() {
		$this->assertTrue($this->object->has('integer'));
		$this->assertTrue($this->object->has('string'));
		$this->assertTrue($this->object->has('map.foo'));
		$this->assertFalse($this->object->has('fakeKey'));
	}

	public function testIndexOf() {
		$this->assertEquals(0, $this->object->indexOf('integer'));
		$this->assertEquals(2, $this->object->indexOf('string'));
		$this->assertEquals(7, $this->object->indexOf('map'));
		$this->assertEquals(-1, $this->object->indexOf('fakeKey'));
	}

	public function testIsEmpty() {
		$this->assertFalse($this->object->isEmpty());

		$this->object->flush();
		$this->assertTrue($this->object->isEmpty());
	}

	public function testIsNotEmpty() {
		$this->assertTrue($this->object->isNotEmpty());

		$this->object->flush();
		$this->assertFalse($this->object->isNotEmpty());
	}

	public function testKeys() {
		$this->assertEquals(['integer', 'number', 'string', 'boolean', 'null', 'zero', 'empty', 'map', 'array'], $this->object->keys());
	}

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

	public function testLength() {
		$this->assertEquals(9, $this->object->length());

		$this->object->append([1, 2]);
		$this->assertEquals(11, $this->object->length());
	}

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

	public function testProduct() {
		$this->assertEquals(0, $this->object->product());
	}

	public function testRandom() {
		$this->assertNotEquals('random', $this->object->random());
	}

	public function testReduce() {
		$this->assertEquals(80235, $this->object->reduce(function($result, $value) {
			if (is_numeric($value)) {
				return $result + $value;
			}

			return $result;
		}));
	}

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

	public function testShuffle() {
		$this->object->shuffle();

		$this->assertNotEquals($this->map, $this->object->value());
	}

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

	public function testSome() {
		$this->assertTrue($this->object->some(function($value, $key) {
			return ($value === true);
		}));

		$this->assertTrue($this->object->some(function($value, $key) {
			return ($value !== false);
		}));
	}

	public function testSort() {

	}

	public function testSortNatural() {

	}

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

}