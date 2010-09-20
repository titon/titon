<?php
/**
 * Custom built exception handler that extends the base PHP exception class.
 * When an exception is thrown it outputs an error in development, and logs an error in production.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\log;

use \titon\log\Debugger;

/**
 * Exception Handler Class
 *
 * @package		Titon
 * @subpackage	Titon.Log
 */
class Exception extends \Exception {  

	/**
	 * How to handle caught errors: log if in production, debug if in development.
	 * Is also the registered handler for dealing with uncaught exceptions.
	 *
	 * @access public
	 * @param Exception $Exception
	 * @return void
	 */
	public function log($Exception) {
		if (!$Exception) {
			$Exception = $this;
		}
		
		$trace = $Exception->getTrace();
		$method = $trace[0]['class'] . $trace[0]['type'] . $trace[0]['function'] .'()';
		$response = $method .': '. $Exception->getMessage();

        if ($code = $Exception->getCode()) {
            $response .= ' (Code: '. $code .')';
        }

		Debugger::error($Exception->getCode(), $response, $Exception->getFile(), $Exception->getLine());
	}
	
}
