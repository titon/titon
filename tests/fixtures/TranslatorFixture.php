<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\fixtures;

use titon\libs\translators\TranslatorAbstract;

/**
 * Fixture for titon\libs\translators\TranslatorAbstract.
 *
 * @package	titon.tests.fixtures
 */
class TranslatorFixture extends TranslatorAbstract {

	public function loadBundle($module, $locale) {}

}