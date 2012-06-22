<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\libs\bundles\locales\LocaleBundle;

/**
 * Test class for titon\libs\bundles\locales\LocaleBundle.
 */
class LocaleBundleTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup bundles for specific conditions.
	 */
	public function setUp() {
		$this->parentBundle = new LocaleBundle(array(
			'bundle' => 'ex'
		));

		$this->bundleFormats = new LocaleBundle(array(
			'bundle' => 'ex_FM'
		));

		$this->bundleInflections = new LocaleBundle(array(
			'bundle' => 'ex_IN'
		));

		$this->bundleValidations = new LocaleBundle(array(
			'bundle' => 'ex_VA'
		));
	}

	/**
	 * Test that the locale meta data is parsed correctly.
	 * If the bundle has a parent, also test that the values between the two are merged correctly.
	 */
	public function testGetLocale() {
		$parentBundle = $this->parentBundle->getLocale();
		$bundleFormats = $this->bundleFormats->getLocale();
		$bundleInflections = $this->bundleInflections->getLocale();
		$bundleValidations = $this->bundleValidations->getLocale();

		// Parent
		$this->assertTrue(is_array($parentBundle));
		$this->assertEquals(array(
			'language' => 'ex',
			'id' => 'ex',
			'iso2' => 'ex',
			'iso3' => 'exp',
			'timezone' => '',
			'title' => 'Example Parent'
		), $parentBundle);

		// Formats
		$this->assertTrue(is_array($bundleFormats));
		$this->assertEquals(array(
			'language' => 'ex',
			'region' => 'FM',
			'id' => 'ex_FM',
			'iso2' => 'ex',
			'iso3' => array('exf', 'frm'),
			'timezone' => '',
			'title' => 'Example for Formats',
			'parent' => 'ex'
		), $bundleFormats);

		// Inflections
		$this->assertTrue(is_array($bundleInflections));
		$this->assertEquals(array(
			'language' => 'ex',
			'region' => 'IN',
			'id' => 'ex_IN',
			'iso2' => 'ex',
			'iso3' => 'inf',
			'timezone' => '',
			'title' => 'Example for Inflections',
			'parent' => 'ex'
		), $bundleInflections);

		// Validations
		$this->assertTrue(is_array($bundleValidations));
		$this->assertEquals(array(
			'language' => 'ex',
			'region' => 'VA',
			'id' => 'ex_VA',
			'iso2' => 'ex',
			'iso3' => 'val',
			'timezone' => '',
			'title' => 'Example for Validations',
			'parent' => 'ex'
		), $bundleValidations);

		// By key
		$this->assertEquals('ex', $this->parentBundle->getLocale('id'));
		$this->assertEquals('ex', $this->parentBundle->getLocale('iso2'));
		$this->assertEquals('exp', $this->parentBundle->getLocale('iso3'));
		$this->assertEquals('', $this->parentBundle->getLocale('timezone'));
		$this->assertEquals(null, $this->parentBundle->getLocale('fakeKey'));
	}

	/**
	 * Test that the formatting rules are parsed correctly.
	 * If the bundle has a parent, also test that the values between the two are merged correctly.
	 */
	public function testGetFormats() {
		$parentBundle = $this->parentBundle->getFormats();
		$bundleFormats = $this->bundleFormats->getFormats();
		$bundleInflections = $this->bundleInflections->getFormats();
		$bundleValidations = $this->bundleValidations->getFormats();

		$parentFormat = array(
			'date' => 'ex',
			'time' => 'ex',
			'datetime' => 'ex',
			'pluralForms' => 2,
			'pluralRule' => function() { }
		);

		// Parent
		$this->assertTrue(is_array($parentBundle));
		$this->assertEquals($parentFormat, $parentBundle);

		// Formats
		$this->assertTrue(is_array($bundleFormats));
		$this->assertEquals(array(
			'date' => 'ex_FM',
			'time' => 'ex',
			'datetime' => 'ex',
			'pluralForms' => 3,
			'pluralRule' => function() { }
		), $bundleFormats);

		// Inflections
		$this->assertTrue(is_array($bundleInflections));
		$this->assertEquals($parentFormat, $bundleInflections);

		// Validations
		$this->assertTrue(is_array($bundleValidations));
		$this->assertEquals($parentFormat, $bundleValidations);

		// By key
		$this->assertEquals('ex_FM', $this->bundleFormats->getFormats('date'));
		$this->assertEquals('ex', $this->bundleFormats->getFormats('time'));
		$this->assertEquals('ex', $this->bundleFormats->getFormats('datetime'));
		$this->assertEquals(3, $this->bundleFormats->getFormats('pluralForms'));
		$this->assertEquals(null, $this->bundleFormats->getFormats('fakeKey'));
	}

	/**
	 * Test that the inflection rules are parsed correctly.
	 * If the bundle has a parent, also test that the values between the two are merged correctly.
	 */
	public function testGetInflections() {
		$parentBundle = $this->parentBundle->getInflections();
		$bundleFormats = $this->bundleFormats->getInflections();
		$bundleInflections = $this->bundleInflections->getInflections();
		$bundleValidations = $this->bundleValidations->getInflections();

		$parentInflections = array(
			'irregular' => array('ex' => 'irregular'),
			'uninflected' => array('ex'),
			'plural' => array('ex' => 'plural'),
			'singular' => array('ex' => 'singular')
		);

		// Parent
		$this->assertTrue(is_array($parentBundle));
		$this->assertEquals($parentInflections, $parentBundle);

		// Formats
		$this->assertTrue(is_array($bundleFormats));
		$this->assertEquals($parentInflections, $bundleFormats);

		// Inflections
		$this->assertTrue(is_array($bundleInflections));
		$this->assertEquals(array(
			'irregular' => array('ex_IN' => 'irregular'),
			'uninflected' => array('ex'),
			'plural' => array('ex_IN' => 'plural'),
			'singular' => array('ex_IN' => 'singular')
		), $bundleInflections);

		// Validations
		$this->assertTrue(is_array($bundleValidations));
		$this->assertEquals($parentInflections, $bundleValidations);

		// By key
		$this->assertEquals(array('ex_IN' => 'irregular'), $this->bundleInflections->getInflections('irregular'));
		$this->assertEquals(array('ex_IN' => 'plural'), $this->bundleInflections->getInflections('plural'));
		$this->assertEquals(array('ex_IN' => 'singular'), $this->bundleInflections->getInflections('singular'));
		$this->assertEquals(array('ex'), $this->bundleInflections->getInflections('uninflected'));
		$this->assertEquals(null, $this->bundleInflections->getInflections('fakeKey'));
	}

	/**
	 * Test that the validation rules are parsed correctly.
	 * If the bundle has a parent, also test that the values between the two are merged correctly.
	 */
	public function testGetValidations() {
		$parentBundle = $this->parentBundle->getValidations();
		$bundleFormats = $this->bundleFormats->getValidations();
		$bundleInflections = $this->bundleInflections->getValidations();
		$bundleValidations = $this->bundleValidations->getValidations();

		$parentValidations = array(
			'phone' => 'ex',
			'postalCode' => 'ex',
			'ssn' => 'ex'
		);

		// Parent
		$this->assertTrue(is_array($parentBundle));
		$this->assertEquals($parentValidations, $parentBundle);

		// Formats
		$this->assertTrue(is_array($bundleFormats));
		$this->assertEquals($parentValidations, $bundleFormats);

		// Inflections
		$this->assertTrue(is_array($bundleInflections));
		$this->assertEquals($parentValidations, $bundleInflections);

		// Validations
		$this->assertTrue(is_array($bundleValidations));
		$this->assertEquals(array(
			'phone' => 'ex_VA',
			'postalCode' => 'ex',
			'ssn' => 'ex_VA'
		), $bundleValidations);

		// By key
		$this->assertEquals('ex_VA', $this->bundleValidations->getValidations('phone'));
		$this->assertEquals('ex_VA', $this->bundleValidations->getValidations('ssn'));
		$this->assertEquals('ex', $this->bundleValidations->getValidations('postalCode'));
		$this->assertEquals(null, $this->bundleValidations->getValidations('fakeKey'));
	}

	/**
	 * Test that parent bundles are loaded.
	 */
	public function testGetParent() {
		$this->assertEquals(null, $this->parentBundle->getParent());
		$this->assertInstanceOf('titon\libs\bundles\locales\LocaleBundle', $this->bundleFormats->getParent());
		$this->assertInstanceOf('titon\libs\bundles\locales\LocaleBundle', $this->bundleInflections->getParent());
		$this->assertInstanceOf('titon\libs\bundles\locales\LocaleBundle', $this->bundleValidations->getParent());
	}

}
