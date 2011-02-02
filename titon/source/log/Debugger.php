<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\log;

use \titon\source\Titon;
use \titon\source\log\Exception;
use \titon\source\log\Logger;

/**
 * Custom system to manage all internal and user created errors and thrown/uncaught exceptions.
 * Errors are displayed with a custom backtrace as well as logged to the filesystem (if passed to the Logger).
 *
 * @package titon.source.log
 * @uses	titon\source\Titon
 * @uses	titon\source\log\Exception
 * @uses	titon\source\log\Logger
 */
class Debugger {

	/**
	* Complete list of all internal errors types.
	*
	* @access public
	* @var array
	* @static
	*/
	public static $errorTypes = array(
		E_ERROR				=> 'Error',
		E_WARNING			=> 'Warning',
		E_PARSE				=> 'Parsing Error',
		E_NOTICE			=> 'Notice',
		E_CORE_ERROR		=> 'Core Error',
		E_CORE_WARNING		=> 'Core Warning',
		E_COMPILE_ERROR		=> 'Compile Error',
		E_COMPILE_WARNING	=> 'Compile Warning',
		E_USER_ERROR		=> 'User Error',
		E_USER_WARNING		=> 'User Warning',
		E_USER_NOTICE		=> 'User Notice',
		E_STRICT			=> 'Runtime Notice',
		E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
		E_DEPRECATED		=> 'Deprecated',
		E_USER_DEPRECATED	=> 'User Deprecated',
		E_ALL				=> 'All'
	);

	/**
	* Errors received during the current request.
	*
	* @access private
	* @var array
	* @static
	*/
	private static $__errors = array();

	/**
	* Disable the class to enforce static methods.
	*
	* @access private
	* @return void
	*/
	private function __construct() { }

	/**
	* Overwrite the error_handler. When in development output errors, throw exceptions in production.
	*
	* @access public
	* @param int $number
	* @param string $message
	* @param string $file
	* @param int $line
	* @param string $context
	* @return void
	* @static
	*/
	public static function error($number, $message, $file = null, $line = null, $context = null) {
		self::$__errors[] = compact($number, $message, $file, $line);

		if (Titon::config()->get('debug.level') > 0) {
			self::__output($number, $message, $file, $line, $context);
		} else {
			Logger::write(sprintf('[%s] %s: %s in %s on line %s.', date('d-M-Y H:i:s'), self::errorType($number), $message, $file, $line));
		}

		return true;
	}

	/**
	* Enable or disable error reporting dynamically during runtime.
	*
	* @access public
	* @param boolean $enabled
	* @return void
	* @static
	*/
	public static function errorReporting($enabled = true) {
		if (!is_bool($enabled)) {
			$enabled = true;
		}

		if ($enabled === true) {
			ini_set('error_reporting', E_ALL | E_STRICT);
		} else {
			ini_set('error_reporting', 0);
		}

		ini_set('display_errors', $enabled);
		ini_set('display_startup_errors', $enabled);
		ini_set('track_errors', $enabled);
	}

	/**
	* Determine the type of error received.
	*
	* @access public
	* @param int $code
	* @return string
	* @static
	*/
	public static function errorType($code = null) {
		if (isset(self::$errorTypes[$code])) {
			return self::$errorTypes[$code];
		}

		return 'Uncaught Exception';
	}

	/**
	* Export a formatted variable to be used.
	*
	* @access public
	* @param mixed $var
	* @return mixed
	* @static
	*/
	public static function export($var = null) {
		return var_export($var, true);
	}

	/**
	* Initialize the error/exception/debug handling depending on environment.
	*
	* @access public
	* @return void
	* @static
	*/
	public static function initialize() {
		if (!Titon::config()->get('debug')) {
			self::errorReporting(true);
		}

		ini_set('log_errors', true);
		ini_set('report_memleaks', true);
		ini_set('error_log', TEMP . Logger::ERROR_LOG);

		set_error_handler(array(__NAMESPACE__ .'Debugger', 'error'), E_ALL | E_STRICT);
		set_exception_handler(array(new Exception(), 'log'));
	}

	/**
	* Parse the backtrace's arguments array and format correctly for a return.
	*
	* @access public
	* @param mixed $arg
	* @return mixed
	* @static
	*/
	public static function parseArg($arg, $end = false) {
		switch (true) {
			case is_numeric($arg):
				return $arg;
			break;
			case is_bool($arg):
				return ($arg === true) ? 'true' : 'false';
			break;
			case is_string($arg):
				//return '"'. substr(htmlentities($arg), 0, 15) .'..."';
				return "'". htmlentities($arg) ."'";
			break;
			case is_array($arg):
				if ($end === true) {
					return 'array([Truncated])';
				} else {
					$args = array();
					foreach ($arg as $a) {
						$args[] = self::parseArg($a, true);
					}
					return 'array('. implode(', ', $args) .')';
				}
			break;
			case is_null($arg):
				return 'null';
			break;
			case is_object($arg):
				return get_class($arg) .'()';
			break;
			case is_resource($arg):
				return strtolower(get_resource_type($var));
			break;
		}
	}

