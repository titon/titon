<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\library\routes;

use \titon\source\Titon;
use \titon\source\library\routes\RouteInterface;
use \titon\source\log\Exception;

/**
 * Represents the skeleton for an individual route. A route matches an internal URL that gets analyzed into multiple parts:
 * module, controller, action, extension, arguments and query parameters. A route can be used to mask a certain URL to
 * another internal destination.
 *
 * @package	titon.source.library.routes
 * @uses	titon\source\Titon
 * @uses	titon\source\log\Exception
 */
abstract class RouteAbstract implements RouteInterface {

	/**
	 * Pre-defined regex patterns.
	 */
	const ALPHABETIC = '([a-z\_\-\+]+)';
	const NUMERIC = '([0-9]+)';
	const WILDCARD = '(.*)';

	/**
	 * Request object.
	 *
	 * @access public
	 * @var titon\source\net\Request
	 */
	public $request;

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
	protected $_matches = array();

	/**
	 * The types of acceptable HTTP methods. Defaults to all.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_method = array();

	/**
	 * The path to match.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_path = '/';

	/**
	 * Custom defined regex patterns.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_patterns = array();

	/**
	 * Collection of route parameters.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_route = array();

	/**
	 * When true, will only match if under HTTPS.
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_secure = false;

	/**
	 * A static route contains no patterns.
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_static = false;

	/**
	 * Custom defined tokens.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tokens = array();

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
	 * @return void
	 */
	public function __construct($path, array $route = array(), array $config = array()) {
		$this->_path = $path;
		$this->_route = Titon::router()->defaults($route);

		// Store configuration
		if (isset($config['secure'])) {
			$this->_secure = (bool)$config['secure'];
		}

		if (isset($config['static'])) {
			$this->_static = (bool)$config['static'];
		}

		if (isset($config['method'])) {
			$this->_method = (array)$config['method'];
		}

		if (isset($config['patterns'])) {
			$this->_patterns = $this->_patterns + (array)$config['patterns'];
		}

		// Compile when class is built
		$this->compile();

		// Grab the Request object
		$this->request = Titon::registry()->factory('titon\source\net\Request');
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
		
		$compiled = str_replace(array('/', '.'), array('\/', '\.'), rtrim($this->_path, '/'));

		if (!$this->isStatic()) {
			preg_match_all('/([\{|\(|\[|\<])([a-z]+)([\}|\)|\]|\>])/i', $this->_path, $matches, PREG_SET_ORDER);
			
			if (!empty($matches)) {
				foreach ($matches as $match) {
					switch (true) {
						case ($match[1] == '{' && $match[3] == '}'):
							$compiled = str_replace($match[0], self::ALPHABETIC, $compiled);
						break;

						case ($match[1] == '[' && $match[3] == ']'):
							$compiled = str_replace($match[0], self::NUMERIC, $compiled);
						break;

						case ($match[1] == '(' && $match[3] == ')'):
							$compiled = str_replace($match[0], self::WILDCARD, $compiled);
						break;

						case ($match[1] == '<' && $match[3] == '>'):
							if (isset($this->_patterns[$match[2]])) {
								$compiled = str_replace($match[0], $this->_patterns[$match[2]], $compiled);
							} else {
								throw new Exception(sprintf('Pattern %s does not exist for route %s', $match[2], $this->_path));
							}
						break;
					}

					$this->_tokens[] = $match[2];
				}
			} else {
				$this->_static = true;
			}
		}

		// Append a wildcard to the end incase of parameters or arguments
		$compiled .= self::WILDCARD .'?';

		// Save the compiled regex
		$this->_compiled = '/^'. $compiled .'/i';

		return $this->_compiled;
	}

	/**
	 * Has the regex pattern been compiled?
	 *
	 * @access public
	 * @return bool
	 */
	public function isCompiled() {
		return ($this->_compiled !== null);
	}

	/**
	 * Attempt to match the object against a passed URL.
	 * If a match is found, extract pattern values and parameters.
	 *
	 * @acccess public
	 * @param string $url
	 * @return bool
	 */
	public function isMatch($url) {
		if ($this->_path == $url) {
			return true;
		}

		if (preg_match($this->compile(), $url, $matches)) {
			$this->_matches = $matches;
			$this->_url = array_shift($matches);

			// Get pattern values
			if (!empty($matches) && !empty($this->_tokens)) {
				$modules = Titon::app()->getModules();
				$controllers = Titon::app()->getControllers();

				foreach ($this->_tokens as $token) {
					switch ($token) {
						case 'module':
							// Is it a module? Check against the installed modules.
							if (in_array($matches[0], $modules)) {
								$this->_route['module'] = array_shift($matches);
							}
						break;
						case 'controller':
							// Is it a controller? Check within the modules controllers.
							if (in_array($matches[0], $controllers[$this->_route['module']])) {
								$this->_route['controller'] = array_shift($matches);
								
							// Doesn't match a controller or module, remove.
							} else {
								throw new Exception(sprintf('Controller %s was not found within the %s module', $matches[0], $this->_route['module']));
							}
						break;
						default:
							$this->_route[$token] = array_shift($matches);
						break;
					}
				}
			}

			// Detect query string and parameters
			if (!empty($matches[0]) && $matches[0] != '/') {
				$parts = explode('/', trim($matches[0], '/'));

				foreach ($parts as $index => $part) {
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
	 * @return bool
	 */
	public function isMethod() {
		if (!empty($this->_method) && !in_array($this->request->method(), $this->_method)) {
			return false;
		}

		return true;
	}

	/**
	 * Validates the route matches a secure connection.
	 *
	 * @access public
	 * @return bool
	 */
	public function isSecure() {
		if ($this->_secure && !$this->request->isSecure()) {
			return false;
		}

		return true;
	}

	/**
	 * Is the route static (no regex patterns)?
	 *
	 * @access public
	 * @return bool
	 */
	public function isStatic() {
		return $this->_static;
	}

	/**
	 * Validate the URL, it's supported HTTP method and secure connection.
	 *
	 * @acccess public
	 * @param string $url
	 * @return bool
	 */
	public function match($url) {
		return ($this->isMatch($url) && $this->isMethod() && $this->isSecure());
	}

	/**
	 * Grab a param from the route.
	 *
	 * @access public
	 * @param string $key
	 * @return string|null
	 */
	public function param($key = null) {
		return isset($this->_route[$key]) ? $this->_route[$key] : $this->_route;
	}

}
