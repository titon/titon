<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\security;

use titon\libs\identifiers\Identifier;
use titon\libs\storage\Storage;

/**
 * @todo
 *
 * @package	titon.security
 */
class Auth {

	/**
	 * Identifier instance.
	 *
	 * @access protected
	 * @var \titon\libs\identifiers\Identifier
	 */
	protected $_identifier;

	/**
	 * Storage instance.
	 *
	 * @access protected
	 * @var \titon\libs\storage\Storage
	 */
	protected $_storage;

	public function authorize() {

	}

	public function authenticate() {

	}

	public function isAuthorized() {

	}

	public function isAuthenticated() {

	}

	public function login() {

	}

	public function logout() {

	}

	/**
	 * Set the Identifier instance.
	 *
	 * @access public
	 * @param \titon\libs\identifiers\Identifier $identifier
	 * @return \titon\security\Auth
	 * @chainable
	 */
	public function setIdentifier(Identifier $identifier) {
		$this->_identifier = $identifier;

		return $this;
	}

	/**
	 * Set the Storage instance.
	 *
	 * @access public
	 * @param \titon\libs\storage\Storage $storage
	 * @return \titon\security\Auth
	 * @chainable
	 */
	public function setStorage(Storage $storage) {
		$this->_storage = $storage;

		return $this;
	}

}