	/**
	* Parse the file path to remove absolute path and replace with constant name.
	*
	* @access public
	* @param string $path
	* @return string
	* @static
	*/
	public static function parseFile($path) {
		if (empty($path)) {
			return '[Internal]';
		}

		if (strpos($path, APP) !== false) {
			$path = str_replace(APP, '[App]', $path);

		} else if (strpos($path, SOURCE) !== false) {
			$path = str_replace(SOURCE, '[Titon]', $path);

		} else if (strpos($path, LIBRARY) !== false) {
			$path = str_replace(LIBRARY, '[Library]', $path);

		} else if (strpos($path, VENDORS) !== false) {
			$path = str_replace(VENDORS, '[Vendors]', $path);
		}

		return $path;
	}

	/**
	* Create a custom backtraced array based on the debug_backtrace() output.
	*
	* @access public
	* @return array
	* @static
	*/
	public static function trace() {
		$backtrace = debug_backtrace();
		$response = array();

		if (!empty($backtrace)) {
			foreach ($backtrace as $trace) {
				if (!in_array($trace['function'], array('trace', '__output'))) {
					$current = array();
					$current['file'] = isset($trace['file']) ? $trace['file'] : '[Internal]';

					if (isset($trace['line'])) {
						$current['line'] = $trace['line'];
					}

					$method = $trace['function'];
					if (isset($trace['class'])) {
						$method = $trace['class'] . $trace['type'] . $method;
					}
					$current['method'] = $method;

					$args = array();
					if (!empty($trace['args'])) {
						foreach ($trace['args'] as $arg) {
							$args[] = self::parseArg($arg);
						}
					}

					$current['args'] = $args; //implode(', ', $args);

					$response[] = $current + array(
						'line'	=> null,
						'method'=> null,
						'file'	=> null,
						'args'	=> null
					);
				}
			}

			$response = array_reverse($response);
		}

		return $response;
	}

	/**
	* Renders a formatted error message to the view accompanied by a stack trace.
	*
	* @access public
	* @param string $error
	* @param string $message
	* @param string $file
	* @param int $line
	* @param mixed $context
	* @return string
	* @static
	*/
	private static function __output($number, $message, $file, $line, $context = null) {
		$append = count(self::$__errors);
		$backtrace = self::trace();

		$toggle = function($id, $table = false) {
			$display = ($table === true) ? 'table-row' : 'block';
			return "document.getElementById('". $id ."').style.display = (document.getElementById('". $id ."').style.display == 'none' ? '". $display ."' : 'none');";
		};

		$output  = '<div id="TitonDebugError_'. $append .'">';
		$output .= '<b><a href="#debug" onclick="'. $toggle('TitonStackTrace_'. $append) .' return false;">'. self::errorType($number) .'</a>:</b> '. $message .' on ';
		$output .= '<b><acronym title="'. $file .'">'. self::parseFile($file) .'</acronym></b> ('. $line .')<br><br>';

		if (!empty($backtrace)) {
			$output .= '<div id="TitonStackTrace_'. $append .'" style="display: none">';
			$output .= '<table cellpadding="0" cellspacing="0" style="border: none">';

			foreach ($backtrace as $stack => $trace) {
				$output .= '<tr><td>';

				if (!empty($trace['args'])) {
					$output .= '<a href="#debug" onclick="'. $toggle('TitonMethodArgs_'. $stack .'_'. $append, true) .' return false;">'. $trace['method'] .'</a>';
				} else {
					$output .= $trace['method'];
				}

				$output .= '() &nbsp;</td><td><i><acronym title="'. $file .'">'. self::parseFile($trace['file']) .'</acronym></i>';

				if (!empty($trace['line'])) {
					$output .= ' ('. $trace['line'] .')';
				}
				$output .= '</td></tr>';

				if (!empty($trace['args'])) {
					$output .= '<tr id="TitonMethodArgs_'. $stack .'_'. $append .'" style="display: none">';
					$output .= '<td colspan="2"><br><b>Arguments:</b><ol>';

					foreach ($trace['args'] as $arg) {
						$output .= '<li>'. $arg .'</li>';
					}

					$output .= '</ol></td></tr>';
				}
			}

			$output .= '</table>';
			$output .= '<br></div>';
		}

		$output .= '</div>';
		echo $output;
	}

}
