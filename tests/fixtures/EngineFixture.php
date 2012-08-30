<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\fixtures;

use titon\libs\engines\EngineAbstract;

/**
 * Fixture for titon\libs\engines\EngineAbstract.
 *
 * @package	titon.tests.fixtures
 */
class EngineFixture extends EngineAbstract {

	public function open($path, array $variables = []) {}
	public function render($path, array $variables = []) {}
	public function run() {}

}