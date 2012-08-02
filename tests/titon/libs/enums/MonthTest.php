<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\enums;

use titon\tests\TestCase;
use titon\libs\enums\Month;

/**
 * Test class for titon\libs\enums\Month.
 */
class MonthTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new Month(Month::FEBRUARY);
	}

	/**
	 * Test that initialize variables are set.
	 */
	public function testVariables() {
		$this->assertEquals(2, $this->object->order);
		$this->assertEquals('February', $this->object->name);
		$this->assertEquals('Feb', $this->object->shortName);
		$this->assertEquals('february', $this->object->slug);
		$this->assertTrue(in_array($this->object->daysInMonth, [28, 29]));
	}

	/**
	 * Test that is() returns true if the type passed is the same enum.
	 */
	public function testIs() {
		$this->assertTrue($this->object->is(Month::FEBRUARY));
		$this->assertFalse($this->object->is(Month::DECEMBER));
	}

}