<?php

namespace titon\source\library\routes;

use \titon\source\library\routes\RouteInterface;
use \titon\source\Titon;
use \titon\source\log\Exception;

abstract class RouteAbstract implements RouteInterface {

	/**
	 * Pre-defined regex patterns.
	 */
	const PATTERN_ALPHABETIC = '([a-z\_\-\+]+)';
	const PATTERN_NUMERIC = '([0-9]+)';
	const PATTERN_WILDCARD = '(.*)';

	/**
	 * The compiled regex pattern.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_compiled = null;

	/**
	 * Custom parameters parsed out from defined regex patterns.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_params = array();

	/**
	 * The path to match.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_path = '/';

	/**
	 * List of custom defined regex patterns.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_patterns = array();

	/**
	 * The parsed route values.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_route = array();

	/**
	 * A static route contains no regex patterns.
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_static = false;

	/**
	 * Store the routing configuration.
	 *
	 * @access public
	 * @param string $path
	 * @param array $route
	 * @param array $patterns
	 * @return void
	 */
	public function __construct($path, array $route = array(), array $patterns = array()) {
		$this->_path = $path;

		// Set default routing paths
		$this->_route = Titon::router()->defaults($route);

		// Store patterns
		$this->_patterns = $this->_patterns + $patterns;

		// Compile when class is built
		$this->compile();
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
							$compiled = str_replace($match[0], self::PATTERN_ALPHABETIC, $compiled);
						break;
					
						case ($match[1] == '[' && $match[3] == ']'):
							$compiled = str_replace($match[0], self::PATTERN_NUMERIC, $compiled);
						break;

						case ($match[1] == '(' && $match[3] == ')'):
							$compiled = str_replace($match[0], self::PATTERN_WILDCARD, $compiled);
						break;

						case ($match[1] == '<' && $match[3] == '>'):
							if (isset($this->_patterns[$match[2]])) {
								$compiled = str_replace($match[0], $this->_patterns[$match[2]], $compiled);
							} else {
								throw new Exception(sprintf('Pattern %s does not exist for route %s', $match[2], $this->_path));
							}
						break;
					}

					$this->_params[] = $match[2];
				}
			} else {
				$this->_static = true;
			}
		}

		// Append a wildcard to the end incase of parameters or arguments
		$compiled .= '\/?'; //self::PATTERN_WILDCARD;

		// Save the compiled regex
		$this->_compiled = '/^'. $compiled .'$/i';

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
	 * Is the route static (no regex patterns)?
	 *
	 * @access public
	 * @return bool
	 */
	public function isStatic() {
		return $this->_static;
	}

	/**
	 * Attempt to match the object against a passed URL.
	 * If a match is found, extract pattern values and parameters.
	 *
	 * @acccess public
	 * @param string $url
	 * @return bool
	 */
	public function match($url) {
		if ($this->_path == $url) {
			return true;
		}

		if (preg_match($this->compile(), $url, $matches)) {
			$modules = Titon::app()->getModules();
			$controllers = Titon::app()->getControllers();
			$url = array_shift($matches);

			// Get pattern values
			if (!empty($matches) && !empty($this->_params)) {
				foreach ($this->_params as $key) {
					switch ($key) {
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

							// If not a controller, maybe it's actually a module?
							} else if (in_array($matches[0], $modules)) {
								$this->_route['module'] = array_shift($matches);

								if (!empty($matches)) {
									$this->_route['controller'] = array_shift($matches);
								}
							}
						break;
						default:
							$this->_route[$key] = array_shift($matches);
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

}