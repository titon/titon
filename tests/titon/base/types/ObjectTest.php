<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

include_once dirname(dirname(dirname(__DIR__))) . '/bootstrap.php';

/**
 * Test class for \titon\base\types\Object.
 */
class ObjectTest extends \PHPUnit_Framework_TestCase {

	protected $object;

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new \titon\base\types\Object(array(
			'property' => 'instantiatedProperty',
			'method' => function() {
				return 'instantiatedMethod';
			}
		));
	}

	/**
	 * Test that adding, removing, setting and calling methods work correctly.
	 */
	public function testMethods() {
		$this->object->addMethod('testMethod', function($self, $arg1, $arg2 = 0) {
			return $arg1 + $arg2;
		});

		$this->object->addMethod('testSelf', function($self) {
			return $self;
		});

		try {
			$this->object->addMethod('testMethod', function($self) {});
			$this->assertFalse(true);
		} catch (\Exception $e) {
			$this->assertTrue(true, $e->getMessage());
		}

		try {
			$this->object->addMethod(12345, function($self) {});
			$this->assertFalse(true);
		} catch (\Exception $e) {
			$this->assertTrue(true, $e->getMessage());
		}

		// instantiated
		$this->assertEquals('instantiatedProperty', $this->object->property);
		$this->assertEquals('instantiatedMethod', $this->object->method());

		// by method name
		$this->assertEquals(10, $this->object->testMethod(5, 5));
		$this->assertEquals(15, $this->object->testMethod(15));
		$this->assertEquals($this->object, $this->object->testSelf());

		// by get()
		$this->assertEquals(125, $this->object->getMethod('testMethod', array(25, 100)));
		$this->assertEquals(25, $this->object->getMethod('testMethod', array(0, 25)));
		$this->assertEquals($this->object, $this->object->getMethod('testSelf'));

		// has
		$this->assertTrue($this->object->hasMethod('testMethod'));
		$this->assertTrue($this->object->hasMethod('testSelf'));
		$this->assertFalse($this->object->hasMethod('fooBar'));

		// override previous method
		$this->object->setMethod('testMethod', function($self, $arg1 = '', $arg2 = '') {
			return $arg1 . $arg2;
		});

		$this->assertNotEquals(10, $this->object->testMethod(5, 5));
		$this->assertNotEquals(15, $this->object->testMethod(15, 0));

		$this->assertEquals('foobar', $this->object->testMethod('foo', 'bar'));
		$this->assertEmpty($this->object->testMethod());

		// remove
		$this->object->removeMethod('testMethod');
		$this->object->removeMethod('testSelf');

		$this->assertFalse($this->object->hasMethod('testMethod'));
		$this->assertFalse($this->object->hasMethod('testSelf'));

		try {
			$this->object->testMethod();
			$this->assertTrue(false);
		} catch (\Exception $e) {
			$this->assertTrue(true, $e->getMessage());
		}
	}

	/**
	 * Test that adding, removing, setting and calling properties work correctly.
	 */
	public function testProperties() {
		$this->object->addProperty('string', 'foobar');
		$this->object->addProperty('number', 123456);
		$this->object->addProperty('boolean', true);
		$this->object->addProperty('array', array());

		try {
			$this->object->addProperty(12345);
			$this->assertFalse(true);
		} catch (\Exception $e) {
			$this->assertTrue(true, $e->getMessage());
		}

		try {
			$this->object->addProperty('string');
			$this->assertFalse(true);
		} catch (\Exception $e) {
			$this->assertTrue(true, $e->getMessage());
		}

		// by prop name
		$this->assertTrue(is_string($this->object->string));
		$this->assertTrue(is_numeric($this->object->number));
		$this->assertTrue(is_bool($this->object->boolean));
		$this->assertTrue(is_array($this->object->array));

		$this->assertEquals('foobar', $this->object->string);
		$this->assertEquals(123456, $this->object->number);
		$this->assertEquals(true, $this->object->boolean);
		$this->assertEquals(array(), $this->object->array);

		// by get()
		$this->assertTrue(is_string($this->object->getProperty('string')));
		$this->assertTrue(is_numeric($this->object->getProperty('number')));
		$this->assertTrue(is_bool($this->object->getProperty('boolean')));
		$this->assertTrue(is_array($this->object->getProperty('array')));

		$this->assertEquals('foobar', $this->object->getProperty('string'));
		$this->assertEquals(123456, $this->object->getProperty('number'));
		$this->assertEquals(true, $this->object->getProperty('boolean'));
		$this->assertEquals(array(), $this->object->getProperty('array'));

		// has
		$this->assertTrue($this->object->hasProperty('string'));
		$this->assertTrue($this->object->hasProperty('number'));
		$this->assertTrue($this->object->hasProperty('boolean'));
		$this->assertTrue($this->object->hasProperty('array'));
		$this->assertFalse($this->object->hasProperty('fooBar'));

		// override
		$this->object->setProperty('string', 'notfoobar');
		$this->assertNotEquals('foobar', $this->object->getProperty('string'));

		// magic
		$this->assertTrue(isset($this->object->string));

		unset($this->object->string);
		$this->assertFalse(isset($this->object->string));

		$this->object->string = 'addedAgain!';
		$this->assertEquals('addedAgain!', $this->object->string);
	}

}
