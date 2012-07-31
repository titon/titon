<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\augments;

use titon\tests\TestCase;
use titon\libs\augments\ConfigAugment;
use \Exception;

/**
 * Test class for titon\libs\augments\ConfigAugment.
 */
class ConfigAugmentTest extends TestCase {

	/**
	 * Default config used for autoboxing.
	 */
	public $defaults = [
		'boolean' => true,
		'integer' => 12345,
		'string' => 'foobar',
		'float' => 50.25,
		'array' => [
			'key' => 'value'
		]
	];

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new ConfigAugment([], $this->defaults);
	}

	/**
	 * Test that get(), and __get() all return a value defined by a key.
	 */
	public function testGet() {
		// all
		$this->assertEquals($this->defaults, $this->object->get());

		// key
		$this->assertEquals(true, $this->object->boolean);
		$this->assertEquals(true, $this->object->get('boolean'));
		$this->assertEquals(true, $this->object['boolean']);
		$this->object->boolean = false;
		$this->assertEquals(false, $this->object->get('boolean'));

		$this->assertEquals('foobar', $this->object->string);
		$this->assertEquals('foobar', $this->object->get('string'));
		$this->assertEquals('foobar', $this->object['string']);
		$this->object->string = 'barbaz';
		$this->assertEquals('barbaz', $this->object->get('string'));

		// nested
		// can't use object notation for nested
		// nor can you set values on nested using array access
		$this->assertEquals('value', $this->object->get('array.key'));
		$this->assertEquals('value', $this->object['array']['key']);
		$this->object->set('array.key', 'var');
		$this->assertEquals('var', $this->object['array']['key']);

		// non-existent keys throw exceptions
		try {
			$this->object->get('fakeKey');
			$this->assertTrue(false);

		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that set() and __set() will set a value and autobox if the default exists.
	 */
	public function testSet() {
		// autobox with boolean
		$this->object->set('boolean', false);
		$this->assertEquals(false, $this->object->boolean);

		$this->object->boolean = 1;
		$this->assertEquals(true, $this->object->boolean);

		$this->object->set('boolean', 'true');
		$this->assertEquals(true, $this->object->boolean);

		$this->object->boolean = null;
		$this->assertEquals(false, $this->object->boolean);

		// autobox with integer
		$this->object->set('integer', 666);
		$this->assertEquals(666, $this->object->integer);

		$this->object->integer = '1337';
		$this->assertEquals(1337, $this->object->integer);

		$this->object->set('integer', true);
		$this->assertEquals(1, $this->object->integer);

		$this->object->integer = 'string';
		$this->assertEquals(0, $this->object->integer);

		$this->object->set('integer', '123str');
		$this->assertEquals(123, $this->object->integer);

		// autobox with string
		$this->object->set('string', false);
		$this->assertEquals('', $this->object->string);

		$this->object->string = true;
		$this->assertEquals('1', $this->object->string);

		$this->object->set('string', 1988);
		$this->assertEquals('1988', $this->object->string);

		$this->object->string = 'string';
		$this->assertEquals('string', $this->object->string);

		// autobox with float
		$this->object->set('float', 50);
		$this->assertEquals(50, $this->object->float);

		$this->object->float = '20.15';
		$this->assertEquals(20.15, $this->object->float);

		$this->object->set('float', '15.5$');
		$this->assertEquals(15.5, $this->object->float);

		$this->object->float = false;
		$this->assertEquals(0, $this->object->float);

		// autobox with nested array
		$this->object->set('array.key', 50);
		$this->assertEquals('50', $this->object['array']['key']);

		$this->object->set('array.key', true);
		$this->assertEquals('1', $this->object['array']['key']);

		// autobox with array
		$this->object->set('array', 50);
		$this->assertEquals([50], $this->object->array);

		$this->object->set('array', 'string');
		$this->assertEquals(['string'], $this->object->array);

		$this->object->set('array', ['foo', 'bar']);
		$this->assertEquals(['foo', 'bar'], $this->object->array);

		// wont autobox since the default didnt exist
		$this->object->set('custom', 100);
		$this->assertEquals(100, $this->object->get('custom'));

		$this->object->set('custom', true);
		$this->assertEquals(true, $this->object->get('custom'));

		$this->object->set('custom', 'test');
		$this->assertEquals('test', $this->object->get('custom'));
	}

	/**
	 * Test that has() and __isset() returns true if the key exists.
	 */
	public function testHas() {
		$this->assertTrue($this->object->has('integer'));
		$this->assertTrue(isset($this->object->integer));

		$this->assertTrue($this->object->has('array.key'));
		$this->assertTrue(isset($this->object['array']['key']));

		$this->assertFalse($this->object->has('fakeKey'));
		$this->assertFalse(isset($this->object->fakeKey));
	}

	/**
	 * Test that remove() and __unset() remove a key from the array.
	 */
	public function testRemove() {
		$this->object->remove('string')->remove('array');
		$this->assertEquals([
			'boolean' => true,
			'integer' => 12345,
			'float' => 50.25
		], $this->object->get());

		unset($this->object->float, $this->object['boolean']);
		$this->assertEquals(['integer' => 12345], $this->object->get());
	}

	/**
	 * Test that looping over the object works.
	 */
	public function testIterator() {
		$config = [];

		foreach ($this->object as $key => $value) {
			$config[$key] = $value;
		}

		$this->assertEquals($this->defaults, $config);
	}

	/**
	 * Test that count() returns the length of the array.
	 */
	public function testCount() {
		$this->assertEquals(5, $this->object->count());

		unset($this->object->string);
		$this->assertEquals(4, $this->object->count());
	}

}