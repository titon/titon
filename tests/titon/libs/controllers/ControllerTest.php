<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\tests\TestCase;

/**
 * Test class for titon\libs\controllers\Controller.
 */
class ControllerTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new TitonLibsControllersMockController([
			'module' => 'module',
			'controller' => 'controller',
			'action' => 'action',
			'args' => [100, 25]
		]);
	}

	/**
	 * Test that dispatching executes the correct action and throws exceptions for invalid or private methods.
	 */
	public function testDispatchAction() {
		try {
			$this->object->dispatchAction(); // wrong action name
			$this->object->dispatchAction('noAction'); // wrong action name
			$this->object->dispatchAction('_private'); // underscored private action
			$this->object->dispatchAction('dispatchAction'); // method from parent
			$this->assertTrue(false);

		} catch (\Exception $e) {
			$this->assertTrue(true);
		}

		$this->assertEquals('actionNoArgs', $this->object->dispatchAction('action1'));
		$this->assertEquals('actionNoArgs', $this->object->dispatchAction('action1', ['foo', 'bar']));
		$this->assertEquals(125, $this->object->dispatchAction('action2'));
		$this->assertEquals(555, $this->object->dispatchAction('action2', [505, 50]));
		$this->assertEquals(335, $this->object->dispatchAction('action2', [335]));
		$this->assertEquals(0, $this->object->dispatchAction('action2', ['foo', 'bar']));
	}

	/**
	 * Test that forwarding the action dispatches correctly.
	 */
	public function testForwardAction() {
		try {
			$this->object->forwardAction('noAction');
			$this->object->forwardAction('_private');
			$this->object->forwardAction('dispatchAction');
			$this->assertTrue(false);

		} catch (\Exception $e) {
			$this->assertTrue(true);
		}

		$this->object->forwardAction('action1');

		$this->assertEquals('action1', $this->object->config->action);
		$this->assertEquals('action1', $this->object->engine->config->get('template.action'));

		$this->object->forwardAction('action2');

		$this->assertEquals('action2', $this->object->config->action);
		$this->assertEquals('action2', $this->object->engine->config->get('template.action'));
	}

	/**
	 * Test that runAction() correctly executes and modifies the passed controller.
	 */
	public function testRunAction() {
		$this->object->config->foo = 'bar';

		$this->assertEquals('bar', $this->object->config->foo);
		$this->assertArrayNotHasKey('test', $this->object->config->get());

		$this->object->runAction(new TitonLibsControllersMockAction());

		$this->assertNotEquals('bar', $this->object->config->foo);
		$this->assertEquals('baz', $this->object->config->foo);
		$this->assertArrayHasKey('test', $this->object->config->get());
	}

	/**
	 * Test that throwing an error setups up the correct error state in the view engine.
	 */
	public function testThrowError() {
		$this->object->throwError(404);

		$this->assertEquals('404 - Not Found', $this->object->engine->data('pageTitle'));
		$this->assertEquals('404', $this->object->engine->config->get('template.action'));
		$this->assertEquals('error', $this->object->engine->config->layout);
		$this->assertTrue($this->object->engine->config->error);

		$this->object->throwError(500);

		$this->assertEquals('500 - Internal Server Error', $this->object->engine->data('pageTitle'));
		$this->assertEquals('500', $this->object->engine->config->get('template.action'));

		$this->object->throwError('customError', ['pageTitle' => 'Custom Error']);

		$this->assertEquals('Custom Error', $this->object->engine->data('pageTitle'));
		$this->assertEquals('customError', $this->object->engine->config->get('template.action'));

		$this->object->throwError('another_error');

		$this->assertEquals('Another Error', $this->object->engine->data('pageTitle'));
		$this->assertEquals('another_error', $this->object->engine->config->get('template.action'));
	}

}

class TitonLibsControllersMockController extends titon\libs\controllers\ControllerAbstract {

	public function action1() {
		return 'actionNoArgs';
	}

	public function action2($arg1, $arg2 = 0) {
		return $arg1 + $arg2;
	}

	public function _private() {
		return 'wontBeCalled';
	}

}

class TitonLibsControllersMockAction extends titon\libs\actions\ActionAbstract {

	public function run() {
		$this->controller->config->set([
			'foo' => 'baz',
			'test' => 'value'
		]);
	}

}