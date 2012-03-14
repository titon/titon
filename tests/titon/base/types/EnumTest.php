<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

include_once dirname(dirname(dirname(__DIR__))) . '/bootstrap.php';

/**
 * Test class for \titon\base\types\Enum.
 */
class EnumTest extends \PHPUnit_Framework_TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->foo = new MockEnum(MockEnum::FOO);
		$this->bar = new MockEnum(MockEnum::BAR);
		$this->baz = new MockEnum(MockEnum::BAZ);
	}

	/**
	 * Test values mapped through initialize().
	 */
	public function testConstructorArgs() {
		$this->assertEquals('Foo', $this->foo->name);
		$this->assertEquals('Bar', $this->bar->name);
		$this->assertEquals('Baz', $this->baz->name);

		$this->assertEquals(123, $this->foo->id);
		$this->assertEquals(null, $this->bar->id);
		$this->assertEquals(789, $this->baz->id);

		$this->assertFalse(false, $this->foo->bool);
		$this->assertFalse(false, $this->bar->bool);
		$this->assertTrue(true, $this->baz->bool);
	}

	/**
	 * Test that toString() returns the type.
	 */
	public function testToString() {
		$this->assertEquals('0', (string) $this->foo);
		$this->assertEquals('1', (string) $this->bar);
		$this->assertEquals('2', (string) $this->baz);
	}

	/**
	 * Test that is() returns true if the object matches the constant enum.
	 */
	public function testIs() {
		$this->assertTrue($this->foo->is(MockEnum::FOO));
		$this->assertFalse($this->bar->is(MockEnum::FOO));
		$this->assertFalse($this->baz->is(MockEnum::FOO));

		$this->assertFalse($this->foo->is(MockEnum::BAR));
		$this->assertTrue($this->bar->is(MockEnum::BAR));
		$this->assertFalse($this->baz->is(MockEnum::BAR));

		$this->assertFalse($this->foo->is(MockEnum::BAZ));
		$this->assertFalse($this->bar->is(MockEnum::BAZ));
		$this->assertTrue($this->baz->is(MockEnum::BAZ));
	}

	/**
	 * Test that the value returned matches.
	 */
	public function testValue() {
		$this->assertEquals(MockEnum::FOO, $this->foo->value());
		$this->assertEquals(MockEnum::BAR, $this->bar->value());
		$this->assertEquals(MockEnum::BAZ, $this->baz->value());
	}

}

class MockEnum extends \titon\base\types\Enum {

	const FOO = 0;
	const BAR = 1;
	const BAZ = 2;

	protected $_enums = array(
		self::FOO => array('Foo', 123),
		self::BAR => array('Bar'),
		self::BAZ => array('Baz', 789, true)
	);

	public function initialize($name, $id = null, $bool = false) {
		$this->name = $name;
		$this->id = $id;
		$this->bool = $bool;
	}

}