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
 * The Memoizer provides functionality to speed up processing time by having method calls avoid repeat
 * executions by caching the results. This process is widely known as Memoization.
 *
 * @package	titon.libs.traits
 */
trait Memoizer {
	
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
	 * @param string $method
	 * @param mixed $id
	 * @param \Closure $callback
	 * @return mixed 
	 */
	public function cacheMethod($method, $id, Closure $callback) {
		if ($id) {
			$method .= ':' . $id;
		}
		
		if (isset($this->_methodCaches[$method])) {
			return $this->_methodCaches[$method];
		}
		
		$this->_methodCaches[$method] = $callback($this);
		
		return $this->_methodCaches[$method];
	}
	
}