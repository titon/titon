<?php
/**
 * Delivers the functionality to start, stop and log benchmarks.
 * Benchmarks store the time difference and memory usage between two blocks during runtime.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\log;

use \titon\core\Config;
use \titon\log\Logger;

/**
 * Benchmark Class
 *
 * @package		Titon
 * @subpackage	Titon.Log
 */
class Benchmark {

    /**
     * Log the benchmark. Argument setting for stop().
     *
     * @var boolean
     */
    const DO_LOG = true;

    /**
     * Do not log the benchmark. Argument setting for stop().
     *
     * @var boolean
     */
    const DONT_LOG = false;

    /**
	 * User and system initiated benchmarking tests.
	 *
	 * @access private
	 * @var array
     * @static
	 */
	private static $__benchmarks = array();

    /**
     * Outputs and formats a benchmark directly as a string.
     *
     * @access public
     * @param string $slug
     * @return string
     * @static
     */
    public static function display($slug = null) {
        if (empty(static::$__benchmarks[$slug])) {
            return false;
        }

        $benchmark = static::$__benchmarks[$slug];
        $time = ($benchmark['endTime'] - $benchmark['startTime']);
        $memory = ($benchmark['endMemory'] - $benchmark['startMemory']);

        $result  = 'Benchmark ['. $slug .']: ';
        $result .= 'Time: '. number_format($time, 4) .' / ';
        $result .= 'Memory: '. $memory .' (Max: '. memory_get_peak_usage() .')';

        return $result;
    }

    /**
	 * Grab a list of all benchmarks or a single benchmark and return an array.
     * Will calculate the averages of the time and memory if $calculate is true.
	 *
	 * @access public
	 * @param string $slug
	 * @return array
	 * @static
	 */
	public static function get($slug = null) {
		if (empty($slug)) {
			$benchmarks = static::$__benchmarks;
		} else if (isset(static::$__benchmarks[$slug])) {
			$benchmarks = array(static::$__benchmarks[$slug]);
		}

		if (!empty($benchmarks)) {
            $peakMemory = memory_get_peak_usage();

            foreach ($benchmarks as &$bm) {
                $bm['avgTime'] = (isset($bm['endTime']) ? ($bm['endTime'] - $bm['startTime']) : null);
                $bm['avgMemory'] = (isset($bm['endMemory']) ? ($bm['endMemory'] - $bm['startMemory']) : null);
                $bm['peakMemory'] = $peakMemory;
            }

			return ($slug) ? $benchmarks[0] : $benchmarks;
		}

		return null;
	}

    /**
	 * Start the benchmarking process by logging the micro seconds and memory usage.
	 *
	 * @access public
	 * @param string $slug
	 * @return void
	 * @static
	 */
	public static function start($slug = 'benchmark') {
		if (Config::get('debug') > 0) {
			static::$__benchmarks[$slug] = array(
				'startTime'		=> microtime(true),
				'startMemory'	=> memory_get_usage(true),
			);
		}
	}

	/**
	 * Stop the benchmarking process by logging the micro seconds and memory usage and then outputting the results.
	 *
	 * @access public
	 * @param string $slug
     * @param boolean $log
	 * @return string|mixed
	 * @static
	 */
	public static function stop($slug = 'benchmark', $log = self::DONT_LOG) {
		if (Config::get('debug') > 0) {
			if (empty(static::$__benchmarks[$slug])) {
				return false;
			}

			static::$__benchmarks[$slug] = array(
				'endTime'	=> microtime(true),
				'endMemory'	=> memory_get_usage(true)
			) + static::$__benchmarks[$slug];

            if ($log === static::DO_LOG) {
                Logger::debug(static::display($slug));
            }

			return static::$__benchmarks[$slug];
		}
	}

}
