<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\tests\TestCase;
use titon\libs\routes\core\DefaultRoute;

/**
 * Test class for titon\libs\routes\core\DefaultRoute.
 */
class DefaultRouteTest extends TestCase {

	/**
	 * Test that isSecure() returns true when secure is enabled and HTTPS is on.
	 */
	/*public function testIsSecure() {
		$route = new DefaultRoute('/', [], ['secure' => false]);
		$secureRoute = new DefaultRoute('/', [], ['secure' => true]);

		$this->assertTrue($route->isSecure()); // Will pass since it doesn't validate
		$this->assertFalse($secureRoute->isSecure());

		$_SERVER['HTTPS'] = 'on';

		$this->assertTrue($route->isSecure());
		$this->assertTrue($secureRoute->isSecure());
	}*/

	/**
	 * Test that isStatic() returns true when the route has no patterns.
	 */
	public function testIsStatic() {
		$route = new DefaultRoute('/', [], ['static' => false]);
		//$staticRoute = new DefaultRoute('/', [], ['static' => true]);

		$this->assertFalse($route->isStatic());
		//$this->assertTrue($staticRoute->isStatic());
		//$this->assertTrue($staticRoute->isStatic());
		//$this->assertTrue($staticRoute->isStatic());
	}

}