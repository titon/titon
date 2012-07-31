<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\fixtures;

use titon\base\Base;
use titon\libs\traits\Cacheable;

/**
 * Fixture for titon\libs\augments.
 *
 * @package	titon.tests.fixtures
 */
class AugmentFixture extends Base {
	use Cacheable;

	const YES = true;
	const NO = false;

	public $publicProp;
	protected $protectedProp;
	private $privateProp;

	public static $staticPublicProp;
	protected static $staticProtectedProp;
	private static $staticPrivateProp;

	public function publicMethod() { }
	protected function protectedMethod() { }
	private function privateMethod() { }

	public static function staticPublicMethod() { }
	protected static function staticProtectedMethod() { }
	private static function staticPrivateMethod() { }

	public function serialize() { }
	public function unserialize($data) { }

}