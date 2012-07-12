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
 * This custom route is used when G11n is enabled and the application wants to support localization.
 * It will prepend all routes with a locale pattern (if it does not exist), and will redirect
 * a URL to a locale-friendly URL if the currently parsed route is invalid.
 *
 * @package	titon.libs.routes.g11n
 */
class LocaleRoute extends RouteAbstract {

	/**
	 * Locale pattern: en-us.
	 */
	const LOCALE = '([a-z]{2}(?:-[a-z]{2})?)';

	/**
	 * Store the routing configuration and prepend the path with the locale match.
	 *
	 * @access public
	 * @param string $path
	 * @param array $route
	 * @param array $config
	 */
	public function __construct($path, array $route = [], array $config = []) {
		if (substr($path, 0, 9) !== '/<locale>') {
			$path = '/<locale>/' . ltrim($path, '/');
		}

		if (isset($config['patterns'])) {
			$config['patterns']['locale'] = self::LOCALE;
		} else {
			$config['patterns'] = ['locale' => self::LOCALE];
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

		// Redirect to the fallback URL
		if (PHP_SAPI !== 'cli' && empty($this->_route['locale'])) {
			$redirect = $this->_route;
			$redirect['locale'] = Titon::g11n()->getFallback()->getLocale('key');

			$this->response->redirect($redirect);
		}

		return $status;
	}

}
