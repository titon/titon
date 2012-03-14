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
 * Test class for \titon\libs\actions\Action.
 */
class ActionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test that run correct executes and modifies the passed controller.
	 */
	public function testRun() {
		$controller = new MockController(array(
			'foo' => 'bar'
		));

		$this->assertEquals('bar', $controller->config('foo'));
		$this->assertArrayNotHasKey('test', $controller->config());

		$action = new MockAction();
		$action->setController($controller);
		$action->run();

		$this->assertNotEquals('bar', $controller->config('foo'));
		$this->assertEquals('baz', $controller->config('foo'));
		$this->assertArrayHasKey('test', $controller->config());
	}

}

class MockController extends \titon\libs\controllers\ControllerAbstract {

}

class MockAction extends \titon\libs\actions\ActionAbstract {

	public function run() {
		$this->controller->configure(array(
			'foo' => 'baz',
			'test' => 'value'
		));
	}

}