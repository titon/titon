<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\dispatchers\front;

use titon\Titon;
use titon\tests\TestCase;
use titon\libs\dispatchers\front\FrontDispatcher;
use \Exception;

/**
 * Test class for titon\libs\dispatchers\front\FrontDispatcher.
 */
class FrontDispatcherTest extends TestCase {

	/**
	 * Setup test app.
	 */
	protected function setUp() {
		parent::setUp();

		Titon::app()->setup('pages', TITON_APP . 'modules/pages/', [
			'index' => 'IndexController',
			'missing-file' => 'MissingFileController'
		]);

		// Use by Controller::throwError()
		Titon::router()->initialize();
	}

	/**
	 * Test that run() renders the controller or throws exceptions.
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
			$this->assertTrue(false);
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
			$this->assertTrue(false);
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

		try {
			$dispatcher->run();
			$this->assertTrue(true);
		} catch (Exception $e) {
			$this->assertTrue(false);
		}
	}

}