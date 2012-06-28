<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\fixtures;

use titon\libs\controllers\ControllerAbstract;

/**
 * Fixture for titon\libs\controllers\Controller.
 *
 * @package	titon.tests.fixtures
 */
class ControllerFixture extends ControllerAbstract {

	public function actionWithArgs($arg1, $arg2 = 0) {
		return $arg1 + $arg2;
	}

	public function actionNoArgs() {
		return 'actionNoArgs';
	}

	public function _actionPseudoPrivate() {
		return 'wontBeCalled';
	}

	protected function actionProtected() {
		return 'wontBeCalled';
	}

	private function actionPrivate() {
		return 'wontBeCalled';
	}

}