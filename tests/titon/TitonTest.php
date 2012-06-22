<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\Titon;
use titon\base\Base;

/**
 * Test class for titon\Titon.
 */
class TitonTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test that get returns the correct installed object.
	 */
	public function testGet() {
		Titon::install('base', new Base(), true);

		$this->assertInstanceOf('titon\base\Base', Titon::get('base'));
		$this->assertInstanceOf('titon\base\Base', Titon::base());

		try {
			Titon::get('foobar');
		} catch (\Exception $e) {
			$this->assertTrue(true);
		}

		Titon::uninstall('base');
	}

	/**
	 * Test that installing and uninstalling (with locks) works correctly.
	 */
	public function testInstallAndUninstall() {
		Titon::install('base1', new Base());
		Titon::install('base2', new Base(), true);
		Titon::install('base3', new Base(), true);

		$this->assertInstanceOf('titon\base\Base', Titon::base1());
		$this->assertInstanceOf('titon\base\Base', Titon::base2());
		$this->assertInstanceOf('titon\base\Base', Titon::base3());

		Titon::uninstall('base1');

		try {
			Titon::base1();
			$this->assertTrue(false);
		} catch (\Exception $e) {
			$this->assertTrue(true);
		}

		Titon::uninstall('base2');

		try {
			Titon::base2();
			$this->assertTrue(true);
		} catch (\Exception $e) {
			$this->assertTrue(false);
		}

		try {
			Titon::install('base3', new Base());
			$this->assertTrue(false);
		} catch (\Exception $e) {
			$this->assertTrue(true);
		}
	}

}