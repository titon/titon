<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\fixtures;

use titon\libs\listeners\ListenerAbstract;
use titon\libs\controllers\Controller;
use titon\libs\engines\Engine;

/**
 * Fixture for titon\libs\listeners\Listener.
 *
 * @package	titon.tests.fixtures
 */
class ListenerFixture extends ListenerAbstract {

	public function startup() {}
	public function shutdown() {}
	public function preDispatch() {}
	public function postDispatch() {}
	public function preProcess(Controller $controller) {}
	public function postProcess(Controller $controller) {}
	public function preRender(Engine $engine) {}
	public function postRender(Engine $engine) {}

}