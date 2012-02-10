<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use \titon\libs\routes\core\DefaultRoute;
use \titon\libs\routes\Route;

/**
 * The Router determines the current routing request, based on the URL address and environment.
 * Stores the current route, its parsed segments and the base URL.
 * Additionally, it will construct a URL based on an array of options, or deconstruct a URL into an array of options.
 * You can also define custom slugs or routes to be used for internal routing mechanisms.
 *
 * @package	titon.core
 */
class Router {

	/**
	 * Base folder structure if the application was placed within a directory.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_base = '';

	/**
	 * The matched route object.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_current = null;

	/**
	 * An array of all paths that have been analyzed. Used for fast lookups.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_mapped = array();

	/**
	 * Manually defined aesthetic routes that re-route internally.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_routes = array();

	/**
	 * The current URL broken up into multiple segments: protocol, host, route, query, base
	 *
	 * @access protected
	 * @var array
	 */
	protected $_segments = array();

	/**
	 * Manually defined slugs that re-route to an internal controller and action.
	 * Primarily used to permanently define a route (array format) destination as a re-useable string.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_slugs = array();

	/**
	 * Return the base URL if the app was not placed in the root directory.
	 *
	 * @access public
	 * @return string
	 */
	public function base() {
		return $this->_base;
	}

	/**
	 * Takes a route in array format and processes it down into a single string that represents an interal URL path.
	 *
	 * @access public
	 * @param array $route
	 * @return string
	 */
	public function build(array $route) {
		$route = $this->defaults($route);

		if ($this->defaults() === $route) {
			return $this->base();
		}

		$path = array();
		$path[] = trim($this->base(), '/');

		// Module, controller, action
		$path[] = $route['module'];
		$path[] = $route['controller'];

		if (!empty($route['ext'])) {
			$path[] = $route['action'] . '.' . $route['ext'];

		} else if ($route['action'] != 'index' || !empty($route['params'])) {
			$path[] = $route['action'];
		}

		unset($route['module'], $route['controller'], $route['action'], $route['ext']);

		// Gather the params and query
		if (!empty($route)) {
			foreach ($route as $key => $value) {
				if ($key === 'params' || $key === 'query' || $key === '#') {
					continue;

				} else if (is_numeric($key)) {
					$route['params'][] = $value;

				} else if (is_string($key)) {
					$route['query'][$key] = $value;
				}

				unset($route[$key]);
			}
		}

		// Action arguments / parameters
		if (!empty($route['params'])) {
			$path[] = implode('/', $route['params']);
		}

		if (!empty($route['query'])) {
			foreach ($route['query'] as $key => $value) {
				$path[] = str_replace(' ', '', $key) . ':' . urlencode($value);
			}
		}

		if (!empty($route['#'])) {
			$path[] = '#' . $route['#'];
		}

		unset($route);

		return '/' . implode('/', $path);
	}

	/**
	 * Return the current matched route object.
	 *
	 * @access public
	 * @return string
	 */
	public function current() {
		return $this->_current;
	}

	/**
	 * Maps the default routes and determines the controller and module.
	 * Can be merged with a dynamic route to map missing segments.
	 *
	 * @access public
	 * @param array $data
	 * @return array
	 */
	public function defaults(array $data = array()) {
		$data = $data + array(
			'ext' => '',
			'query' => $this->segments('query'),
			'params' => array()
		);

		if (empty($data['module'])) {
			$data['module'] = 'pages';
		}

		if (empty($data['controller'])) {
			$data['controller'] = 'index';
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
	 * @return string|array
	 */
	public function detect($url) {
		if (is_array($url)) {
			if (isset($url['slug'])) {
				$route = $this->slugs($url['slug']);
				
				if ($route) {
					unset($url['slug']);
					$route = $url + $route;
				}
			} else {
				$route = $url;
			}
		} else if ($slug = $this->slugs($url)) {
			$route = $slug;
		} else {
			$route = $url;
		}
		
		if (is_array($route)) {
			$route = $this->build($route);
		}
		
		return $route;
	}

	/**
	 * Parses the current URL into multiple segments, maps the default routing objects and attempts to match.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		list($base, $path) = explode('index.php', $_SERVER['PHP_SELF']);

		if (empty($path)) {
			$path = '/';
		}

		if (!empty($base)) {
			$this->_base = rtrim($base, '/');
		}

		// Store the current URL and query as router segments
		$this->_segments = array_merge(parse_url($_SERVER['REQUEST_URI']), array(
			'scheme' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http',
			'query' => $_GET,
			'host' => $_SERVER['HTTP_HOST'],
			'path' => $path
		));

		// Map default internal routes
		$this->map('moduleControllerActionExt', new DefaultRoute('/{module}/{controller}/{action}.{ext}'));
		$this->map('moduleControllerAction', new DefaultRoute('/{module}/{controller}/{action}'));
		$this->map('moduleController', new DefaultRoute('/{module}/{controller}'));
		$this->map('module', new DefaultRoute('/{module}'));
		$this->map('root', new DefaultRoute('/', array(), array('static' => true)));

		// Match the current URL to a route
		$this->_current = $this->match($path);
	}

	/**
	 * Add a custom defined route object that matches to an internal destination.
	 *
	 * @access public
	 * @param string $key
	 * @param Route $route
	 * @return Router
	 * @chainable
	 */
	public function map($key, Route $route) {
		$this->_routes[$key] = $route;

		return $this;
	}

	/**
	 * Add a slug to the routing system. A slug is a string that is used as a lookup for a route (array).
	 *
	 * @access public
	 * @param string $key
	 * @param string|array $route
	 * @return Router
	 * @chainable
	 */
	public function mapSlug($key, $route = array()) {
		if (is_array($route)) {
			$route = $this->defaults($route);
		}

		$this->_slugs[$key] = $route;

		return $this;
	}

	/**
	 * Attempt to match an internal route.
	 *
	 * @access public
	 * @param string $url
	 * @return Route
	 */
	public function match($url) {
		foreach ($this->_routes as $route) {
			if ($route->match($url)) {
				return $route;
			}
		}

		return null;
	}

	/**
	 * Return a single segment, or all segments, or all segments assembled.
	 *
	 * @access public
	 * @param mixed $key
	 * @return string|array
	 */
	public function segments($key = false) {
		if ($key === true) {
			$segments = $this->segments();

			$url  = $segments['scheme'] . '://';
			$url .= $segments['host'];
			$url .= $this->base();
			$url .= $segments['path'];

			if (!empty($segments['query'])) {
				$url .= '?' . http_build_query($segments['query']);
			}

			return $url;

		} else if (isset($this->_segments[$key])) {
			return $this->_segments[$key];
		}

		return $this->_segments;
	}

	/**
	 * Returns a given slugs route if it has been defined.
	 *
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function slugs($key) {
		return isset($this->_slugs[$key]) ? $this->_slugs[$key] : null;
	}

}
