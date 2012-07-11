<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use titon\Titon;
use titon\libs\routes\Route;
use titon\libs\traits\Cacheable;
use titon\utility\Inflector;
use titon\utility\Hash;

/**
 * The Router determines the current routing request, based on the URL address and environment.
 * Stores the current route, its parsed segments and the base URL.
 * Additionally, it will construct a URL based on an array of options, or deconstruct a URL into an array of options.
 * You can also define custom slugs or routes to be used for internal routing mechanisms.
 *
 * @package	titon.core
 */
class Router {
	use Cacheable;

	/**
	 * Base folder structure if the application was placed within a directory.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_base = '/';

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
	protected $_mapped = [];

	/**
	 * Manually defined aesthetic routes that re-route internally.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_routes = [];

	/**
	 * The current URL broken up into multiple segments: protocol, host, route, query, base
	 *
	 * @access protected
	 * @var array
	 */
	protected $_segments = [];

	/**
	 * Manually defined slugs that re-route to an internal controller and action.
	 * Primarily used to permanently define a route (array format) destination as a re-useable string.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_slugs = [];

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
	 * Takes a route in array format and processes it down into a single string that represents an internal URL path.
	 *
	 * @access public
	 * @param array $route
	 * @return string
	 */
	public function build(array $route = []) {
		$cacheKey = $this->createCacheKey(array(__METHOD__, $route));

		if ($cache = $this->getCache($cacheKey)) {
			return $cache;
		}

		$defaults = $this->defaults();
		$route = $this->defaults($route);
		$base = $this->base();

		if ($defaults === $route) {
			return $base;
		}

		$path = [];
		$args = [];
		$query = [];
		$fragment = '';
		$ext = '';

		foreach ($route as $key => $value) {
			if (in_array($key, array('module', 'controller', 'action', 'ext'), true)) {
				continue;

			} else if ($key === 'args') {
				$args = (array) $value + $args;

			} else if ($key === 'query') {
				$query = (array) $value + $query;

			} else if ($key === '#') {
				$fragment = $value;

			} else if (is_numeric($key)) {
				$args[] = $value;

			} else if (is_string($key)) {
				$query[$key] = $value;
			}

			unset($route[$key]);
		}

		if ($base !== '/' && $base !== '') {
			$path[] = trim($base, '/');
		}

		// Module, controller, action
		$path[] = $route['module'];
		$path[] = $route['controller'];

		if ($route['action'] !== 'index' || !empty($args) || !empty($route['ext'])) {
			$path[] = $route['action'];
		}

		if (!empty($route['ext'])) {
			$ext = $route['ext'];
		}

		unset($route);

		// Action arguments
		if (!empty($args)) {
			foreach ($args as $arg) {
				$path[] = urlencode($arg);
			}
		}

		$path = '/' . implode('/', $path);

		if (!empty($ext)) {
			$path .= '.' . $ext;
		}

		if (!empty($query)) {
			$path .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC1738);
		}

		if (!empty($fragment)) {
			if (is_array($fragment)) {
				$path .= '#' . http_build_query($fragment, '', '&', PHP_QUERY_RFC1738);
			} else {
				$path .= '#' . urlencode($fragment);
			}
		}

		return $this->setCache($cacheKey, $path);
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
	public function defaults($data = []) {
		$data = $data + [
			'ext' => '',
			'query' => $this->segments('query'),
			'args' => []
		];

		if (empty($data['module'])) {
			$data['module'] = 'pages';
		} else {
			$data['module'] = Inflector::route($data['module']);
		}

		if (empty($data['controller'])) {
			$data['controller'] = 'index';
		} else {
			$data['controller'] = Inflector::route($data['controller']);
		}

		if (empty($data['action'])) {
			$data['action'] = 'index';
		} else {
			$data['action'] = Inflector::route($data['action']);
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
				$route = $this->slug($url['slug']);

				// Use array_merge to not remove numerical indices
				if ($route) {
					$route = array_merge($route, $url);
				} else {
					$route = $url;
				}

				unset($route['slug']);
			} else {
				$route = $url;
			}
		} else if ($slug = $this->slug($url)) {
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

		if (!empty($base) && $base !== '/') {
			$this->_base = rtrim($base, '/');
		}

		// Store the current URL and query as router segments
		$this->_segments = array_merge(parse_url($_SERVER['REQUEST_URI']), [
			'scheme' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http',
			'query' => $_GET,
			'host' => $_SERVER['HTTP_HOST'],
			'path' => $path
		]);

		// Map default internal routes
		$routeClass = Titon::g11n()->isEnabled()
			? 'titon\libs\routes\g11n\LocaleRoute'
			: 'titon\libs\routes\core\DefaultRoute';

		$this->map('moduleControllerActionExt', new $routeClass('/{module}/{controller}/{action}.{ext}'));
		$this->map('moduleControllerAction', new $routeClass('/{module}/{controller}/{action}'));
		$this->map('moduleController', new $routeClass('/{module}/{controller}'));
		$this->map('module', new $routeClass('/{module}'));
		$this->map('root', new $routeClass('/', [], ['static' => true]));

		// Match the current URL to a route
		$this->_current = $this->match($path);
	}

	/**
	 * Add a custom defined route object that matches to an internal destination.
	 *
	 * @access public
	 * @param string $key
	 * @param titon\libs\routes\Route $route
	 * @return titon\core\Router
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
	 * @return titon\core\Router
	 * @chainable
	 */
	public function mapSlug($key, $route = []) {
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
	 * @return titon\libs\routes\Route
	 */
	public function match($url) {
		foreach ($this->_routes as $route) {
			if ($route->match($url)) {
				return $route;
			}
		}

		return $this->_routes['root'];
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
			$base = $this->base();

			$url = $segments['scheme'] . '://';
			$url .= $segments['host'];

			if ($base !== '/') {
				$url .= $base;
			}

			$url .= $segments['path'];

			if (!empty($segments['query'])) {
				$url .= '?' . http_build_query($segments['query'], '', '&', PHP_QUERY_RFC1738);
			}

			if (!empty($segments['fragment'])) {
				$url .= '#' . $segments['fragment'];
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
	public function slug($key) {
		return Hash::get($this->_slugs, $key);
	}

}
