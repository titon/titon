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

/**
 * Test class for titon\core\Router.
 */
class RouterTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = Titon::router();
	}

	public function testBuild() {
		// module, controller, action
		$this->assertEquals('/', $this->object->build());
		$this->assertEquals('/module/index', $this->object->build(array('module' => 'module')));
		$this->assertEquals('/module/index/action', $this->object->build(array('module' => 'module', 'action' => 'action')));
		$this->assertEquals('/module/controller/action', $this->object->build(array('module' => 'module', 'controller' => 'controller', 'action' => 'action')));
		$this->assertEquals('/pages/controller/action', $this->object->build(array('controller' => 'controller', 'action' => 'action')));
		$this->assertEquals('/dash-ed/camelCase/under-score', $this->object->build(array('module' => 'dash-ed', 'controller' => 'camelCase', 'action' => 'under_score')));
		$this->assertEquals('/s-p-a-c-e-s/1-2-3-numbers/punctuation', $this->object->build(array('module' => 's p a c e s', 'controller' => '1 2 3 numbers', 'action' => 'punc"@*#$*&)tuation')));

		// ext
		$this->assertEquals('/pages/index/index.html', $this->object->build(array('ext' => 'html')));
		$this->assertEquals('/pages/foo/bar.json', $this->object->build(array('controller' => 'foo', 'action' => 'bar', 'ext' => 'json')));
		$this->assertEquals('/api/index/index.xml', $this->object->build(array('module' => 'api', 'ext' => 'xml')));

		// arguments
		$this->assertEquals('/pages/index/index/1/foo/838293', $this->object->build(array(1, 'foo', 838293)));
		$this->assertEquals('/pages/index/index/1/foo/838293', $this->object->build(array('args' => [1, 'foo', 838293])));
	}

}