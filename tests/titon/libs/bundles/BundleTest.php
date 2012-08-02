<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\bundles;

use titon\tests\TestCase;
use titon\tests\fixtures\BundleFixture;
use titon\libs\readers\core\PhpReader;
use titon\libs\readers\core\IniReader;
use titon\libs\readers\core\JsonReader;
use titon\libs\readers\core\XmlReader;
use titon\libs\readers\gettext\PoReader;

/**
 * Test class for titon\libs\bundles\Bundle.
 */
class BundleTest extends TestCase {

	/**
	 * Test that addLocation() and getLocations() resolves correctly.
	 */
	public function testLocations() {
		$bundle = new BundleFixture();
		$bundle
			->addLocation('/some/path')
			->addLocation([
				'\another\path',
				'/and\another\path'
			]);

		$this->assertEquals([
			'/some/path/',
			'/another/path/',
			'/and/another/path/'
		], $bundle->getLocations());

		// with config
		$bundle = new BundleFixture([
			'module' => 'foo',
			'container' => 'bar'
		]);
		$bundle
			->addLocation('/{module}/some/path')
			->addLocation([
				'\{container}\another\path',
				'/{module}/and\{container}/another\path'
			]);

		$this->assertEquals([
			'/foo/some/path/',
			'/bar/another/path/',
			'/foo/and/bar/another/path/'
		], $bundle->getLocations());
	}

	/**
	 * Test that addReader() and getReaders() resolves correctly.
	 */
	public function testReaders() {
		$bundle = new BundleFixture();
		$bundle
			->addReader(new PhpReader())
			->addReader(new IniReader())
			->addReader(new JsonReader())
			->addReader(new XmlReader())
			->addReader(new PoReader());

		$this->assertEquals(['php', 'ini', 'json', 'xml', 'po'], array_keys($bundle->getReaders()));
	}

}