<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\traits;

use \Closure;

/**
 * Memoizeable provides functionality to speed up processing time by having method calls avoid repeat
 * executions by caching the results. This process is widely known as Memoization.
 *
 * @package	titon.libs.traits
 */
trait Memoizeable {

	/**
	 * Store the methods return value after evaluating.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_methodCaches = array();

	/**
	 * Execute the method and cache the result when executed. If the data has already been cached, return that instead.
	 *
	 * @access public
	 * @param string|array $key
	 * @param Closure $callback
	 * @return mixed
	 */
	public function cacheMethod($key, Closure $callback) {
		if (method_exists(__CLASS__, 'createCacheKey')) {
			$key = self::createCacheKey($key);

		} else if (is_array($key)) {
			$key = implode('-', $key);
		}

		if (isset($this->_methodCaches[$key])) {
			return $this->_methodCaches[$key];
		}

		$callback = Closure::bind($callback, $this, __CLASS__);

		$this->_methodCaches[$key] = $callback();

		return $this->_methodCaches[$key];
	}

}