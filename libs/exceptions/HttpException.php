<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\exceptions;

use \titon\Exception;
use \titon\constant\Http;

/**
 * Base Exception for HTTP exceptions.
 *
 * @package	titon.libs.exceptions
 */
class HttpException extends Exception {

	/**
	 * Set the HTTP status code and message.
	 *
	 * @access public
	 * @param $message
	 * @param $code
	 * @param $previous
	 * @return void
	 */
	public function __construct($message, $code, $previous) {
		if ($code && !isset(Http::$statusCodes[$code])) {
			$code = null;
		}

		if ($code && empty($message)) {
			$message = Http::$statusCodes[$code];
		}

		parent::__construct($message, $code, $previous);
	}

}
