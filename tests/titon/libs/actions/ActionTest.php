<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\tests\TestCase;
use titon\tests\fixtures\ActionFixture;
use titon\tests\fixtures\ControllerFixture;

/**
 * Test class for titon\libs\actions\Action.
 */
class ActionTest extends TestCase {

	/**
	 * Test that run correctly executes and modifies the passed controller.
	 */
	public function testRun() {
		$controller = new ControllerFixture(['foo' => 'bar']);

		$this->assertEquals('bar', $controller->config->foo);
		$this->assertArrayNotHasKey('test', $controller->config->get());

		$action = new ActionFixture();
		$action->setController($controller)->run();

		$this->assertNotEquals('bar', $controller->config->foo);
		$this->assertEquals('baz', $controller->config->foo);
		$this->assertArrayHasKey('test', $controller->config->get());
	}

}