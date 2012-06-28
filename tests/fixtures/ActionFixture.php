<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\fixtures;

use titon\libs\actions\ActionAbstract;

/**
 * Fixture for titon\libs\actions\Action.
 *
 * @package	titon.tests.fixtures
 */
class ActionFixture extends ActionAbstract {

	public function run() {
		$this->_controller->config->set([
			'foo' => 'baz',
			'test' => 'value'
		]);
	}

}