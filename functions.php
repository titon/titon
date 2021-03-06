<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon;

/**
 * Outputs multiple variables in an easily readable format.
 */
function debug() {
	if (error_reporting() > 0) {
		$vars = func_get_args();
		$calledFrom = debug_backtrace();

		echo '<div class="titon-debug">';
		echo '<b>' . trim(str_replace(TITON_APP, '', $calledFrom[0]['file'])) . '</b> (' . $calledFrom[0]['line'] . ')';

		if ($vars) {
			foreach ($vars as $var) {
				echo '<pre>' . print_r($var, true) . '</pre>' . PHP_EOL;
			}
		}

		echo '</div>';
	}
}

/**
 * Works exactly like debug() except uses var_dump() in place of print_r().
 */
function dump() {
	if (error_reporting() > 0) {
		$vars = func_get_args();
		$calledFrom = debug_backtrace();

		echo '<div class="titon-debug">';
		echo '<b>' . trim(str_replace(TITON_APP, '', $calledFrom[0]['file'])) . '</b> (' . $calledFrom[0]['line'] . ')';

		if ($vars) {
			foreach ($vars as $var) {
				echo '<pre>';
				var_dump($var);
				echo '</pre>' . PHP_EOL;
			}
		}

		echo '</div>';
	}
}

/**
 * Convenience function for fetching a localized string.
 *
 * @param string $key
 * @param array $params
 * @return string
 */
function msg($key, array $params = []) {
	return \titon\Titon::g11n()->translate($key, $params);
}