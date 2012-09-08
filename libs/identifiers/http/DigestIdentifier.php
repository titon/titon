<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\identifiers\http;

use titon\libs\identifiers\IdentifierAbstract;

/**
 * DigestIdentifier uses HTTP Digest Authentication to login and authenticate a user.
 *
 * @package	titon.libs.identifiers.http
 */
class DigestIdentifier extends IdentifierAbstract {

	public function authenticate() {}

	public function login() {}

	public function logout() {}

}