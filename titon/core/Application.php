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
	 * Files that have been included into the current scope through the use of import().
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__imported = array();

	/**
	 * Disable the class to enforce static methods.
	 *
	 * @access private
	 * @return void
	 */
	private function __construct() { }
	
	/**
	 * Method that deals with autoloading classes. Attemps to import file based on namespace.
	 *
	 * @access public
	 * @param string $class
	 * @return void
	 * @static
	 */
	public static function autoload($class) {
		if (class_exists($class, false) || interface_exists($class, false)) {
            return;
        }

		static::$__imported[] = static::toDotNotation($class);
        
        include_once static::toPath($class, 'php', false);
	}

    /**
	 * Strips the base namespace to return the base class name.
	 * Example: \namespace\to\MyClass = MyClass
	 *
	 * @access public
	 * @param string $namespace
	 * @param string $sep
	 * @return string
	 * @static
	 */
	public static function baseClass($namespace, $sep = NS) {
		return trim(strrchr($namespace, $sep), $sep);
	}

    /**
	 * Returns a namespace with only the base path, and not the class name.
	 *
	 * @access public
	 * @param string $namespace
	 * @param string $sep
	 * @return string
	 */
	public static function baseNamespace($namespace, $sep = NS) {
		return substr($namespace, 0, strrpos($namespace, $sep));
	}

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
        // Try and autoload from include_paths first
		spl_autoload_register();
		spl_autoload_register('\titon\core\App::autoload');

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
	 * Includes files into the application. Attemps to include a file based on namespace or given path.
     * Relies heavily on the defined include paths.
	 *
	 * @access public
	 * @param string $slug
	 * @return void
	 * @static
	 */
	public static function import($slug) {
        $path = static::toPath($slug);
        $namespace = static::toNamespace($path);

		include_once $path;
		
		static::$__imported[] = static::toDotNotation($namespace);

		return $namespace;
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

    /**
     * Converts a namespace into a dot notated path.
     * Example: \namespace\to\MyClass = namespace.to.MyClass
     *
     * @access public
     * @param string $namespace
     * @return string
     * @static
     */
    public static function toDotNotation($namespace) {
        return trim(str_replace(NS, '.', $namespace), NS);
    }

	/**
	 * Converts a path or dot notation path to a namespace. Does not append file extension.
	 * Example: /root/path/to/file.php = \path\to\File
     * Example: path.to.File = \path\to\File
	 *
	 * @access public
	 * @param string $path
	 * @return string
	 * @static
	 */
	public static function toNamespace($path) {
        if (strpos($path, DS) === false) {
            $namespace = str_replace('.', NS, $path);
        } else {
            $path = substr($path, 0, strrpos($path, '.'));
            $namespace = str_replace(DS, NS, str_replace(ROOT, '', $path));
        }

        if (substr($namespace, 0, 1) != NS) {
            $namespace = NS. $namespace;
        }

        return $namespace;
	}

	/**
	 * Converts a namespace to an absolute path. Does not append file extension.
	 * Example: \path\to\MyClass = /root/path/to/MyClass.php
	 *
	 * @access public
	 * @param string $namespace
	 * @param string $ext
     * @param boolean $root - Should we append the root path?
	 * @return string
	 * @static
	 */
	public static function toPath($namespace, $ext = 'php', $root = ROOT) {
        if (strpos($namespace, NS) === false) {
            $namespace = static::toNamespace($namespace);
        }

		$namespace = trim($namespace, NS);
		$paths = explode(NS, $namespace);
		$class = array_pop($paths);
		$path  = implode(DS, $paths) . DS . str_replace('_', DS, $class);

		if ($ext) {
			$path .= '.'. $ext;
		}

        if ($root) {
            $path = $root . $path;
        }

        return $path;
	}

}