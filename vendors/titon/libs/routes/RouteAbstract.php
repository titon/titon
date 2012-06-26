<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\routes;

use titon\Titon;
use titon\base\Base;
use titon\libs\routes\Route;
use titon\libs\routes\RouteException;
use titon\libs\traits\Attachable;

/**
 * Represents the skeleton for an individual route. A route matches an internal URL that gets analyzed into multiple parts:
 * module, controller, action, extension, arguments and query parameters. A route can be used to mask a certain URL to
 * another internal destination.
 *
 * @package	titon.libs.routes
 * @uses	titon\Titon
 * @uses	titon\libs\routes\RouteException
 * @abstract
 */
abstract class RouteAbstract extends Base implements Route {
	use Attachable;

	/**
	 * Pre-defined regex patterns.
	 */
	const ALPHA = '([a-z\_\-\+]+)';
	const ALNUM = '([a-z0-9\_\-\+]+)';
	const NUMERIC = '([0-9]+)';
	const WILDCARD = '(.*)';

	/**
	 * Configuration.
	 *
	 *	secure 		- When true, will only match if under HTTPS
	 *	static 		- A static route that contains no patterns
	 *	method 		- The types of acceptable HTTP methods (defaults to all)
	 *	patterns 	- Custom defined regex patterns
	 *
	 * @access public
	 * @var array
	 */
	protected $_config = [
		'secure' => false,
		'static' => false,
		'method' => [],
		'patterns' => []
	];

	/**
	 * The compiled regex pattern.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_compiled = null;

	/**
	 * Patterns matched during the isMatch() process.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_matches = [];

	/**
	 * The path to match.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_path = '/';

	/**
	 * Collection of route parameters.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_route = [];

	/**
	 * Custom defined tokens.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tokens = [];

	/**
	 * The corresponding URL when a match is found.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_url;

	/**
	 * Store the routing configuration.
	 *
	 * @access public
	 * @param string $path
	 * @param array $route
	 * @param array $config
	 */
	public function __construct($path, array $route = [], array $config = []) {
		$this->_path = $path;
		$this->_route = Titon::router()->defaults($route);

		parent::__construct($config);
	}

	/**
	 * Compile the given path into a detectable regex pattern.
	 *
	 * @access public
	 * @return string
	 */
	public function compile() {
		if ($this->isCompiled()) {
			return $this->_compiled;
		}

		$path = ($this->_path !== '/') ? rtrim($this->_path, '/') : $this->_path;
		$compiled = str_replace(['/', '.'], ['\/', '\.'], $path);
		$patterns = $this->config->patterns;

		if (!$this->isStatic()) {
			preg_match_all('/([\{|\(|\[|\<])([a-z]+)([\}|\)|\]|\>])/i', $this->_path, $matches, PREG_SET_ORDER);

			if (!empty($matches)) {
				foreach ($matches as $match) {
					$m1 = isset($match[1]) ? $match[1] : '';
					$m2 = isset($match[2]) ? $match[2] : '';
					$m3 = isset($match[3]) ? $match[3] : '';

					if ($m1 === '{' && $m3 === '}') {
						$compiled = str_replace($match[0], self::ALPHA, $compiled);

					} else if ($m1 === '[' && $m3 === ']') {
						$compiled = str_replace($match[0], self::NUMERIC, $compiled);

					} else if ($m1 === '(' && $m3 === ')') {
						$compiled = str_replace($match[0], self::WILDCARD, $compiled);

					} else if ($m1 === '<' && $m3 === '>' && isset($patterns[$m2])) {
						$compiled = str_replace($match[0], $patterns[$m2], $compiled);
					}

					$this->_tokens[] = $m2;
				}
			} else {
				$this->config->static = true;
			}
		}

		// Append a wildcard to the end incase of parameters or arguments
		$compiled .= self::WILDCARD . '?';

		// Save the compiled regex
		$this->_compiled = '/^' . $compiled . '/i';

		return $this->_compiled;
	}

	/**
	 * Grab a param from the route.
	 *
	 * @access public
	 * @param string $key
	 * @return string|null
	 */
	public function getParam($key = null) {
		return isset($this->_route[$key]) ? $this->_route[$key] : null;
	}

	/**
	 * Return all route params.
	 *
	 * @access public
	 * @return string|null
	 */
	public function getParams() {
		return $this->_route;
	}

	/**
	 * Compile the route and attach the request object.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->compile();

		$this->attachObject('request', function() {
			return Titon::registry()->factory('titon\net\Request');
		});
	}

	/**
	 * Has the regex pattern been compiled?
	 *
	 * @access public
	 * @return boolean
	 */
	public function isCompiled() {
		return ($this->_compiled !== null);
	}

	/**
	 * Attempt to match the object against a passed URL.
	 * If a match is found, extract pattern values and parameters.
	 *
	 * @access public
	 * @param string $url
	 * @return boolean
	 */
	public function isMatch($url) {
		if ($this->_path === $url) {
			return true;
		}

		if (preg_match($this->compile(), $url, $matches)) {
			$this->_matches = $matches;
			$this->_url = array_shift($matches);

			// Get pattern values
			if (!empty($matches) && !empty($this->_tokens)) {
				$locales = Titon::g11n()->listing();
				$modules = Titon::app()->getModules();
				$controllers = Titon::app()->getControllers();

				foreach ($this->_tokens as $token) {
					switch ($token) {
						case 'locale':
							// Is their a locale? Has it been setup?
							if (in_array($matches[0], $locales)) {
								$this->_route['locale'] = array_shift($matches);
							} else {
								array_shift($matches);
							}
						break;
						case 'module':
							// Is it a module? Check against the installed modules.
							if (in_array($matches[0], $modules)) {
								$this->_route['module'] = array_shift($matches);
							} else {
								array_shift($matches);
							}
						break;
						case 'controller':
							// Is it a controller? Check within the modules controllers.
							if (isset($controllers[$this->_route['module']]) && in_array($matches[0], $controllers[$this->_route['module']])) {
								$this->_route['controller'] = array_shift($matches);
							} else {
								array_shift($matches);
							}
						break;
						default:
							$this->_route[$token] = array_shift($matches);
						break;
					}
				}
			}

			// Detect query string and parameters
			if (!empty($matches[0]) && $matches[0] !== '/') {
				$parts = explode('/', trim($matches[0], '/'));

				foreach ($parts as $part) {
					if (strpos($part, ':') !== false) {
						list($key, $value) = explode(':', $part);
						$this->_route['query'][$key] = $value;

					} else {
						$this->_route['params'][] = $part;
					}
				}

				unset($matches);
			}

			return true;
		}

		return false;
	}

	/**
	 * Validates the route matches the correct HTTP method.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isMethod() {
		$method = (array) $this->config->method;

		if (!empty($method) && !in_array($this->request->method(), $method)) {
			return false;
		}

		return true;
	}

	/**
	 * Validates the route matches a secure connection.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isSecure() {
		if ($this->config->secure && !$this->request->isSecure()) {
			return false;
		}

		return true;
	}

	/**
	 * Is the route static (no regex patterns)?
	 *
	 * @access public
	 * @return boolean
	 */
	public function isStatic() {
		return $this->config->static;
	}

	/**
	 * Validate the URL, it's supported HTTP method and secure connection.
	 *
	 * @access public
	 * @param string $url
	 * @return boolean
	 */
	public function match($url) {
		return ($this->isMatch($url) && $this->isMethod() && $this->isSecure());
	}

}
