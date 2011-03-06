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
use \titon\source\log\Logger;

/**
 * Delivers the functionality to start, stop and log benchmarks.
 * Benchmarks store the time difference and memory usage between two blocks during runtime.
 *
 * @package	titon.source.log
 * @uses	titon\source\Titon
 * @uses	titon\source\log\Logger
 */
class Benchmark {

	/**
	 * User and system initiated benchmarking tests.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__benchmarks = array();

	/**
	 * Disable the class to enforce static methods.
	 *
	 * @access private
	 * @return void
	 */
	private function __construct() { }

	/**
	 * Outputs and formats a benchmark directly as a string.
	 *
	 * @access public
	 * @param string $key
	 * @return string
	 * @static
	 */
	public static function display($key = null) {
		if (empty(self::$__benchmarks[$key])) {
			return false;
		}

		$benchmark = self::get($key);
		$result  = 'Benchmark ['. $key .'] - ';
		$result .= 'Time: '. number_format($benchmark['avgTime'], 4) .' - ';
		$result .= 'Memory: '. $benchmark['avgMemory'] .' (Max: '. $benchmark['peakMemory'] .')';

		return $result;
	}

	/**
	 * Grab a list of all benchmarks or a single benchmark and return an array.
	 * Will calculate the averages of the time and memory if $calculate is true.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 * @static
	 */
	public static function get($key = null) {
		if (empty($key)) {
			$benchmarks = self::$__benchmarks;
			
		} else if (isset(self::$__benchmarks[$key])) {
			$benchmarks = array(self::$__benchmarks[$key]);
		}

		if (!empty($benchmarks)) {
			$peakMemory = memory_get_peak_usage();

			foreach ($benchmarks as &$bm) {
				$bm['avgTime'] = isset($bm['endTime']) ? ($bm['endTime'] - $bm['startTime']) : null;
				$bm['avgMemory'] = isset($bm['endMemory']) ? ($bm['endMemory'] - $bm['startMemory']) : null;
				$bm['peakMemory'] = $peakMemory;
			}

			return ($key) ? $benchmarks[0] : $benchmarks;
		}

		return null;
	}

	/**
	 * Start the benchmarking process by logging the micro seconds and memory usage.
	 *
	 * @access public
	 * @param string $key
	 * @return void
	 * @static
	 */
	public static function start($key = 'benchmark') {
		if (Titon::config()->get('debug.level') > 0) {
			self::$__benchmarks[$key] = array(
				'startTime'		=> microtime(true),
				'startMemory'	=> memory_get_usage(true),
			);
		}
	}

	/**
	 * Stop the benchmarking process by logging the micro seconds and memory usage and then outputting the results.
	 *
	 * @access public
	 * @param string $key
	 * @param bool $log
	 * @return string|mixed
	 * @static
	 */
	public static function stop($key = 'benchmark', $log = false) {
		if (Titon::config()->get('debug.level') > 0) {
			if (empty(self::$__benchmarks[$key])) {
				return false;
			}

			self::$__benchmarks[$key] = array(
				'endTime'	=> microtime(true),
				'endMemory'	=> memory_get_usage(true)
			) + self::$__benchmarks[$key];

			if ($log) {
				Logger::debug(self::display($key));
			}

			return self::$__benchmarks[$key];
		}
	}

}
