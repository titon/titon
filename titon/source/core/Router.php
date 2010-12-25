<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

/**
 * The Router determines the current routing request, based on the URL address and environment.
 * Stores the current route, its parsed segments and the base URL.
 * Additionally, it will construct a URL based on an array of options, or deconstruct a URL into an array of options.
 * You can also define custom slugs or routes to be used for internal routing mechanisms.
 *
 * @package	titon.source.core
 */
class Router {

	/**
	 * The current route broken up into its parts.
	 *
	 * @access private
	 * @var array
	 */
	private $__current = array();

	/**
	 * An array of all paths that have been analyzed. Used for fast lookups.
	 *
	 * @access private
	 * @var array
	 */
	private $__mapped = array();

	/**
	 * Manually defined aesthetic routes that re-route internally.
	 *
	 * @access private
	 * @var array
	 */
	private $__routes = array();

	/**
	 * The current URL broken up into multiple segments: protocol, host, route, query, base
	 *
	 * @access private
	 * @var array
	 */
	private $__segments = array();

	/**
	 * Manually defined slugs that re-route to an internal controller and action.
	 * Primarily used to permanently define a route (array format) destination as a re-useable string.
	 *
	 * @access private
	 * @var array
	 */
	private $__slugs = array();

	/**
	 * Parses the current URL into multiple segments as well as parses the current route into an application path.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		list($base, $route) = explode('index.php', $_SERVER['PHP_SELF']);

		if (empty($route)) {
			$route = '/';
		}

		$this->__segments = array(
			'protocol'  => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://',
			'host'      => $_SERVER['HTTP_HOST'],
			'base'      => $base,
			'route'     => $route,
			'query'     => $_GET
		);

		$this->__current = $this->analyze($route);
	}

	/**
	 * Analyze a string (a route found in the address bar) into an array that maps to the correct module, controller and action.
	 *
	 * @access public
	 * @param string $url
	 * @return array
	 */
	public function analyze($url = '') {
		if (isset($this->__mapped[$url])) {
			return $this->__mapped[$url];
		}

		$params = $this->defaults();
		$params['query'] = $this->segment('query');

		if (empty($url)) {
			$url = $this->segment('route');
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

		$this->__mapped[$url] = $params;

		return $params;
	}

	/**
	 * Return the base URL if the app was not placed in the root directory.
	 *
	 * @access public
	 * @return string
	 */
	public function base() {
		return $this->segment('base');
	}

	/**
	 * Combine an array route into a string URL path. Injects arguments and query parameters.
	 *
	 * @access public
	 * @param array $route
	 * @return string
	 */
	public function build($route = '') {
		if (!is_array($route)) {
			return (string)$route;
			
		} else if (empty($rout)) {
			$route = $this->current();
		}

		$route = $this->defaults($route);
		$path = $this->base();

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
	 */
	public function current() {
		return $this->__current;
	}

	/**
	 * Maps the default routes and determines the controller and container.
	 * Can be merged with a dynamic route to map missing segments.
	 *
	 * @access public
	 * @param array $data
	 * @return array
	 */
	public function defaults(array $data = array()) {
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
	 */
	public function detect($url) {
		if (is_array($url)) {
			if (isset($url['slug'])) {
				$slug = $url['slug'];
				unset($url['slug']);

				if ($route = $this->slug($slug)) {
					return ($url + $route);
				}
			} else {
				return ($url + $this->current());
			}
		}

		return $this->slug($url);
	}

	/**
	 * Add a custom defined route that matches to an internal destination.
	 *
	 * @access public
	 * @param string $url
	 * @param string|array $route
	 * @return void
	 */
	public function map($url, $route = array()) {
		// @todo
	}

	/**
	 * Add a slug to the routing system. A slug is a string that is used as a lookup for a route (array).
	 *
	 * @access public
	 * @param string $key
	 * @param string|array $route
	 * @return void
	 */
	public function mapSlug($key, $route = array()) {
		if (is_array($route)) {
			$route = $this->defaults($route);
		}

		$this->__slugs[$key] = $route;
	}

	/**
	 * Return a single segment, or all segments, or all segments assembled.
	 *
	 * @access public
	 * @param mixed $key
	 * @return string|array
	 */
	public function segment($key = false) {
		if ($key === true) {
			$segments = $this->__segments;

			if (!empty($segments['base'])) {
				$segments['base'] = '/'. trim($segments['base'], '/');
			}

			if (!empty($segments['query'])) {
				$segments['query'] = http_build_query($segments['query']);
			}

			return implode('', $segments);

		} else if (isset($this->__segments[$key])) {
			return $this->__segments[$key];
		}

		return $this->__segments;
	}

	/**
	 * Returns a given slugs route if it has been defined.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function slug($key) {
		return $this->__slugs[$key] ?: null;
	}

}
