<?php
/**
 * The App class contains all methods that manipulate and power the application, or add quick convenience.
 * It contains a statically defined architecture for the locations of specific modules and libraries.
 * The locate() and import() methods are used to include specific classes into the current scope, or find the correct namespace.
 *
 * All $_POST and superglobal data is stored within this object, and is supplied elsewhere by reference.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\core;

use \titon\core\Environment;
use \titon\log\Debugger;
use \titon\log\Exception;
use \titon\router\Router;

/**
 * Application Class
 *
 * @package		Titon
 * @subpackage	Titon.Core
 */
class App {

    /**
	 * An array of cleaned $_POST and $_FILES data for the current request.
	 *
	 * @access public
	 * @var array
     * @static
	 */
	public static $data = array();

	/**
	 * Super global arrays: GET, POST, FILES, SERVER, ENV.
	 *
	 * @access public
	 * @var array
     * @static
	 */
	public static $globals = array();

	/**
	 * Disable the class to enforce static methods.
	 *
	 * @access private
	 * @return void
	 */
	private function __construct() { }

    /**
     * Get the currently defined charset for the application.
     *
     * @access public
     * @return string
     */
    public static function charset() {
        return Config::get('App.encoding') ?: 'UTF-8';
    }
	
	/**
	 * Initialize all classes required for runtime. Master initialize method.
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public static function initialize() {

        // Initialize core components
		Environment::initialize();
		Debugger::initialize();
		Router::initialize();

        // Get super globals
        $get = $_GET;
        $post = $_POST;
        $files = array();

        if (!empty($_FILES)) {
            foreach ($_FILES as $model => $data) {
                foreach ($data as $meta => $values) {
                    $keys = array_keys($values);
                    $files[$model][$keys[0]][$meta] = $values[$keys[0]];
                }
            }
        }

        // Clear magic quotes, just in case
        if (get_magic_quotes_gpc() > 0) {
            $stripSlashes = function($data) {
                return (is_array($data) ? array_map($stripSlashes, $data) : stripslashes($data));
            };

            $get = $stripSlashes($get);
            $post = $stripSlashes($post);
            $files = $stripSlashes($files);
        }

        static::$data = array_merge_recursive($post, $files);
		static::$globals = array(
			'_GET' => $get,
			'_POST'	=> $post,
			'_FILES' => $files,
            '_SERVER' => $_SERVER,
            '_ENV' => $_ENV
		);
	}

	/**
	 * Get the currently used locale for the application.
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function locale() {
        return Config::get('Locale.current') ?: Config::get('Locale.default');
	}

}