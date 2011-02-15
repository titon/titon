<?php

namespace titon\source\library\routes;

use \titon\source\library\routes\RouteInterface;
use \titon\source\Titon;

abstract class RouteAbstract implements RouteInterface {

	/**
	 * Regex patterns.
	 */
	const PATTERN_ALNUM = '([a-z0-9\_\-\+]+)';
	const PATTERN_NUMERIC = '([0-9]+)';
	const PATTERN_WILDCARD = '(.*)';

	/**
	 * The path to match.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_path = '/';

	/**
	 * The default and parsed route values.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_route = array();

	/**
	 * List of patterns used within the URL.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_patterns = array();

	/**
	 * The compiled regex pattern.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_compiled = null;

	/**
	 * Is the route static (no regex patterns)?
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
	 * @param bool $static
	 * @return void
	 */
	public function __construct($path, array $route, $static = false) {
		$this->_path = $path;
		$this->_route = Titon::router()->defaults($route);
		$this->_static = $static;

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
		
		$compiled = str_replace('/', '\/', rtrim($this->_path, '/'));

		// Find any defined patterns if static is set to false: #, *, :
		if (!$this->isStatic()) {
			preg_match_all('/{([\:|\#|\*]{1})([a-z0-9]+)}/', $compiled, $matches, PREG_SET_ORDER);

			if (!empty($matches)) {
				foreach ($matches as $match) {
					switch ($match[1]) {
						case ':': $compiled = str_replace($match[0], self::PATTERN_ALNUM, $compiled); break;
						case '#': $compiled = str_replace($match[0], self::PATTERN_NUMERIC, $compiled); break;
						case '*': $compiled = str_replace($match[0], self::PATTERN_WILDCARD, $compiled); break;
					}

					$this->_patterns[] = $match[2];
				}
			} else {
				$this->_static = true;
			}
		}

		// Append a wildcard to the end incase of parameters or arguments
		$compiled .= self::PATTERN_WILDCARD;

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
	 * Attempt to match the class against a passed URL.
	 * If a match is found, extract pattern values and parameters.
	 *
	 * @acccess public
	 * @param string $url
	 * @return bool
	 */
	public function match($url) {
		if ($this->_path == $url) {
			return true;
		} else {
			$result = preg_match($this->compile(), $url, $matches);

			if ($result) {
				$route = array_shift($matches);
				
				// Get pattern values
				if (!empty($matches) && !empty($this->_patterns)) {
					foreach ($this->_patterns as $key) {
						$this->_route[$key] = array_shift($matches);
					}
				}

				// Detect arguments and parameters
				if (!empty($matches[0]) && $matches[0] != '/') {
					$parts = explode('/', $matches[0]);

					foreach ($parts as $index => $part) {
						if (strpos($part, ':') !== false) {
							list($key, $value) = explode(':', $part);
							$this->_route['query'][$key] = $value;

						} else {
							$this->_route['params'][] = $part;
						}
					}
				}

				return true;
			}
		}

		return false;
	}

}