<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\log;

use \titon\Titon;

/**
 * A simple class that handles the logging of errors and debug messages to the filesystem.
 * Logs are categorized based on threat level, which determines the location of where it is to be written.
 * There are two files in which the logger uses: debug.log and error.log, both of which are located in the app/temp.
 *
 * @package	titon.log
 * @uses	titon\Titon
 */
class Logger {

	/**
	 * Log filenames.
	 */
	const ERROR_LOG = 'error.log';
	const DEBUG_LOG = 'debug.log';

	/**
	 * Types of logging threat levels.
	 */
	const CRITICAL = 1;
	const ALERT = 2;
	const WARNING = 3;
	const NOTICE = 4;
	const INFO = 5;
	const DEBUG = 6;

	/**
	 * Disable the class to enforce static methods.
	 *
	 * @access private
	 * @return void
	 */
	private function __construct() { }

	/**
	 * Wrapper function to log alerted errors.
	 *
	 * @access public
	 * @param string $message
	 * @return void
	 * @static
	 */
	public static function alert($message) {
		self::write('[' . date('d-M-Y H:i:s') . '] ' . $message, self::ALERT);
	}

	/**
	 * Wrapper function to log critical errors.
	 *
	 * @access public
	 * @param string $message
	 * @return void
	 * @static
	 */
	public static function critical($message) {
		self::write('[' . date('d-M-Y H:i:s') . '] ' . $message, self::CRITICAL);
	}

	/**
	 * Wrapper function to log debug messages.
	 *
	 * @access public
	 * @param string $message
	 * @return void
	 * @static
	 */
	public static function debug($message) {
		self::write('[' . date('d-M-Y H:i:s') . '] ' . $message, self::DEBUG);
	}

	/**
	 * Wrapper function to log informational messages.
	 *
	 * @access public
	 * @param string $message
	 * @return void
	 * @static
	 */
	public static function info($message) {
		self::write('[' . date('d-M-Y H:i:s') . '] ' . $message, self::INFO);
	}

	/**
	 * Wrapper function to log notices.
	 *
	 * @access public
	 * @param string $message
	 * @return void
	 * @static
	 */
	public static function notice($message) {
		self::write('[' . date('d-M-Y H:i:s') . '] ' . $message, self::NOTICE);
	}

	/**
	 * Wrapper function to log warnings.
	 *
	 * @access public
	 * @param string $message
	 * @return void
	 * @static
	 */
	public static function warning($message) {
		self::write('[' . date('d-M-Y H:i:s') . '] ' . $message, self::WARNING);
	}

	/**
	 * Writes a message to the error or debug log, depending on the threat level.
	 * Additionally, it will send you an email with the error message if debug.email is defined.
	 *
	 * @access public
	 * @param string $message
	 * @param int $level
	 * @return void
	 * @static
	 */
	public static function write($message, $level = 0) {
		if (!empty($message)) {
			switch ($level) {
				case self::CRITICAL:
					$type = 'Critical'; 
				break;
				case self::ALERT:
					$type = 'Alert'; 
				break;
				case self::WARNING:
					$type = 'Warning'; 
				break;
				case self::NOTICE:
					$type = 'Notice'; 
				break;
				case self::INFO:
					$type = 'Info'; 
				break;
				case self::DEBUG:
					$type = 'Debug'; 
				break;
				default:
					$type = 'Internal';
				break;
			}

			if ($level == self::DEBUG) {
				$file = self::DEBUG_LOG;
			} else {
				$file = self::ERROR_LOG;
				$message = '[' . $type . '] ' . $message;
			}
			
			file_put_contents(APP_TEMP. $file, $message ."\n", FILE_APPEND | LOCK_EX);

			if ($level >= self::WARNING) {
				$email = Titon::config()->get('Debug.email');

				if (!empty($email)) {
					mail($email, '[Titon Error] ' . $type, $message);
				}
			}
		}
	}

}