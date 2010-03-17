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

namespace titon\router;

/**
 * Routing Class
 *
 * @package		Titon
 * @subpackage	Titon.Router
 */
class Router {

    /**
     * The base directory if the app is placed outside of the root.
     *
     * @access private
     * @var string
     * @static
     */
    private static $__baseUrl;

    /**
     * Configuration settings for the routing logic.
     *
     * @access private
     * @var array
     * @static
     */
    private static $__defaultConfig = array(
        'container' => '',
        'controller' => 'pages',
        'action' => 'index'
    );

    /**
     * The current route broken up into its arrayed parts.
     *
     * @access private
     * @var array
     * @static
     */
    private static $__current = array();

    /**
     * An array of all paths that have been parsed into segments. Used for fast lookups.
     *
     * @access private
     * @var array
     * @static
     */
    private static $__mappedPaths = array();

	/**
	 * Manually defined aesthetic routes that re-route to an internal controller and action.
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
     * Primarily used to permanently define a routes (array format) destination as a re-useable string.
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
	 * Add a custom defined route that matches to an internal destination.
	 *
	 * @access public
	 * @param string $dest
	 * @param string|array $route
	 * @return void
	 * @static
	 */
	public static function addRoute($dest, $route = array()) {
        // @todo
	}

    /**
     * Add a slug to the routing system. A slug is a string that is used as a lookup for a route (array).
     *
     * @access public
     * @param string $slug
     * @param string|array $route
     * @return void
     * @static
     */
    public static function addSlug($slug, $route = array()) {
        if (is_array($route)) {
            $route = self::mapDefaults($route);
        }

        self::$__slugs[(string)$slug] = $route;
    }

	/**
	 * Return the base URL if the app was not placed in the root directory.
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function baseUrl() {
        return self::$__baseUrl;
	}

    /**
	 * Construct an array route into a string URL path. Injects arguments and query parameters.
	 *
	 * @access public
	 * @param array $route
	 * @return string
	 * @static
	 */
	public static function construct($route) {
		if (!is_array($route)) {
			return (string)$route;
		}

        $route = self::mapDefaults($route);
		$params = $args = array();
        $path = '/';

        if (!empty(self::$__baseUrl)) {
            $path = self::$__baseUrl . $path;
        }

		if ($route['container'] != self::$__defaultConfig['container']) {
			$path .= $route['container'] .'/';
		}

		$path .= $route['controller'] .'/'. $route['action'];
        $path .= (!empty($route['ext']) ? '.'. $route['ext'] .'/' : '/');

		unset($route['container'], $route['controller'], $route['action'], $route['ext']);

        // Store named params and query string
		foreach ($route as $key => $value) {
            if ($key === '#') {
                $fragment = $value;
            } else if ($key === 'query') {
                $query = $value;
            } else if (is_numeric($key)) {
                $args[] = $value;
            } else {
                $params[$key] = $value;
            }
		}

        // Append them now
		if (!empty($args)) {
			$path .= implode('/', $args) .'/';
		}

		if (!empty($params)) {
			foreach ($params as $key => $value) {
				$path .= str_replace(' ', '', $key) .':'. urlencode($value) .'/';
			}
		}

        if (isset($query)) {
            if (is_array($query)) {
                $query = '?'. http_build_query($query);
            } else {
                $query = (string)$query;
                if (substr($query, 0, 1) != '?') {
                    $query = '?'. $query;
                }
            }

            $path .= $query;
        }

        if (isset($fragment)) {
            $path .= '#'. $fragment;
        }

		return $path;
	}

