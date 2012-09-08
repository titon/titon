<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\identifiers;

/**
 * Interface for the identifiers library.
 *
 * @package	titon.libs.identifiers
 */
interface Identifier {

	/**
	 * Authenticate a user.
	 *
	 * @access public
	 * @return boolean
	 */
	public function authenticate();

	public function login();

	public function logout();

}