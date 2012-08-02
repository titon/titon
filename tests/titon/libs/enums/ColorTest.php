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
use titon\libs\enums\Color;

/**
 * Test class for titon\libs\enums\Color.
 */
class ColorTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new Color(Color::GRAY);
	}

	/**
	 * Test that initialize variables are set.
	 */
	public function testVariables() {
		$this->assertEquals('808080', $this->object->hex);
		$this->assertEquals(128, $this->object->r);
		$this->assertEquals(128, $this->object->g);
		$this->assertEquals(128, $this->object->b);
	}

	/**
	 * Test that is() returns true if the type passed is the same enum.
	 */
	public function testIs() {
		$this->assertTrue($this->object->is(Color::GRAY));
		$this->assertFalse($this->object->is(Color::RED));
	}

}