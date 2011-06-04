<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

/**
 * @package	tests.titon.source.core
 * @uses	titon\source\core\Application
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Application
	 */
	protected $object;

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new Application();
		$this->object->setup('module');
	}

	/**
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}

	/**
	 * Validate that controllers are set when setting a module.
	 */
	public function testControllers() {
		$this->assertEquals(array(), $this->object->controllers('module'));

		$this->object->setup('users', array('login', 'logout'));
		$this->assertEquals(array('login', 'logout'), $this->object->controllers('users'));

		$this->object->setup('api', array('created', 'read'));
		$this->object->setup('api', array('update', 'delete'));
		$this->assertEquals(array('created', 'read', 'update', 'delete'), $this->object->controllers('api'));

		$this->object->setup('foobar', array(123, array('value'), ''));
		$this->assertEquals(array('123', 'Array'), $this->object->controllers('foobar'));

		// Get all controllers
		$this->assertEquals(array(
			'module' => array(),
			'users' => array('login', 'logout'),
			'api' => array('created', 'read', 'update', 'delete'),
			'foobar' => array('123', 'Array')
		), $this->object->controllers());
	}

	/**
	 * Validate that all modules are returned.
	 */
	public function testModules() {
		$this->object->setup('module');
		$this->object->setup('users');
		$this->object->setup('api');
		$this->assertEquals(array('module', 'users', 'api'), $this->object->modules());

		$this->object->setup('foo');
		$this->object->setup('bar');
		$this->assertEquals(array('module', 'users', 'api', 'foo', 'bar'), $this->object->modules());
	}

	/**
	 * Validate that a module is added; strings only.
	 */
	public function testSetup() {
		$this->object->setup('module');
		$this->assertEquals(array('module'), $this->object->modules());

		$this->object->setup('module', array('controller'));
		$this->assertEquals(array('module'), $this->object->modules());

		$this->object->setup('module', 'pages');
		$this->assertEquals(array('module'), $this->object->modules());

		$this->object->setup('users', array('logout'));
		$this->assertEquals(array('module', 'users'), $this->object->modules());

		$this->object->setup('users', 'login');
		$this->assertEquals(array('module', 'users'), $this->object->modules());

		$this->object->setup('api');
		$this->assertEquals(array('module', 'users', 'api'), $this->object->modules());

		$this->object->setup(array());
		$this->assertEquals(array('module', 'users', 'api', 'Array'), $this->object->modules());

		$this->object->setup(123);
		$this->assertEquals(array('module', 'users', 'api', 'Array', '123'), $this->object->modules());
	}

}
