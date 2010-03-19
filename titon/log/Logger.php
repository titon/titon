<?php
/**
 * A simple class that handles the logging of errors and debug messages to the filesystem.
 * Logs are categorized based on threat level, which determines the location of where it is to be written.
 * There are two files in which the logger uses: debug.log and error.log, both of which are located in the app/temp.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\log;

use \titon\core\Config;
use \titon\log\Debugger;

/**
 * Logger Class
 *
 * @package		Titon
 * @subpackage	Titon.Log
 */
class Logger {

    /**
	 * Name of the error log.
	 *
	 * @var string
	 */
	const ERROR_LOG = 'error.log';

	/**
	 * Name of the dump/debug log.
	 *
	 * @var string
	 */
	const DEBUG_LOG = 'debug.log';

    /**
     * Level for critical items.
     *
     * @var string
     */
    const CRITICAL = 1;
    
    /**
     * Level for alerted items.
     * 
     * @var string
     */
    const ALERT = 2;

    /**
     * Level for warned items.
     *
     * @var string
     */
    const WARNING = 3;

    /**
     * Level for notice messages.
     *
     * @var string
     */
    const NOTICE = 4;

    /**
     * Level for informational messages.
     *
     * @var string
     */
    const INFO = 5;

    /**
     * Level for debug items.
     *
     * @var string
     */
    const DEBUG = 6;

    /**
     * Wrapper function to log alerted errors.
     *
     * @access public
     * @param string $message
     * @return void
     * @static
     */
    public static function alert($message) {
        static::write('['. date('d-M-Y H:i:s') .'] '. $message, static::ALERT);
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
        static::write('['. date('d-M-Y H:i:s') .'] '. $message, static::CRITICAL);
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
        static::write('['. date('d-M-Y H:i:s') .'] '. $message, static::DEBUG);
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
        static::write('['. date('d-M-Y H:i:s') .'] '. $message, static::INFO);
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
        static::write('['. date('d-M-Y H:i:s') .'] '. $message, static::NOTICE);
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
        static::write('['. date('d-M-Y H:i:s') .'] '. $message, static::WARNING);
    }

    /**
	 * Writes a message to the error or debug log, depending on the threat level.
	 * Additionally, it will send you an email with the error message if Debug.email is defined.
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
                case static::CRITICAL:$type = 'Critical'; break;
                case static::ALERT:   $type = 'Alert'; break;
                case static::WARNING: $type = 'Warning'; break;
                case static::NOTICE:  $type = 'Notice'; break;
                case static::INFO:    $type = 'Info'; break;
                case static::DEBUG:   $type = 'Debug'; break;
                default:            $type = 'Internal'; break;
            }

            if ($level == static::DEBUG) {
                $file = static::DEBUG_LOG;
            } else {
                $file = static::ERROR_LOG;
                $message = '['. $type .'] '. $message;
            }

			$log = fopen(TEMP . $file, 'ab');
			fwrite($log, $message ."\n");
			fclose($log);

            if ($level >= static::WARNING) {
                if ($email = Config::get('Debug.email')) {
                    mail($email, '[Titon Error] '. $type, $message);
                }
            }
		}
	}

}