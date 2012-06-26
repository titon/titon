<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\log;

use titon\Titon;
use titon\log\Logger;

/**
 * Delivers the functionality to start, stop and log benchmarks.
 * Benchmarks store the time difference and memory usage between two blocks during runtime.
 *
 * @package	titon.log
 * @uses	titon\Titon
 * @uses	titon\log\Logger
 */
class Benchmark {

	/**
	 * User and system initiated benchmarking tests.
	 *
	 * @access protected
	 * @var array
	 * @static
	 */
	protected static $_benchmarks = [];

	/**
	 * Disable the class to enforce static methods.
	 *
	 * @access private
	 */
	private function __construct() { }

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
			$benchmarks = self::$_benchmarks;

		} else if (isset(self::$_benchmarks[$key])) {
			$benchmarks = [self::$_benchmarks[$key]];
		}

		if (isset($benchmarks)) {
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
	 * Outputs and formats a benchmark directly as a string.
	 *
	 * @access public
	 * @param string $key
	 * @return string
	 * @static
	 */
	public static function output($key = null) {
		$benchmark = self::get($key);

		if (!empty($benchmark)) {
			$result  = 'Benchmark [' . $key . '] - ';
			$result .= 'Time: ' . number_format($benchmark['avgTime'], 4) . ' - ';
			$result .= 'Memory: ' . $benchmark['avgMemory'] . ' (Max: ' . $benchmark['peakMemory'] . ')';

			return $result;
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
		if (error_reporting() > 0) {
			self::$_benchmarks[$key] = [
				'startTime'		=> microtime(true),
				'startMemory'	=> memory_get_usage(true),
			];
		}
	}

	/**
	 * Stop the benchmarking process by logging the micro seconds and memory usage and then outputting the results.
	 *
	 * @access public
	 * @param string $key
	 * @param boolean $log
	 * @return mixed
	 * @static
	 */
	public static function stop($key = 'benchmark', $log = false) {
		if (error_reporting() > 0) {
			if (empty(self::$_benchmarks[$key])) {
				return false;
			}

			self::$_benchmarks[$key] = [
				'endTime'	=> microtime(true),
				'endMemory'	=> memory_get_usage(true)
			] + self::$_benchmarks[$key];

			if ($log) {
				Logger::debug(self::output($key));
			}

			return self::$_benchmarks[$key];
		}

		return false;
	}

}
