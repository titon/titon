<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

include_once '../../../../bootstrap.php';

use \titon\libs\bundles\locales\LocaleBundle;

/**
 * Test class for \titon\libs\bundles\locales\LocaleBundleTest.
 */
class LocaleBundleTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup bundles for specific conditions.
	 */
	public function setUp() {
		$this->withParent = new LocaleBundle(array(
			'bundle' => 'en_US'
		));

		// Currently has no inflections, validations
		$this->noParent = new LocaleBundle(array(
			'bundle' => 'es'
		));
	}

	/**
	 * Test that parents and child merge inflections. If no inflections exist, an empty array should be present.
	 */
	public function testGetInflections() {
		$inflections = $this->withParent->getInflections();

		$this->assertTrue(is_array($inflections));
		$this->assertEquals(array('irregular', 'uninflected', 'plural', 'singular'), array_keys($inflections));

		//$inflections = $this->noParent->getInflections();

		//$this->assertTrue(is_array($inflections));
		//$this->assertEmpty($inflections);

		//print_r($this->withParent);
		//print_r($this->noParent);
	}

	/**
	 * Test that parent detection works correctly.
	 */
	public function testGetParents() {
		//print_r($this->withParent);
		//print_r($this->noParent);
		//$this->assertInstanceOf('\titon\libs\bundles\locales\LocaleBundle', $this->withParent->getParent());
		//$this->assertEquals(null, $this->noParent->getParent());
	}

}
