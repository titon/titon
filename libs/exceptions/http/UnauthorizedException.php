<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\exceptions;

use \titon\libs\exceptions\HttpException;

/**
 * Represents an HTTP 401 error.
 *
 * @package	titon.libs.exceptions
 */
class UnauthorizedException extends HttpException {

	/**
	 * Set the HTTP status code and message.
	 *
	 * @access public
	 * @param $message
	 * @param $code
	 * @param $previous
	 * @return void
	 */
	public function __construct($message, $code = 401, $previous) {
		parent::__construct($message, $code, $previous);
	}

}
