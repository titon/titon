<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

include_once dirname(__DIR__) . '/bootstrap.php';

use \titon\Titon;

/**
 * Test class for \titon\Titon.
 */
class TitonTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test that get returns the correct installed object.
	 */
	public function testGet() {
		Titon::install('base', new \titon\base\Base(), true);

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
		Titon::install('base1', new \titon\base\Base());
		Titon::install('base2', new \titon\base\Base());
		Titon::install('base3', new \titon\base\Base(), true);

		$this->assertInstanceOf('titon\base\Base', Titon::base1());
		$this->assertInstanceOf('titon\base\Base', Titon::base2());
		$this->assertInstanceOf('titon\base\Base', Titon::base3());

		Titon::uninstall('base1');

		try {
			Titon::base1();
		} catch (\Exception $e) {
			$this->assertTrue(true);
		}

		try {
			Titon::uninstall('base2');
		} catch (\Exception $e) {
			$this->assertTrue(true);
		}

		try {
			Titon::install('base3', new \titon\base\Base());
		} catch (\Exception $e) {
			$this->assertTrue(true);
		}
	}

}