<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

use \titon\source\Titon;
use \titon\source\core\routes\Route;
use \titon\source\core\routes\RouteInterface;

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
	 * Analyze a string (a route found in the address bar) into an array that maps to the correct module, controller and action.
	 *
	 * @access public
	 * @param string $url
	 * @return Route
	 */
	public function analyze($url) {
		if (isset($this->__mapped[$url])) {
			return $this->__mapped[$url];
		}

		$params = $this->defaults();
		$params['query'] = $this->segment('query');

		if ($url === '/') {
			return $params;
		}

		// Remove base path if it exists
		$base = $this->base();
		$baseLength = strlen($base);

		if (substr($url, 0, $baseLength) == $base) {
			$url = substr($url, $baseLength);
		}

		$url = trim($url, '/');
		$parts = explode('/', $url);
		$modules = Titon::app()->getModules();
		$controllers = Titon::app()->getControllers();

		// If the module is found within the URL and has been bootstrapped
		if (in_array($parts[0], $modules)) {
			$params['module'] = $this->inflect($parts[0]);
			unset($parts[0]);

			// If parts isn't empty, was controller listed within the module?
			if (!empty($parts) && in_array($parts[1], $controllers[$params['module']])) {
				$params['controller'] = $this->inflect($parts[1]);
				unset($parts[1]);
			}

		// If the module is not in the URL, fallback to default
		// Since no module, first path must be a controller
		} else {
			$params['controller'] = $this->inflect($parts[0]);
			unset($parts[0]);
		}

		// Parse out the action, query params, named params, and arguments
		if (!empty($parts)) {

			// Use as action if not a query param or has an extension
			$action = array_shift($parts);

			if (strpos($action, ':') === false) {
				if (strpos($action, '.') !== false) {
					list($action, $ext) = explode('.', $action);
					$params['ext'] = $ext;
				}

				$params['action'] = $this->inflect($action);
			} else {
				array_unshift($parts, $action);
			}

			// If the part contains a colon, its a query param, else action argument
			foreach ($parts as $index => $part) {
				if (strpos($part, ':') !== false) {
					list($key, $value) = explode(':', $part);
					$params['query'][$key] = $value;

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
	 * Takes a route in array format and processes it down into a single string that represents an interal URL path.
	 *
	 * @access public
	 * @param array $route
	 * @return string
	 */
	public function build(array $route) {
		$defaults = $this->defaults();
		$route = $this->defaults($route);

		if ($defaults === $route) {
			return $this->base();
		}

		$path = array();
		$path[] = trim($this->base(), '/');

		// Module, controller, action
		if ($route['module'] != Titon::app()->getDefaultModule()) {
			$path[] = $route['module'];
		}

		$path[] = $route['controller'];

		if (!empty($route['ext'])) {
			$path[] = $route['action'] .'.'. $route['ext'];
			
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
					$route['params'][$key] = $value;

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
				$path[] = str_replace(' ', '', $key) .':'. urlencode($value);
			}
		}

		if (!empty($route['#'])) {
			$path[] = '#'. $route['#'];
		}

		unset($route);

		return '/'. implode('/', $path);
	}

	/**
	 * Return the current analyzed route object.
	 *
	 * @access public
	 * @return string
	 */
	public function current() {
		return $this->__current;
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
			'query' => array(),
			'params' => array()
		);

		if (empty($data['module'])) {
			$data['module'] = Titon::app()->getDefaultModule();
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
				$slug = $url['slug'];
				$route = $this->slug($slug);
				
				unset($url['slug']);

				if (!empty($route)) {
					$url = $url + $route;
				}
			} else {
				// @todo
			}
		}

		return $this->slug($url);
	}

	/**
	 * Inflects a URL segment to it's proper internal format.
	 * Dashes are replaced with underscores, non alpha-numeric are removed.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 */
	public function inflect($string) {
		return str_replace('-', '_', preg_replace('/[^a-z\-_]+/i', '', $string));
	}

	/**
	 * Parses the current URL into multiple segments as well as parses the current route into an application path.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
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
	 * Add a custom defined route object that matches to an internal destination.
	 *
	 * @access public
	 * @param string $key
	 * @param RouteInterface $route
	 * @return this
	 * @chainable
	 */
	public function map($key, RouteInterface $route) {
		$this->__routes[$key] = $route;

		return $this;
	}

	/**
	 * Add a slug to the routing system. A slug is a string that is used as a lookup for a route (array).
	 *
	 * @access public
	 * @param string $key
	 * @param string|array $route
	 * @return this
	 * @chainable
	 */
	public function mapSlug($key, $route = array()) {
		if (is_array($route)) {
			$route = $this->defaults($route);
		}

		$this->__slugs[$key] = $route;

		return $this;
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
