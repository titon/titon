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
		$this->foo = new EnumMock(EnumMock::FOO);
		$this->bar = new EnumMock(EnumMock::BAR);
		$this->baz = new EnumMock(EnumMock::BAZ);
	}

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
		$this->assertTrue($this->foo->is(EnumMock::FOO));
		$this->assertFalse($this->bar->is(EnumMock::FOO));
		$this->assertFalse($this->baz->is(EnumMock::FOO));

		$this->assertFalse($this->foo->is(EnumMock::BAR));
		$this->assertTrue($this->bar->is(EnumMock::BAR));
		$this->assertFalse($this->baz->is(EnumMock::BAR));

		$this->assertFalse($this->foo->is(EnumMock::BAZ));
		$this->assertFalse($this->bar->is(EnumMock::BAZ));
		$this->assertTrue($this->baz->is(EnumMock::BAZ));
	}

	/**
	 * Test that the value returned matches.
	 */
	public function testValue() {
		$this->assertEquals(EnumMock::FOO, $this->foo->value());
		$this->assertEquals(EnumMock::BAR, $this->bar->value());
		$this->assertEquals(EnumMock::BAZ, $this->baz->value());
	}
}

class EnumMock extends \titon\base\types\Enum {
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