<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

/**
 * Test class for titon\libs\actions\Action.
 */
class ActionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test that run correctly executes and modifies the passed controller.
	 */
	public function testRun() {
		$controller = new TitonLibsActionsMockController(['foo' => 'bar']);

		$this->assertEquals('bar', $controller->config->foo);
		$this->assertArrayNotHasKey('test', $controller->config->get());

		$action = new TitonLibsActionsMockAction();
		$action->setController($controller);
		$action->run();

		$this->assertNotEquals('bar', $controller->config->foo);
		$this->assertEquals('baz', $controller->config->foo);
		$this->assertArrayHasKey('test', $controller->config->get());
	}

}

class TitonLibsActionsMockController extends titon\libs\controllers\ControllerAbstract {

}

class TitonLibsActionsMockAction extends titon\libs\actions\ActionAbstract {

	public function run() {
		$this->controller->config->set([
			'foo' => 'baz',
			'test' => 'value'
		]);
	}

}