<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\log;

use titon\Titon;
use titon\utility\Inflector;

/**
 * A simple class that handles the logging of errors and debug messages to the filesystem.
 * Logs are categorized based on threat level, which determines the location of where it is to be written.
 *
 * @package	titon.log
 */
class Logger {

	/**
	 * Types of logging threat levels.
	 *
	 * @link http://en.wikipedia.org/wiki/Syslog#Severity_levels
	 */
	const EMERGENCY = 0;
	const ALERT = 1;
	const CRITICAL = 2;
	const ERROR = 3;
	const WARNING = 4;
	const NOTICE = 5;
	const INFO = 6;
	const DEBUG = 7;

	/**
	 * Disable the class to enforce static methods.
	 *
	 * @access private
	 */
	private function __construct() { }

	/**
	 * Wrapper function to log emergency errors.
	 *
	 * @access public
	 * @param string $message
	 * @return void
	 * @static
	 */
	public static function emergency($message) {
		self::write($message, self::EMERGENCY);
	}

	/**
	 * Wrapper function to log alerted errors.
	 *
	 * @access public
	 * @param string $message
	 * @return void
	 * @static
	 */
	public static function alert($message) {
		self::write($message, self::ALERT);
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
		self::write($message, self::CRITICAL);
	}

	/**
	 * Wrapper function to log error messages.
	 *
	 * @access public
	 * @param string $message
	 * @return void
	 * @static
	 */
	public static function error($message) {
		self::write($message, self::ERROR);
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
		self::write($message, self::WARNING);
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
		self::write($message, self::NOTICE);
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
		self::write($message, self::INFO);
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
		self::write($message, self::DEBUG);
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
	public static function write($message, $level = self::DEBUG) {
		if (!$message) {
			return;
		}

		$types = [
			self::EMERGENCY => 'Emergency',
			self::ALERT 	=> 'Alert',
			self::CRITICAL 	=> 'Critical',
			self::ERROR 	=> 'Error',
			self::WARNING 	=> 'Warning',
			self::NOTICE 	=> 'Notice',
			self::INFO 		=> 'Info',
			self::DEBUG 	=> 'Debug'
		];

		if (isset($types[$level])) {
			$type = $types[$level];
		} else {
			$type = 'Internal';
		}

		if (is_array($message)) {
			$message = print_r($message, true);
		}

		$file = Inflector::fileName($type, 'log', false);
		$message = '[' . date('Y-m-d H:i:s') . '] ' . $message;

		file_put_contents(APP_LOGS . $file, $message . "\n", FILE_APPEND | LOCK_EX);

		if ($level > self::WARNING) {
			if ($email = Titon::config()->get('Debug.email')) {
				mail($email, '[Titon Error] ' . $type, $message);
			}
		}
	}

}