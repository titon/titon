<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

include_once dirname(dirname(__DIR__)) . '/bootstrap.php';

/**
 * Test class for \titon\core\Registry.
 */
class RegistryTest extends \PHPUnit_Framework_TestCase {

	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = \titon\Titon::registry();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}

	/**
	 * Test that defining config sets get applied to the correct classes.
	 */
	public function testConfigure() {
		$this->object->configure('titon\base\Base', array('foo' => 'bar'));

		$object1 = $this->object->factory('titon\base\Base');

		$this->assertArrayHasKey('foo', $object1->config());
		$this->assertEquals('bar', $object1->config('foo'));

		$object2 = $this->object->factory('titon\libs\controllers\core\DefaultController');

		$this->assertArrayNotHasKey('foo', $object2->config());

		$this->object->flush();
	}

	/**
	 * Test that factory returns the correct object for the supplied namespace.
	 */
	public function testFactory() {
		$this->assertInstanceOf('titon\base\Base', $this->object->factory('titon\base\Base', array(), false));
		$this->assertInstanceOf('titon\base\Base', $this->object->factory('titon/base/Base', array(), false));
		$this->assertInstanceOf('titon\base\Base', $this->object->factory('\titon\base\Base', array(), false));
		$this->assertInstanceOf('titon\base\Base', $this->object->factory('/titon/base/Base', array(), false));

		$this->object->flush();
	}

	/**
	 * Test that flush resets all data and that listing returns the correct keys.
	 */
	public function testFlushAndListing() {
		$test = array();

		for ($i = 1; $i <= 10; $i++) {
			$this->object->store(new \titon\base\Base(), 'key' . $i);
			$test[] = 'key' . $i;
		}

		$registered = $this->object->listing();

		$this->assertEquals($test, $registered);
		$this->assertEquals(10, count($registered));

		$this->object->flush();

		$registered = $this->object->listing();

		$this->assertEquals(0, count($registered));
	}

	/**
	 * Test that has returns a boolean if the correct object has been stored.
	 */
	public function testHasAndStore() {
		for ($i = 1; $i <= 10; $i++) {
			$this->object->store(new \titon\base\Base(), 'key' . $i);
		}

		$this->assertTrue($this->object->has('key1'));
		$this->assertTrue($this->object->has('key4'));
		$this->assertTrue($this->object->has('key8'));
		$this->assertFalse($this->object->has('key20'));
		$this->assertFalse($this->object->has('key25'));
		$this->assertFalse($this->object->has('key28'));
	}

	/**
	 * Test that removing a registered object returns a correct boolean.
	 */
	public function testRemove() {
		for ($i = 1; $i <= 10; $i++) {
			$this->object->store(new \titon\base\Base(), 'key' . $i);
		}

		$this->assertTrue($this->object->has('key1'));
		$this->assertTrue($this->object->has('key4'));
		$this->assertTrue($this->object->has('key8'));

		$this->object->remove('key1');
		$this->object->remove('key4');
		$this->object->remove('key8');

		$this->assertFalse($this->object->has('key1'));
		$this->assertFalse($this->object->has('key4'));
		$this->assertFalse($this->object->has('key8'));
	}

}
