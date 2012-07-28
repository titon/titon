<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\base;

use titon\base\String;
use titon\tests\TestCase;

/**
 * Test class for titon\base\Type.
 */
class TypeTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new String('Titon'); // Use String so we can use set()
		$this->object->set('Titon Framework');
	}

	/**
	 * Test that isRaw() returns true if the current value and raw value are equal.
	 */
	public function testIsRaw() {
		$this->assertFalse($this->object->isRaw());

		$this->object->set('Titon');
		$this->assertTrue($this->object->isRaw());
	}

	/**
	 * Test that serialize() returns the string serialized.
	 */
	public function testSerialize() {
		$this->assertEquals('s:15:"Titon Framework";', $this->object->serialize());
	}

	/**
	 * Test that raw() returns the raw (initial) value.
	 */
	public function testRaw() {
		$this->assertEquals('Titon', $this->object->raw());
	}

	/**
	 * Test that rawOf() returns a new Type object based off the raw value.
	 */
	public function testRawOf() {
		$type = $this->object->rawOf();

		$this->assertInstanceOf('titon\base\Type', $type);
		$this->assertEquals('Titon', $type->value());
	}

	/**
	 * Test that toString() returns the value as a string.
	 */
	public function testToString() {
		$this->assertEquals('Titon Framework', $this->object->toString());
		$this->assertEquals('Titon Framework', (string) $this->object);
	}

	/**
	 * Test that unserialize() will unserialize and set the string.
	 */
	public function testUnserialize() {
		$this->object->unserialize('s:5:"Titon";');

		$this->assertEquals('Titon', $this->object->value());
	}

	/**
	 * Test that value() returns the current value.
	 */
	public function testValue() {
		$this->assertEquals('Titon Framework', $this->object->value());

		$this->object->set('Titon PHP');
		$this->assertEquals('Titon PHP', $this->object->value());
	}

	/**
	 * Test that valueOf() returns a new Type object based off the current value.
	 */
	public function testValueOf() {
		$type = $this->object->valueOf();

		$this->assertInstanceOf('titon\base\Type', $type);
		$this->assertEquals('Titon Framework', $type->value());
	}

}