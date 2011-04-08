<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

/**
 * @package	tests.titon.source.core
 * @uses	titon\source\core\Application
 */
class ConfigTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Config
	 */
	protected $object;

	/**
	 * Default config.
	 *
	 * @var array
	 */
	protected $config = array(
		'name' => 'Titon',
		'salt' => '66c63d989368170aff46040ab2353923',
		'encoding' => 'UTF-8'
	);

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new Config();
		$this->object->set('App', $this->config);
	}

	/**
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}

	/**
	 * Test encoding defaults and overwriting works.
	 */
	public function testEncoding() {
		$this->assertEquals('UTF-8', $this->object->encoding());

		$this->object->set('App.encoding', 'UTF-16');
		$this->assertEquals('UTF-16', $this->object->encoding());

		$this->object->set('App.encoding', '');
		$this->assertEquals('UTF-8', $this->object->encoding());

		$this->object->set('App.encoding', null);
		$this->assertEquals('UTF-8', $this->object->encoding());
	}

	/**
	 * Test that get()ting data works correctly.
	 */
	public function testGet() {
		$this->assertEquals('Titon', $this->object->get('App.name'));

		$this->assertEquals($this->config, $this->object->get('App'));

		$this->assertEquals(array('App' => $this->config), $this->object->get());

		$this->assertEquals('', $this->object->get('App.nameFake'));
		
		$this->object->set('App.nameFake', 'NotTiton');
		$this->assertEquals('NotTiton', $this->object->get('App.nameFake'));
	}

	/**
	 * Does the config contain specific keys/values.
	 */
	public function testHas() {
		$this->assertTrue($this->object->has('App.name'));
		$this->assertTrue($this->object->has('App.encoding'));
		$this->assertFalse($this->object->has('App.foo'));
		$this->assertFalse($this->object->has('Foo.bar'));
	}

	/**
	 * @todo Implement testLoad().
	 */
	public function testLoad() {
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * Test that the App.name is fetched.
	 */
	public function testName() {
		$this->assertEquals('Titon', $this->object->name());

		$this->object->set('App.name', 'NotTiton');
		$this->assertEquals('NotTiton', $this->object->name());

		$this->object->set('App.name', '');
		$this->assertEmpty($this->object->name());
	}

	/**
	 * Test that the App.salt is fetched.
	 */
	public function testSalt() {
		$this->assertEquals($this->config['salt'], $this->object->salt());

		$this->object->set('App.salt', md5('Titoner'));
		$this->assertEquals(md5('Titoner'), $this->object->salt());

		$this->object->set('App.salt', '');
		$this->assertEmpty($this->object->salt());
	}

	/**
	 * Test that set()ting data works correctly.
	 */
	public function testSet() {
		$this->object->set('Debug', array(
			'level' => 1,
			'email' => 'titon@domain.com'
		));

		$this->assertEquals('titon@domain.com', $this->object->get('Debug.email'));

		$this->object->set('Debug.foo.bar', 'value');
		$this->assertArrayHasKey('foo', $this->object->get('Debug'));
		$this->assertEquals('value', $this->object->get('Debug.foo.bar'));

		$this->object->set('Debug.boolean', true);
		$this->assertTrue($this->object->get('Debug.boolean'));

		$this->object->set('Debug.boolean', false);
		$this->assertFalse($this->object->get('Debug.boolean'));

		$this->object->set('Debug.boolean', null);
		$this->assertNull($this->object->get('Debug.boolean'));

		$this->object->set('Debug.class', $this);
		$this->assertInstanceOf('\PHPUnit_Framework_TestCase', $this->object->get('Debug.class'));

		$this->object->set('Debug.really.really.deep.nested.array', $this->config);
		$this->assertArrayHasKey('name', $this->object->get('Debug.really.really.deep.nested.array'));
		$this->assertEquals($this->config, $this->object->get('Debug.really.really.deep.nested.array'));
	}

}
