<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\augments;

use titon\tests\TestCase;
use titon\tests\fixtures\AugmentFixture;
use titon\libs\augments\InfoAugment;

/**
 * Test class for titon\libs\augments\InfoAugment.
 */
class InfoAugmentTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new InfoAugment(new AugmentFixture());
	}

	public function testReflection() {

	}

	public function testClassName() {
		$this->assertEquals('titon\tests\fixtures\AugmentFixture', $this->object->className);
		$this->assertEquals('titon\tests\fixtures\AugmentFixture', $this->object->className());
	}

	public function testShortClassName() {
		$this->assertEquals('AugmentFixture', $this->object->shortClassName);
		$this->assertEquals('AugmentFixture', $this->object->shortClassName());
	}

	public function testNamespaceName() {
		$this->assertEquals('titon\tests\fixtures', $this->object->namespaceName);
		$this->assertEquals('titon\tests\fixtures', $this->object->namespaceName());
	}

	public function testFilePath() {
		$this->assertEquals('titon/tests/fixtures/AugmentFixture.php', $this->object->filePath);
		$this->assertEquals('titon/tests/fixtures/AugmentFixture.php', $this->object->filePath());
	}

	public function testMethods() {
		$methods = [
			'publicMethod', 'protectedMethod', 'privateMethod', 'staticPublicMethod', 'staticProtectedMethod', 'staticPrivateMethod',
			//'publicProp', 'protectedProp', 'privateProp', 'staticPublicProp', 'staticProtectedProp', 'staticPrivateProp',
			'serialize', 'unserialize', 'initialize', 'noop', 'toString', '__toString', '__construct',
			'getCache', 'setCache', 'toggleCache', 'removeCache', 'createCacheKey', 'flushCache', 'cache'
		];

		$this->assertArraysEqual($methods, $this->object->methods);
		$this->assertArraysEqual($methods, $this->object->methods());
	}

	public function testPublicMethods() {

	}

	public function testProtectedMethods() {

	}

	public function testPrivateMethods() {
		$methods = ['privateMethod', 'staticPrivateMethod'];

		$this->assertArraysEqual($methods, $this->object->privateMethods);
		$this->assertArraysEqual($methods, $this->object->privateMethods());
	}

	public function testStaticMethods() {

	}

	public function testProperties() {

	}

	public function testPublicProperties() {

	}

	public function testProtectedProperties() {

	}

	public function testPrivateProperties() {

	}

	public function testStaticProperties() {

	}

	public function testConstants() {

	}

	public function testTraits() {

	}

	public function testInterfaces() {

	}

	public function testParent() {

	}

}