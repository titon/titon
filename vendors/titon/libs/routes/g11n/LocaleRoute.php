<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\routes\g11n;

use titon\Titon;
use titon\libs\routes\RouteAbstract;

/**
 * @todo
 *
 * @package	titon.libs.routes.g11n
 */
class LocaleRoute extends RouteAbstract {

	/**
	 * Store the routing configuration and prepend the path with the locale match.
	 *
	 * @access public
	 * @param string $path
	 * @param array $route
	 * @param array $config
	 */
	public function __construct($path, array $route = [], array $config = []) {
		if (substr($path, -9) !== '/<locale>') {
			$path = '/<locale>' . $path;
		}

		$pattern = '([a-z]{2}(?:-[a-z]{2})?)';

		if (isset($config['patterns'])) {
			$config['patterns']['locale'] = $pattern;
		} else {
			$config['patterns'] = ['locale' => $pattern];
		}

		parent::__construct($path, $route, $config);
	}

	/**
	 * Attempt to match the object against a passed URL.
	 * If a match is found, extract pattern values and parameters.
	 * If the locale has not been set, redirect to the same URL with the locale appended.
	 *
	 * @access public
	 * @param string $url
	 * @return boolean
	 */
	public function isMatch($url) {
		$status = parent::isMatch($url);

		if (!isset($this->_route['locale']) || empty($this->_route['locale'])) {
			$redirect = $this->_route;
			$redirect['locale'] = Titon::g11n()->getFallback()->config('key');

			Titon::registry()->factory('titon\net\Response')->redirect($redirect);
		}

		return $status;
	}

}
