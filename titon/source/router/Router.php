<?php
/**
 * The Router determines the current routing request, based on the URL address and environment.
 * Stores the current route, its parsed segments, the base URL and more.
 * Additionally, it will construct a URL based on an array of options, or deconstruct a URL into an array of options.
 * Lastly, you can define custom slugs or routes to be used for internal routing mechanisms.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\source\router;

/**
 * Routing Class
 *
 * @package		Titon
 * @subpackage	Titon.Router
 */
class Router {

	/**
	 * The current route broken up into its parts.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__current = array();

	/**
	 * An array of all paths that have been analyzed. Used for fast lookups.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__mapped = array();

	/**
	 * Manually defined aesthetic routes that re-route internally.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__routes = array();

	/**
	 * The current URL broken up into multiple segments: protocol, host, route, query, base
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__segments = array();

	/**
	 * Manually defined slugs that re-route to an internal controller and action.
	 * Primarily used to permanently define a route (array format) destination as a re-useable string.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__slugs = array();

	/**
	 * Disable the class to enforce static methods.
	 *
	 * @access private
	 * @return void
	 */
	private function __construct() { }

	/**
	 * Analyze a string (a route found in the address bar) into an array that maps to the correct module, controller and action.
	 *
	 * @access public
	 * @param string $url
	 * @return array
	 * @static
	 */
	public static function analyze($url = '') {
		if (isset(self::$__mapped[$url])) {
			return self::$__mapped[$url];
		}

		$params = self::defaults();
		$params['ext'] = null;
		$params['query'] = self::segment('query');
		$params['params'] = array();

		if (empty($url)) {
			$url = self::segment('route');
		}

		if ($url === '/') {
			return $params;
		}

		$url = trim($url, '/');
		$parts = explode('/', $url);
		$inflect = function($string) {
			return str_replace('-', '_', preg_replace('/[^a-z\-_]+/i', '', $string));
		};

		// Module
		if (is_dir(APP_MODULES . $parts[0])) {
			$params['module'] = $inflect($parts[0]);

			if (count($parts) == 1) {
				$params['controller'] = $params['module'];
			} else {
				$params['controller'] = $inflect($parts[1]);
				unset($parts[1]);
			}

		// Controller
		} else {
			$params['controller'] = $inflect($parts[0]);
		}

		unset($parts[0]);

		if (!empty($parts)) {
			// Action
			$action = array_shift($parts);

			if (is_string($action) && strpos($action, ':') === false) {
				if (strpos($action, '.') !== false) {
					list($action, $ext) = explode('.', $action);
					$params['ext'] = $inflect($ext);
				}

				$params['action'] = $inflect($action);
			} else {
				array_unshift($parts, $action);
			}

			// Query
			foreach ($parts as $index => $part) {
				// Params
				if (strpos($part, ':') !== false) {
					list($key, $value) = explode(':', $part);
					$params['query'][$key] = $value;

				// Arguments
				} else {
					$params['params'][] = $part;
				}

				unset($parts[$index]);
			}
		}

		self::$__mapped[$url] = $params;

		return $params;
	}

	/**
	 * Return the base URL if the app was not placed in the root directory.
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function base() {
		return self::segment('base');
	}

	/**
	 * Combine an array route into a string URL path. Injects arguments and query parameters.
	 *
	 * @access public
	 * @param array $route
	 * @return string
	 * @static
	 */
	public static function build($route = '') {
		if (!is_array($route)) {
			return (string)$route;
			
		} else if (empty($rout)) {
			$route = self::current();
		}

		$route = self::defaults($route);
		$path = self::base();

		if ($route['module'] != 'core') {
			$path .= $route['module'] .'/';
		}

		$path .= $route['controller'] .'/'. $route['action'];
		$path .= !empty($route['ext']) ? '.'. $route['ext'] .'/' : '/';

		if (!empty($route['params'])) {
			$path .= implode('/', $route['params']) .'/';
		}

		if (!empty($route['query'])) {
			foreach ($route['query'] as $key => $value) {
				$path .= str_replace(' ', '', $key) .':'. urlencode($value) .'/';
			}
		}

		if (!empty($route['#'])) {
			$path .= '#'. $route['#'];
		}

		return $path;
	}

