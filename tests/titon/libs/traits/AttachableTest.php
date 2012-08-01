<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\bundles;

use titon\base\Base;
use titon\base\String;
use titon\base\Map;
use titon\tests\TestCase;
use titon\tests\fixtures\TraitFixture;
use \Exception;

/**
 * Test class for titon\libs\traits\Attachable.
 */
class AttachableTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new TraitFixture();

		// by closure
		$this->object->attachObject('base', function() {
			return new Base();
		});

		// by class
		$this->object->attachObject([
			'alias' => 'map',
			'class' => 'titon\base\Map'
		]);

		// by property
		$this->object->string = function() {
			return new String();
		};
	}

	/**
	 * Test that attachObject() and __set() will create relations and lazy load.
	 */
	public function testAttachObject() {
		$this->assertInstanceOf('titon\base\Base', $this->object->base);
		$this->assertInstanceOf('titon\base\Map', $this->object->map);
		$this->assertInstanceOf('titon\base\String', $this->object->string);

		// with interface requirement
		$this->object->attachObject([
			'alias' => 'map2',
			'class' => 'titon\base\Map',
			'interface' => 'ArrayAccess'
		]);

		$this->object->attachObject([
			'alias' => 'string2',
			'interface' => 'ArrayAccess'
		], function() {
			return new String();
		});

		$this->assertInstanceOf('titon\base\Map', $this->object->map2);

		try {
			$this->assertInstanceOf('titon\base\String', $this->object->string2);
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// error states
		try {
			$this->object->attachObject(['class' => 'titon\base\Base']);
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		try {
			$this->object->attachObject(['register' => false]);
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that detachObject() and __unset() will unset relations.
	 */
	public function testDetachObject() {
		$this->assertTrue(isset($this->object->relation));
		$this->object->detachObject('relation');
		$this->assertFalse(isset($this->object->relation));

		$this->assertTrue(isset($this->object->map));
		unset($this->object->map);
		$this->assertFalse(isset($this->object->map));
	}

	/**
	 * Test that getObject and __get() will initialize and return the object relation.
	 */
	public function testGetObject() {
		$this->assertInstanceOf('titon\base\Base', $this->object->base);
		$this->assertInstanceOf('titon\base\Base', $this->object->getObject('base'));

		$this->assertInstanceOf('titon\base\Map', $this->object->map);
		$this->assertInstanceOf('titon\base\Map', $this->object->getObject('map'));

		$this->assertInstanceOf('titon\base\String', $this->object->string);
		$this->assertInstanceOf('titon\base\String', $this->object->getObject('string'));

		// restricted
		$this->object->restrictObject('map');

		$this->assertEquals(null, $this->object->map);
		$this->assertEquals(null, $this->object->getObject('map'));

		// not attached
		try {
			$this->object->getObject('fake');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that hasObject() and __isset() will return true of the relation has been defined.
	 */
	public function testHasObject() {
		$this->object->string->set('Forcing this object to be instantiated!');

		$this->assertTrue(isset($this->object->string));
		$this->assertTrue($this->object->hasObject('map'));

		$this->assertFalse($this->object->hasObject('fake'));
	}

	/**
	 * Test that allowObject() and restrictObject() will lock scope.
	 */
	public function testAllowRestrictObject() {
		$this->assertInstanceOf('titon\base\Base', $this->object->base);

		$this->object->restrictObject('base');
		$this->assertEquals(null, $this->object->base);

		$this->object->allowObject('base');
		$this->assertInstanceOf('titon\base\Base', $this->object->base);
	}

	/**
	 * Test that deep nested relations will chain correctly.
	 */
	public function testChaining() {
		$this->assertInstanceOf('titon\tests\fixtures\TraitFixture', $this->object->relation);

		// we can go as deep as we want
		$this->assertInstanceOf('titon\tests\fixtures\TraitFixture', $this->object->relation->relation->relation->relation->relation->relation->relation->relation->relation->relation->relation);
	}

}