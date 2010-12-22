<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

/**
 * Handles the autoloading, importing and including of files within the system.
 * Provides convenience functions for inflecting notation paths, namespace paths and file system paths.
 *
 * @package		Titon
 * @subpackage	Core
 */
class Loader {

	/**
	 * Files that have been included into the current scope through the use of import() or autoload().
	 *
	 * @access private
	 * @var array
	 */
	private $__imported = array();

	/**
	 * Define autoloader and attempt to autoload from include_paths first.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		spl_autoload_extensions('.php');
		spl_autoload_register();
		spl_autoload_register('\titon\source\core\Loader::autoload');
	}

	/**
	 * Primary method that deals with autoloading classes.
	 * Attemps to import file based on namespace or file path.
	 *
	 * @access public
	 * @param string $class
	 * @return void
	 */
	public function autoload($class) {
		if (class_exists($class, false) || interface_exists($class, false)) {
			return;
		}

		include_once $this->toPath($class, 'php', false);

		$this->__imported[] = $this->toNotation($class);
	}

	/**
	 * Strips the namespace to return the base class name.
	 *
	 * @access public
	 * @param string $class
	 * @param string $sep
	 * @return string
	 */
	public function baseClass($class, $sep = NS) {
		$class = trim(strrchr($class, $sep), $sep);

		// Remove ext for file paths
		if ($sep === DS) {
			$class = substr($class, 0, strrpos($class, '.'));
		}

		return $class;
	}

	/**
	 * Returns a namespace with only the base path, and not the class name.
	 *
	 * @access public
	 * @param string $class
	 * @param string $sep
	 * @return string
	 */
	public function baseNamespace($class, $sep = NS) {
		return $this->toNamespace(substr($class, 0, strrpos($class, $sep)));
	}

	/**
	 * Check to see if a specific file has been imported.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function check($key) {
		return in_array($this->toNotation($key), $this->__imported);
	}

	/**
	 * Converts OS directory separators to the standard forward slash.
	 *
	 * @access public
	 * @param string $path
	 * @return string
	 */
	public function ds($path) {
		return str_replace(array(':/', ':\\', '/', '\\'), DS, $path);
	}

	/**
	 * Attempts to include files into the application based on namespace or given path.
	 * Relies heavily on the defined include paths.
	 *
	 * @access public
	 * @param string $path
	 * @return boolean
	 */
	public function import($path) {
		$path = $this->toPath($path);
		$notation = $this->toNotation($path);

		if (isset($this->__imported[$notation])) {
			return true;

		} else if (file_exists($path)) {
			$this->__imported[] = $notation;

			include_once $path;
			return true;
		}

		return false;
	}

	/**
	 * Define additional include paths for PHP to detect within.
	 *
	 * @access public
	 * @param string|array $paths
	 * @return void
	 */
	public function includePath($paths) {
		$current = array(get_include_path());

		if (is_array($paths)) {
			$current = $current + $paths;
		} else {
			$current[] = $paths;
		}
		
		set_include_path(implode(PS, $current));
	}

	/**
	 * Converts a path to a namespace path.
	 *
	 * @access public
	 * @param string $path
	 * @return string
	 */
	public function toNamespace($path) {
		$path = $this->ds($path);
		$hasDS = (strpos($path, DS) !== false);
		$hasDot = (strpos($path, '.') !== false);

		// From a notation
		if ($hasDot && !$hasDS) {
			$namespace = str_replace('.', NS, $path);

		// From a filepath or namespace
		} else {
			if ($hasDot) {
				$path = substr($path, 0, strrpos($path, '.'));
			}

			$namespace = str_replace(DS, NS, $path);
		}

		if (substr($namespace, 0, 1) != NS) {
			$namespace = NS . $namespace;
		}

		return $namespace;
	}

	/**
	 * Converts a path to a dot notated path.
	 *
	 * @access public
	 * @param string $path
	 * @return string
	 */
	public function toNotation($path) {
		if (strpos($path, NS) === false) {
			$path = $this->toNamespace($path);
		}

		return str_replace(NS, '.', trim($path, NS));
	}

	/**
	 * Converts a path to an absolute file system path.
	 *
	 * @access public
	 * @param string $path
	 * @param string $ext
	 * @param mixed $root
	 * @return string
	 */
	public function toPath($path, $ext = 'php', $root = ROOT) {
		if (strpos($path, NS) === false) {
			$path = $this->toNamespace($path);
		}

		$path = trim($path, NS);
		$dirs = explode(NS, $path);
		$file = array_pop($dirs);
		$path = implode(DS, $dirs) . DS . str_replace('_', DS, $file);

		if ($ext) {
			$path .= '.'. $ext;
		}

		if ($root) {
			$path = $root . $path;
		}

		return $path;
	}

}