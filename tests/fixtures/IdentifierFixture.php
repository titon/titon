<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\fixtures;

use titon\libs\identifiers\IdentifierAbstract;

/**
 * Fixture for titon\libs\identifiers\Identifier.
 *
 * @package	titon.tests.fixtures
 */
class IdentifierFixture extends IdentifierAbstract {

	public function authenticate() {
		return ['username' => 'user', 'password' => 'pass'];
	}

	public function login() {
		return true;
	}

	public function logout() {
		return true;
	}

}