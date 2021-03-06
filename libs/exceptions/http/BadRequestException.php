<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\exceptions\http;

use titon\libs\exceptions\HttpException;

/**
 * Represents an HTTP 400 error.
 *
 * @package	titon.libs.exceptions.http
 */
class BadRequestException extends HttpException {

	/**
	 * Set the HTTP status code and message.
	 *
	 * @access public
	 * @param string $message
	 * @param int $code
	 * @param mixed $previous
	 */
	public function __construct($message, $code = 400, $previous = null) {
		parent::__construct($message, $code, $previous);
	}

}
