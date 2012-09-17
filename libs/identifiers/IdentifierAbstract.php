<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\identifiers;

use titon\Titon;
use titon\base\Base;
use titon\libs\identifiers\Identifier;
use titon\libs\traits\Attachable;

/**
 * Base class for all Identifiers to extend.
 *
 * @package	titon.libs.identifiers
 * @abstract
 */
abstract class IdentifierAbstract extends Base implements Identifier {
	use Attachable;

	/**
	 * Attach the Request and Response objects for later use.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('request', function() {
			return Titon::registry()->factory('titon\net\Request');
		});

		$this->attachObject('response', function() {
			return Titon::registry()->factory('titon\net\Response');
		});
	}

}