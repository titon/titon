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
 * Test class for titon\core\Application.
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase {

	public $module = 'module';
	public $path = __DIR__;
	public $controllers = array(
		'index' => 'IndexController',
		'test' => 'TestController',
		'foo' => 'FooController',
		'bar' => 'BarController'
	);

	protected $object;

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->path = titon\Titon::loader()->ds(__DIR__ . '/');

		$this->object = titon\Titon::app();
		$this->object->setup($this->module, $this->path, $this->controllers);
		$this->object->setup('otherModule', $this->path, array());
	}

	/**
	 * Test that the controllers are organized by module and returned as an array.
	 */
	public function testControllers() {
		$controllers = $this->object->getControllers();

		$this->assertArrayHasKey($this->module, $controllers);
		$this->assertArrayNotHasKey('noModule', $controllers);

		$this->assertEquals($this->controllers, $controllers[$this->module]);
	}

	/**
	 * Test that the returned module contains the correct data when setup().
	 */
	public function testModule() {
		$module = $this->object->getModule($this->module);

		$this->assertTrue(isset($module['name']));
		$this->assertEquals($this->module, $module['name']);

		$this->assertTrue(isset($module['path']));
		$this->assertEquals($this->path, $module['path']);

		$this->assertTrue(isset($module['controllers']));
		$this->assertEquals($this->controllers, $module['controllers']);
		$this->assertContains('IndexController', $module['controllers']);
		$this->assertNotContains('FakeController', $module['controllers']);
	}

	/**
	 * Test that all modules are returned correctly.
	 */
	public function testModules() {
		$modules = $this->object->getModules();

		$this->assertArrayHasKey($this->module, $modules);
		$this->assertArrayNotHasKey('noModule', $modules);

		$this->assertTrue(is_array($modules[$this->module]));
		$this->assertTrue(isset($modules['otherModule']));
		$this->assertTrue(empty($modules['otherModule']['controllers']));
	}

}