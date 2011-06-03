<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\library\routes;

/**
 * Interface for all Routers.
 *
 * @package	titon.source.library.routes
 */
interface RouteInterface {

	/**
	 * Compile the given path into a detectable regex pattern.
	 *
	 * @access public
	 * @return string
	 */
	public function compile();

	/**
	 * Attempt to match the class against a passed URL.
	 * If a match is found, extract pattern values and parameters.
	 *
	 * @acccess public
	 * @param string $url
	 * @return bool
	 */
	public function match($url);

}