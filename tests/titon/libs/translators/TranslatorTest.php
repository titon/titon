<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\translators;

use titon\Titon;
use titon\tests\TestCase;
use titon\tests\fixtures\TranslatorFixture;
use \Exception;

/**
 * Test class for titon\libs\translators\Translator.
 */
class TranslatorTest extends TestCase {

	/**
	 * Test that parsing a translation key returns the correct module, catalog and id.
	 */
	public function testParseKey() {
		$object = new TranslatorFixture();

		$this->assertEquals(['module', 'catalog', 'id'], $object->parseKey('module.catalog.id'));
		$this->assertEquals(['module', 'catalog', 'id.multi.part'], $object->parseKey('module.catalog.id.multi.part'));
		$this->assertEquals(['module', 'catalog', 'id-dashed'], $object->parseKey('module.catalog.id-dashed'));
		$this->assertEquals(['module', 'catalog', 'idspecial27304characters'], $object->parseKey('module.catalog.id * special )*&2)*7304 characters'));
		$this->assertEquals(['Module', 'Catalog', 'id.CamelCase'], $object->parseKey('Module.Catalog.id.CamelCase'));
		$this->assertEquals(['m', 'c', 'i'], $object->parseKey('m.c.i'));
		$this->assertEquals([1, 2, 3], $object->parseKey('1.2.3'));

		$this->assertEquals([null, 'catalog', 'id'], $object->parseKey('catalog.id'));
		$this->assertEquals([null, 'root', 'id'], $object->parseKey('root.id'));
		$this->assertEquals([null, 'test', 'key'], $object->parseKey('test.key'));
		$this->assertEquals([null, 1, 2], $object->parseKey('1.2'));

		try {
			$object->parseKey('noModuleOrCatalog');
			$object->parseKey('not-using-correct-notation');

			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

}