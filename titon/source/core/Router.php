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
use \titon\library\routes\core\Route;
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
	private $__current = null;

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
	 * Return the current matched route object.
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
	 * Parses the current URL into multiple segments, maps the default routing objects and attempts to match.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		list($base, $url) = explode('index.php', $_SERVER['PHP_SELF']);

		if (empty($url)) {
			$url = '/';
		}

		// Store the current URL and query as router segments
		$this->__segments = array(
			'protocol'  => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://',
			'host'      => $_SERVER['HTTP_HOST'],
			'base'      => $base,
			'route'     => $url,
			'query'     => $_GET
		);

		// Map default internal routes
		$this->map('controllerActionExt', new Route('/{controller}/{action}.{ext}'));
		$this->map('controllerAction', new Route('/{controller}/{action}'));
		$this->map('controller', new Route('/{controller}'));
		$this->map('moduleControllerActionExt', new Route('/{module}/{controller}/{action}.{ext}'));
		$this->map('moduleControllerAction', new Route('/{module}/{controller}/{action}'));
		$this->map('moduleController', new Route('/{module}/{controller}'));
		$this->map('module', new Route('/{module}'));
		$this->map('root', new Route('/'));

		// Match the current URL to a route
		$this->__current = $this->match($url);
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

	public function match($url) {
		foreach ($this->__routes as $key => $route) {
			if ($route->match($url)) {
				$this->__current = $route;
				break;
			}
		}

		if (!$this->__current) {
			$this->__current = new Route();
		}
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
		return isset($this->__slugs[$key]) ? $this->__slugs[$key] : null;
	}

}
