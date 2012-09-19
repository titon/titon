<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\security;

use titon\base\Base;

/**
 * @todo
 *
 * @package	titon.security
 */
class Acl extends Base {

	protected $_roles = [];

	public function addRole($role, $parent = null) {

	}

	public function allow($role, array $permissions) {

	}

	public function deny($role, array $permissions) {

	}

	public function isAllowed() {

	}

	public function isRole() {

	}

}