	/**
	 * Return the current analyzed route as an array of values.
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function current() {
		return self::$__current;
	}

	/**
	 * Maps the default routes and determines the controller and container.
	 * Can be merged with a dynamic route to map missing segments.
	 *
	 * @access public
	 * @param array $data
	 * @return array
	 * @static
	 */
	public static function defaults(array $data = array()) {
		$data = $data + array(
			'ext' => '',
			'query' => array(),
			'params' => array()
		);

		if (empty($data['module'])) {
			$data['module'] = 'core';
		}

		if (empty($data['controller'])) {
			$data['controller'] = 'core';
		}

		if (empty($data['action'])) {
			$data['action'] = 'index';
		}

		return $data;
	}

	/**
	 * Detects whether to construct a URL if an array is given, return a defined slug if a string is given,
	 * or construct a URL if an array is given with a slug index.
	 *
	 * @access public
	 * @param string|array $url
	 *      - 'slugName' // Returns the defined array for the slug
	 *      - array('slug' => 'slugName', 'id' => 5) // Merges with slugName's array and appends the id index
	 *      - array('controller' => 'main', 'action' => 'index') // Merges with default values and returns
	 * @return string|array
	 * @static
	 */
	public static function detect($url) {
		if (is_array($url)) {
			if (isset($url['slug'])) {
				$slug = $url['slug'];
				unset($url['slug']);

				if ($route = self::slug($slug)) {
					return ($url + $route);
				}
			} else {
				return ($url + Router::current());
			}
		}

		return self::slug($url);
	}

	/**
	 * Parses the current URL into multiple segments as well as parses the current route into an application path.
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public static function initialize() {
		list($base, $route) = explode('index.php', $_SERVER['PHP_SELF']);

		if (empty($route)) {
			$route = '/';
		}

		self::$__segments = array(
			'protocol'  => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://',
			'host'      => $_SERVER['HTTP_HOST'],
			'base'      => $base,
			'route'     => $route,
			'query'     => $_GET
		);

		self::$__current = self::analyze($route);
	}

	/**
	 * Add a custom defined route that matches to an internal destination.
	 *
	 * @access public
	 * @param string $url
	 * @param string|array $route
	 * @return void
	 * @static
	 */
	public static function map($url, $route = array()) {
		// @todo
	}

	/**
	 * Add a slug to the routing system. A slug is a string that is used as a lookup for a route (array).
	 *
	 * @access public
	 * @param string $key
	 * @param string|array $route
	 * @return void
	 * @static
	 */
	public static function mapSlug($key, $route = array()) {
		if (is_array($route)) {
			$route = self::defaults($route);
		}

		self::$__slugs[(string)$key] = $route;
	}

	/**
	 * Return a single segment, or all segments, or all segments assembled.
	 *
	 * @access public
	 * @param mixed $key
	 * @return string|array
	 * @static
	 */
	public static function segment($key = false) {
		if ($key === true) {
			$segments = self::$__segments;

			if (!empty($segments['base'])) {
				$segments['base'] = '/'. trim($segments['base'], '/');
			}

			if (!empty($segments['query'])) {
				$segments['query'] = http_build_query($segments['query']);
			}

			return implode('', $segments);

		} else if (isset(self::$__segments[$key])) {
			return self::$__segments[$key];
		}

		return self::$__segments;
	}

	/**
	 * Returns a given slugs route if it has been defined.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 * @static
	 */
	public static function slug($key) {
		return isset(self::$__slugs[$key]) ? self::$__slugs[$key] : null;
	}

}
