<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\Titon;
use titon\tests\TestCase;
use titon\libs\dispatchers\front\FrontDispatcher;

/**
 * Test class for titon\libs\dispatchers\front\FrontDispatcher.
 */
class FrontDispatcherTest extends TestCase {

	protected function setUp() {
		$_SERVER['HTTP_HOST'] = 'domain.com';
		$_SERVER['PHP_SELF'] = '/root/index.php/pages/index';
		$_SERVER['REQUEST_URI'] = '/pages/index';

		Titon::app()->setup('pages', TITON_APP . 'modules/pages/', [
			'index' => 'IndexController',
			'missing-file' => 'MissingFileController'
		]);

		Titon::router()->initialize();
	}

	/**
	 *
	 */
	public function testRun() {
		// not loaded controller
		$dispatcher = new FrontDispatcher([
			'module' => 'pages',
			'controller' => 'foobar',
			'action' => 'index',
			'ext' => '',
			'args' => [],
			'query' => []
		]);

		try {
			$dispatcher->run();
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// missing file controller
		$dispatcher = new FrontDispatcher([
			'module' => 'pages',
			'controller' => 'missing-file',
			'action' => 'index',
			'ext' => '',
			'args' => [],
			'query' => []
		]);

		try {
			$dispatcher->run();
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// Should pass
		$dispatcher = new FrontDispatcher([
			'module' => 'pages',
			'controller' => 'index',
			'action' => 'index',
			'ext' => '',
			'args' => [],
			'query' => []
		]);

		$dispatcher->run();

		\titon\debug($dispatcher->controller->engine->content());
	}

}