<?php
/**
 * A bootstrap that loads convenience functions for the user.
 * The functions reference core libraries for easier usage.
 *
 * @copyright		Copyright 2009, Titon (A PHP Micro Framework)
 * @link			http://titonphp.com
 * @license			http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

/**
 * Fallback method if the app autoloader isn't called.
 *
 * @param string $class
 * @return void
 */
function __autoload($class) {
    \titon\core\App::autoload($class);
}

/**
 * Outputs/Debugs multiple variables and shows where it was called from.
 * 
 * @param mixed $var, $var, $var...
 * @return array|string
 */
function debug() {
	if (\titon\core\Config::get('debug') > 0) {
		$vars = func_get_args();
		$calledFrom = debug_backtrace();

        echo '<div class="TitonDebug">';
		echo '<b>' . trim(str_replace(ROOT, '', $calledFrom[0]['file'])) . '</b> (' . $calledFrom[0]['line'] . ')';

		if (!empty($vars)) {
			foreach ($vars as $var) {
				echo '<pre>'. print_r($var, true) .'</pre>';
			}
		}
        
        echo '</div>';
	}
}

/**
 * Works exactly like debug() except uses var_dump() in place of print_r().
 *
 * @param mixed $var, $var, $var...
 * @return array|string
 */
function dump() {
	if (\titon\core\Config::get('debug') > 0) {
		$vars = func_get_args();
		$calledFrom = debug_backtrace();

        echo '<div class="TitonDebug">';
		echo '<b>' . trim(str_replace(ROOT, '', $calledFrom[0]['file'])) . '</b> (' . $calledFrom[0]['line'] . ')';

		if (!empty($vars)) {
			foreach ($vars as $var) {
				echo '<pre>';
				var_dump($var);
				echo '</pre>';
			}
		}

        echo '</div>';
	}
}

/**
 * Convenience function for accessing the environment.
 *
 * @param string|array $var
 * @return string|array
 */
function env($var) {
	if ($value = getenv($key)) {
		return $value;
	} else if (isset($_SERVER[$key])) {
		return $_SERVER[$key];
	} else if (isset($_ENV[$key])) {
		return $_ENV[$key];
	}

	return null;
}