	/**
	 * Return the current deconstructed route as an array of values.
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function current() {
        return self::$__current;
	}

    /**
	 * Deconstructs a string (a route found in the address bar) into an array that maps to the correct container, controller and action.
	 *
	 * @access public
	 * @param string $url
	 * @return array
	 * @static
	 */
    public static function deconstruct($url = '') {
        if (isset(self::$__mappedPaths[$url])) {
            return self::$__mappedPaths[$url];
        }

		$params = self::mapDefaults();
        $params['ext'] = null;
        $params['query'] = $params['params'] = $params['args'] = array();

        if (empty($url)) {
            $url = self::$__segments['route'];
        }

		if ($url === '/') {
			return $params;
		}

		$url = trim($url, '/');
        $parts = explode('/', $url);

        // Determine container and controller
        if (is_dir(CONTROLLERS . $parts[0])) {
            $params['container'] = str_replace('-', '_', $parts[0]);

			if (count($parts) == 1) {
				$params['controller'] = $params['container'];
                unset($parts[0]);
			} else {
				$params['controller'] = str_replace('-', '_', $parts[1]);
                unset($parts[1]);
			}
        } else {
			$params['controller'] = str_replace('-', '_', $parts[0]);
            unset($parts[0]);
            
            /*if (isset($parts[1]) && $parts[0] == $parts[1]) {
				$params['controller'] = str_replace('-', '_', $parts[1]);
                unset($parts[0], $parts[1]);
			} else {
				$params['controller'] = str_replace('-', '_', $parts[0]);
                unset($parts[0]);
			}*/
        }

		// Action and Parameters
		if (!empty($parts)) {
			$action = array_shift($parts);

			if (!is_numeric($action) && strpos($action, ':') === false) {
				if (strpos($action, '.') !== false) {
					list($action, $ext) = explode('.', $action);
					$params['ext'] = $ext;
				}
				$params['action'] = $action;
			} else {
				array_unshift($parts, $action);
			}

			foreach ($parts as $index => $part) {
				// Named params
                if (strpos($part, ':') !== false) {
					list($key, $value) = explode(':', $part);
					$params['params'][$key] = $value;

				// Arguments
				} else {
					$params['args'][] = $part;
				}

                unset($parts[$index]);
			}
		} else {
			$params['action'] = self::$__defaultConfig['action'];
		}

		// Get query string
		$params['query'] = self::$__segments['query'];

        self::$__mappedPaths[$url] = $params;
		return $params;
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
        } else if ($route = self::slug($url)) {
            return $route;
        }

        return $url;
    }

    /**
	 * Parses the current URL into multiple segments as well as parses the current route into an application path.
	 *
	 * @access public
	 * @return void
     * @static
	 */
	public static function initialize() {
		$protocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://');
		
        list($baseUrl, $route) = explode('index.php', $_SERVER['PHP_SELF']);

		if (empty($route)) {
			$route = '/';
		}

        self::$__segments = array(
			'protocol'  => $protocol,
			'host'      => $_SERVER['HTTP_HOST'],
            'base'      => $baseUrl,
			'route'     => $route,
            'query'     => $_GET
		);

        self::$__baseUrl = $baseUrl;
        self::$__current = self::deconstruct($route);
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
	public static function mapDefaults(array $data = array()) {
		$data = $data + array(
			'container' => '',
			'controller' => '',
			'action' => self::$__defaultConfig['action']
        );

        // Check after the merge, incase they set controller to null
		if (empty($data['controller'])) {
			$data['controller'] = self::$__defaultConfig['controller'];
		}

        return $data;
	}

    /**
	 * Return all the segments that were parsed for the current request.
     * Can be assembled into a string, or returned as an array.
	 *
	 * @access public
     * @param boolean $assembled
	 * @return string|array
	 * @static
	 */
	public static function segments($assembled = false) {
        if ($assembled === true) {
            $segments = self::$__segments;
            $segments['query'] = http_build_query($segments['query']);

			return implode('', $segments);
		}

        return self::$__segments;
	}

    /**
     * Returns a given slugs route if it has been defined.
     *
     * @access public
     * @param string $slug
     * @return string|array
     * @static
     */
    public static function slug($slug) {
        return (isset(self::$__slugs[$slug]) ? self::$__slugs[$slug] : null);
    }
	
}
