<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use \titon\Titon;
use \titon\log\Logger;
use \Exception;

/**
 * Custom system to manage all internal and user created errors and thrown/uncaught exceptions.
 * Errors are displayed with a custom backtrace as well as logged to the filesystem (if passed to the Logger).
 *
 * @package titon.log
 * @uses	titon\Titon
 * @uses	titon\log\Logger
 */
class Debugger {

	/**
	 * Complete list of all internal errors types.
	 *
	 * @access public
	 * @var array
	 */
	public $errorTypes = array(
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
		E_RECOVERABLE_ERROR	=> 'Catchable Fatal Error',
		E_DEPRECATED		=> 'Deprecated',
		E_USER_DEPRECATED	=> 'User Deprecated',
		E_ALL				=> 'All'
	);

	/**
	 * Errors received during the current request.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_errors = array();
	
	/**
	 * The last uncaught exception.
	 * 
	 * @access protected
	 * @var Exception
	 */
	protected $_exception;

	/**
	 * Initialize the error/exception/debug handling.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		ini_set('log_errors', true);
		ini_set('report_memleaks', true);
		ini_set('error_log', APP_TEMP . Logger::ERROR_LOG);

		set_error_handler(array($this, 'error'), E_ALL | E_STRICT);
		set_exception_handler(array($this, 'uncaught'));
		
		$this->enable();
	}

	/**
	 * Enable or disable error reporting dynamically during runtime.
	 *
	 * @access public
	 * @param boolean $enabled
	 * @return void
	 */
	public function enable($enabled = true) {
		$enabled = (bool) $enabled;

		if ($enabled) {
			ini_set('error_reporting', E_ALL | E_STRICT);
		} else {
			ini_set('error_reporting', 0);
		}

		ini_set('display_errors', $enabled);
		ini_set('display_startup_errors', $enabled);
		ini_set('track_errors', $enabled);
	}

	/**
	 * Overwrite the error_handler. When in development output errors, in production save to logs.
	 *
	 * @access public
	 * @param int $number
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 * @param string $context
	 * @return void
	 */
	public function error($number, $message, $file = null, $line = null, $context = null) {
		$this->_errors[] = compact($number, $message, $file, $line);

		if (error_reporting() > 0) {
			$this->output($number, $message, $file, $line, $context);
		} else {
			Logger::write(sprintf('[%s] %s: %s in %s on line %s.', date('d-M-Y H:i:s'), $this->errorType($number), $message, $file, $line));
		}
	}

	/**
	 * Determine the type of error received.
	 *
	 * @access public
	 * @param int $code
	 * @return string
	 */
	public function errorType($code = null) {
		if (isset($this->errorTypes[$code])) {
			return $this->errorTypes[$code];
		}
		
		if ($this->_exception instanceof Exception) {
			return Titon::loader()->baseClass(get_class($this->_exception));
		}
		
		return 'Unknown Error';
	}

	/**
	 * Export a formatted variable to be used.
	 *
	 * @access public
	 * @param mixed $var
	 * @return mixed
	 */
	public function export($var = null) {
		return var_export($var, true);
	}

