<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use titon\Titon;
use \Closure;

/**
 * Handles the autoloading, importing and including of files within the system.
 * Provides convenience functions for inflecting notation paths, namespace paths and file system paths.
 *
 * @package	titon.core
 * @uses	titon\Titon
 */
class Loader {

	/**
	 * Collection of loader detection closures.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_loaders = [];

	/**
	 * Define autoloader and attempt to autoload from include_paths first.
	 *
	 * @access public
	 */
	public function __construct() {
		spl_autoload_extensions('.php');
		spl_autoload_register();
		spl_autoload_register([$this, 'autoload']);

		// Add default loader
		$this->setup('default', function($class) {
			return Titon::loader()->import($class);
		});

		// Set default include paths
		$this->includePath([TITON_APP, TITON, TITON_LIBS, VENDORS]);
	}

	/**
	 * Cycle through all the defined loaders until the file is included.
	 *
	 * @access public
	 * @param string $class
	 * @return void
	 */
	public function autoload($class) {
		if (class_exists($class, false) || interface_exists($class, false)) {
			return;
		}

		foreach ($this->_loaders as $loader) {
			if ($loader($class)) {
				break;
			}
		}
	}

	/**
	 * Strips the namespace to return the base class name.
	 *
	 * @access public
	 * @param string $class
	 * @param string $separator
	 * @return string
	 */
	public function baseClass($class, $separator = '\\') {
		return $this->stripExt(trim(strrchr($class, $separator), $separator));
	}

	/**
	 * Returns a namespace with only the base package, and not the class name.
	 *
	 * @access public
	 * @param string $class
	 * @param string $separator
	 * @return string
	 */
	public function baseNamespace($class, $separator = '\\') {
		$class = $this->toNamespace($class);

		return substr($class, 0, strrpos($class, $separator));
	}

	/**
	 * Converts OS directory separators to the standard forward slash.
	 *
	 * @access public
	 * @param string $path
	 * @param boolean $endSlash
	 * @return string
	 */
	public function ds($path, $endSlash = false) {
		$path = str_replace('\\', '/', $path);

		if ($endSlash && substr($path, -1) !== '/') {
			$path .= '/';
		}

		return $path;
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
		foreach ([TITON_APP, TITON, VENDORS, TITON_LIBS] as $root) {
			$source = $this->toPath($path, 'php', $root);

			if (file_exists($source)) {
				include_once $source;

				return true;
			}
		}

		return false;
	}

	/**
	 * Define additional include paths for PHP to detect within.
	 *
	 * @access public
	 * @param string|array $paths
	 * @return titon\core\Loader
	 * @chainable
	 */
	public function includePath($paths) {
		$current = [get_include_path()];

		if (is_array($paths)) {
			foreach ($paths as $path) {
				$current[] = $path;
			}
		} else {
			$current[] = $paths;
		}

		set_include_path(implode(PATH_SEPARATOR, $current));

		return $this;
	}

	/**
	 * Delete an auto loader.
	 *
	 * @access public
	 * @param string $key
	 * @return titon\core\Loader
	 * @chainable
	 */
	public function remove($key) {
		unset($this->_loaders[$key]);

		return $this;
	}

	/**
	 * Primary method that deals with autoloading classes.
	 * Defines a closure that is triggered to attempt to include a file.
	 *
	 * @access public
	 * @param string $key
	 * @param Closure $loader
	 * @return titon\core\Loader
	 * @chainable
	 */
	public function setup($key, Closure $loader) {
		$this->_loaders[$key] = $loader;

		return $this;
	}

	/**
	 * Strip off the extension if it exists.
	 *
	 * @access public
	 * @param string $path
	 * @return string
	 */
	public function stripExt($path) {
		if (strpos($path, '.') !== false) {
			$path = substr($path, 0, strrpos($path, '.'));
		}

		return $path;
	}

	/**
	 * Converts a path to a namespace package.
	 *
	 * @access public
	 * @param string $path
	 * @return string
	 */
	public function toNamespace($path) {
		$path = $this->stripExt($path);

		if (strpos($path, '/') !== false) {
			$path = str_replace($this->ds(VENDORS), '', $this->ds($path));
			$path = str_replace('/', '\\', $path);
		}

		return trim($path, '\\');
	}

	/**
	 * Converts a namespace to a relative or absolute file system path.
	 *
	 * @access public
	 * @param string $path
	 * @param string $ext
	 * @param mixed $root
	 * @return string
	 */
	public function toPath($path, $ext = 'php', $root = false) {
		$path = $this->ds($path);
		$dirs = explode('/', $path);
		$file = array_pop($dirs);
		$path = implode('/', $dirs) . '/' . str_replace('_', '/', $file);

		if ($ext && substr($path, -strlen($ext)) !== $ext) {
			$path .= '.' . $ext;
		}

		if ($root) {
			$path = $root . $path;
		}

		return $path;
	}

}