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

	public $executed = [];

	public function startup() {
		$this->executed[] = 'startup';
	}

	public function shutdown() {
		$this->executed[] = 'shutdown';
	}

	public function preDispatch() {
		$this->executed[] = 'preDispatch';
	}

	public function postDispatch() {
		$this->executed[] = 'postDispatch';
	}

	public function preProcess(Controller $controller) {
		$this->executed[] = 'preProcess';
	}

	public function postProcess(Controller $controller) {
		$this->executed[] = 'postProcess';
	}

	public function preRender(Engine $engine) {
		$this->executed[] = 'preRender';
	}

	public function postRender(Engine $engine) {
		$this->executed[] = 'postRender';
	}

}