	/**
	 * Renders a formatted error message to the view accompanied by a stack trace.
	 *
	 * @access public
	 * @param string $number
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 * @param mixed $context
	 * @return string
	 */
	public function output($number, $message, $file, $line, $context = null) {
		$append = count($this->_errors);
		$backtrace = $this->trace();

		$toggle = function($id, $table = false) {
			$display = ($table) ? 'table-row' : 'block';
			return "document.getElementById('". $id ."').style.display = (document.getElementById('". $id ."').style.display == 'none' ? '". $display ."' : 'none');";
		};

		$output  = '<div id="TitonDebugError-'. $append .'">';
		$output .= '<b><a href="#debug" onclick="'. $toggle('TitonStackTrace-'. $append) .' return false;">'. $this->errorType($number) .'</a>:</b> '. $message .' ';
		$output .= '<b><acronym title="'. $file .'">'. $this->parseFile($file) .'</acronym></b> ('. $line .')<br><br>';

		if (!empty($backtrace)) {
			$output .= '<div id="TitonStackTrace-'. $append .'" style="display: none">';
			$output .= '<table cellpadding="0" cellspacing="0" style="border: none">';

			foreach ($backtrace as $stack => $trace) {
				$output .= '<tr><td>';

				if (!empty($trace['args'])) {
					$output .= '<a href="#debug" onclick="'. $toggle('TitonMethodArgs-'. $stack .'-'. $append, true) .' return false;">'. $trace['method'] .'</a>';
				} else {
					$output .= $trace['method'];
				}

				$output .= '() &nbsp;</td><td><i><acronym title="'. $trace['file'] .'">'. $this->parseFile($trace['file']) .'</acronym></i>';

				if (!empty($trace['line'])) {
					$output .= ' ('. $trace['line'] .')';
				}
				
				$output .= '</td></tr>';

				if (!empty($trace['args'])) {
					$output .= '<tr id="TitonMethodArgs-'. $stack .'-'. $append .'" style="display: none">';
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

	/**
	 * Parse the backtrace's arguments array and format correctly for a return.
	 *
	 * @access public
	 * @param mixed $arg
	 * @param boolean $end
	 * @return mixed
	 */
	public function parseArg($arg, $end = false) {
		if (is_numeric($arg)) {
			return (int) $arg;

		} else if (is_bool($arg)) {
			return ($arg === true) ? 'true' : 'false';

		} else if (is_string($arg)) {
			return "'" . htmlentities($arg) . "'";

		} else if (is_array($arg)) {
			if ($end === true) {
				return 'array([Truncated])';
			} else {
				$args = array();

				foreach ($arg as $a) {
					$args[] = $this->parseArg($a, true);
				}

				return 'array('. implode(', ', $args) .')';
			}

		} else if (is_null($arg)) {
			return 'null';

		} else if (is_object($arg)) {
			return get_class($arg);

		} else if (is_resource($arg)) {
			return strtolower(get_resource_type($arg));

		} else {
			return (string) $arg;
		}
	}

	/**
	 * Parse the file path to remove absolute path and replace with constant name.
	 *
	 * @access public
	 * @param string $path
	 * @return string
	 */
	public function parseFile($path) {
		if (empty($path)) {
			return '[Internal]';
		}

		$path = Titon::loader()->ds($path);

		foreach(array('app', 'library', 'vendors', 'titon', 'root') as $constant) {
			$location = Titon::loader()->ds(constant(strtoupper($constant)));

			if (strpos($path, $location) !== false) {
				$path = str_replace($location, '['. $constant .']', $path);
				break;
			}
		}

		return $path;
	}

	/**
	 * Create a custom backtraced array based on the debug_backtrace() output.
	 *
	 * @access public
	 * @return array
	 */
	public function trace() {
		$backtrace = debug_backtrace();
		$response = array();

		if (!empty($backtrace)) {
			foreach ($backtrace as $trace) {
				//if (!in_array($trace['function'], get_class_methods($this))) {
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
							$args[] = $this->parseArg($arg);
						}
					}

					$current['args'] = $args;

					$response[] = $current + array(
						'line'	=> null,
						'method'=> null,
						'file'	=> null,
						'args'	=> null
					);
				//}
			}

			$response = array_reverse($response);
		}

		return $response;
	}

	/**
	 * Handler for catching uncaught exceptions.
	 *
	 * @access public
	 * @param Exception $exception
	 * @return void
	 */
	public function uncaught(Exception $exception) {
		$this->_exception = $exception;
		
		$trace = $exception->getTrace();
		$method = '';

		if (isset($trace[0]['class'])) {
			$method = $trace[0]['class'] . $trace[0]['type'] . $trace[0]['function'] .'()';
			
		} else if (strpos($trace[0]['function'], 'closure') !== false) {
			$method = get_class($trace[0]['args'][0]) . '[Closure]';
		}
		
		$response = $method .': '. $exception->getMessage();
		$code = $exception->getCode();

		if ($code) {
			$response .= ' (Code: '. $code .')';
		}

		$this->error($code, $response, $exception->getFile(), $exception->getLine());
	}

}
