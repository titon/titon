<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

/**
 * Fallback method if the app autoloader isn't called.
 *
 * @param string $class
 * @return void
 */
function __autoload($class) {
	$app->loader->autoload($class);
}

/**
 * Outputs/Debugs multiple variables and shows where it was called from.
 * 
 * @param mixed $var, $var, $var...
 * @return array|string
 */
function debug() {
	if (error_reporting() > 0) {
		$vars = func_get_args();
		$calledFrom = debug_backtrace();

		echo '<div class="TitonDebug">';
		echo '<b>' . trim(str_replace(APP, '', $calledFrom[0]['file'])) . '</b> (' . $calledFrom[0]['line'] . ')';

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
	if (error_reporting() > 0) {
		$vars = func_get_args();
		$calledFrom = debug_backtrace();

		echo '<div class="TitonDebug">';
		echo '<b>' . trim(str_replace(APP, '', $calledFrom[0]['file'])) . '</b> (' . $calledFrom[0]['line'] . ')';

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
