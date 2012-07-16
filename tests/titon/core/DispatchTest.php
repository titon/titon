<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\core;

use titon\Titon;
use titon\tests\TestCase;
use titon\tests\fixtures\DispatcherFixture;

/**
 * Test class for titon\core\Dispatch.
 */
class DispatchTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = Titon::dispatch();
		$this->object->setup(new DispatcherFixture(['scope' => 'all']));
		$this->object->setup(new DispatcherFixture(['scope' => 'module']), ['module' => 'users']);
		$this->object->setup(new DispatcherFixture(['scope' => 'controller']), ['controller' => 'profile']);
		$this->object->setup(new DispatcherFixture(['scope' => 'both']), ['module' => 'users', 'controller' => 'profile']);
	}

	/**
	 * Test that run() utilizes the correct dispatcher for the correct scope.
	 */
	public function testRun() {
		$_SERVER['PHP_SELF'] = '/index.php/';
		$_SERVER['REQUEST_URI'] = '/';

		Titon::router()->initialize();

		$dispatcher = $this->object->run(true);
		$this->assertInstanceOf('titon\libs\dispatchers\Dispatcher', $dispatcher);
		$this->assertEquals('all', $dispatcher->config->scope);

		// module
		$_SERVER['PHP_SELF'] = '/index.php/users';
		$_SERVER['REQUEST_URI'] = '/';

		Titon::router()->initialize();

		$dispatcher = $this->object->run(true);
		$this->assertInstanceOf('titon\libs\dispatchers\Dispatcher', $dispatcher);
		$this->assertEquals('module', $dispatcher->config->scope);

		// controller
		$_SERVER['PHP_SELF'] = '/index.php/pages/profile';
		$_SERVER['REQUEST_URI'] = '/';

		Titon::router()->initialize();

		$dispatcher = $this->object->run(true);
		$this->assertInstanceOf('titon\libs\dispatchers\Dispatcher', $dispatcher);
		$this->assertEquals('controller', $dispatcher->config->scope);

		// module, controller
		$_SERVER['PHP_SELF'] = '/index.php/users/profile';
		$_SERVER['REQUEST_URI'] = '/';

		Titon::router()->initialize();

		$dispatcher = $this->object->run(true);
		$this->assertInstanceOf('titon\libs\dispatchers\Dispatcher', $dispatcher);
		$this->assertEquals('both', $dispatcher->config->scope);
	}

}