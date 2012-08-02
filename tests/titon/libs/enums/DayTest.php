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
use titon\libs\enums\Day;

/**
 * Test class for titon\libs\enums\Day.
 */
class DayTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new Day(Day::SATURDAY);
	}

	/**
	 * Test that initialize variables are set.
	 */
	public function testVariables() {
		$this->assertEquals(6, $this->object->order);
		$this->assertEquals('Saturday', $this->object->name);
		$this->assertEquals('Sat', $this->object->shortName);
		$this->assertEquals('saturday', $this->object->slug);
	}

	/**
	 * Test that is() returns true if the type passed is the same enum.
	 */
	public function testIs() {
		$this->assertTrue($this->object->is(Day::SATURDAY));
		$this->assertFalse($this->object->is(Day::WEDNESDAY));
	}

}