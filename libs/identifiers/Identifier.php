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

	/**
	 * Login a user.
	 *
	 * @access public
	 * @return boolean
	 */
	public function login();

	/**
	 * Logout a user.
	 *
	 * @access public
	 * @return boolean
	 */
	public function logout();

}