<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\log;

use \titon\source\log\Debugger;

/**
 * Custom built exception handler that extends the base PHP exception class.
 * When an exception is thrown it outputs an error in development, and logs an error in production.
 *
 * @package	titon.source.log
 * @uses	titon\source\log\Debugger
 */
class Exception extends \Exception {  

	/**
	 * How to handle caught errors: log if in production, debug if in development.
	 * Is also the registered handler for dealing with uncaught exceptions.
	 *
	 * @access public
	 * @param Exception $exc
	 * @return void
	 */
	public function log(\Exception $exception) {
		if (!$exception) {
			$exception = $this;
		}

		$trace = $exception->getTrace();
		$method = $trace[0]['class'] . $trace[0]['type'] . $trace[0]['function'] .'()';
		$response = $method .': '. $exception->getMessage();
		$code = $exception->getCode();

		if ($code) {
			$response .= ' (Code: '. $code .')';
		}

		Debugger::error($code, $response, $exception->getFile(), $exception->getLine());
	}
	
}
