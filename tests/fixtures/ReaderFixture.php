<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\fixtures;

use titon\libs\readers\ReaderAbstract;

/**
 * Fixture for titon\libs\readers\ReaderAbstract.
 *
 * @package	titon.tests.fixtures
 */
class ReaderFixture extends ReaderAbstract {

	const EXT = 'exp';

	public function parse() { }

}