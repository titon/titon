<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\tests\TestCase;
use titon\tests\fixtures\EnumFixture;

/**
 * Test class for titon\base\types\Enum.
 */
class EnumTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->warrior = new EnumFixture(EnumFixture::WARRIOR);
		$this->ranger = new EnumFixture(EnumFixture::RANGER);
		$this->mage = new EnumFixture(EnumFixture::MAGE);
	}

	/**
	 * Test values mapped through initialize().
	 */
	public function testConstructorArgs() {
		$this->assertEquals('Warrior', $this->warrior->name);
		$this->assertEquals('Ranger', $this->ranger->name);
		$this->assertEquals('Mage', $this->mage->name);

		$this->assertEquals(true, $this->warrior->melee);
		$this->assertEquals(false, $this->ranger->melee);
		$this->assertEquals(false, $this->mage->melee);

		$this->assertEquals(1000, $this->warrior->health);
		$this->assertEquals(500, $this->ranger->health);
		$this->assertEquals(300, $this->mage->health);

		$this->assertEquals(0, $this->warrior->energy);
		$this->assertEquals(250, $this->ranger->energy);
		$this->assertEquals(600, $this->mage->energy);
	}

	/**
	 * Test that toString() returns the type.
	 */
	public function testToString() {
		$this->assertEquals('0', (string) $this->warrior);
		$this->assertEquals('1', (string) $this->ranger);
		$this->assertEquals('2', (string) $this->mage);
	}

	/**
	 * Test that is() returns true if the object matches the constant enum.
	 */
	public function testIs() {
		$this->assertTrue($this->warrior->is(EnumFixture::WARRIOR));
		$this->assertFalse($this->ranger->is(EnumFixture::WARRIOR));
		$this->assertFalse($this->mage->is(EnumFixture::WARRIOR));

		$this->assertFalse($this->warrior->is(EnumFixture::RANGER));
		$this->assertTrue($this->ranger->is(EnumFixture::RANGER));
		$this->assertFalse($this->mage->is(EnumFixture::RANGER));

		$this->assertFalse($this->warrior->is(EnumFixture::MAGE));
		$this->assertFalse($this->ranger->is(EnumFixture::MAGE));
		$this->assertTrue($this->mage->is(EnumFixture::MAGE));
	}

	/**
	 * Test that the value returned matches.
	 */
	public function testValue() {
		$this->assertEquals(EnumFixture::WARRIOR, $this->warrior->value());
		$this->assertEquals(EnumFixture::RANGER, $this->ranger->value());
		$this->assertEquals(EnumFixture::MAGE, $this->mage->value());
	}

}