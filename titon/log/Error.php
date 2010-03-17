<?php
/**
 * A central hub for all fatal errors and their respective response pages.
 * When an error occurs, the system immediately calls this class to render an error page and stop page execution.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\log;

use \titon\core\App;
use \titon\core\Config;
use \titon\core\Registry;
use \titon\log\Benchmark;
use \titon\router\Router;
use \titon\utility\Inflector;

/**
 * Error Management Class
 *
 * @package		Titon
 * @subpackage	Titon.Log
 */
class Error {

	/**
	 * Disable the class to enforce static methods.
	 *
	 * @access private
	 * @return void
	 */
	private function __construct() { }

    /**
     * Error page for a missing controller action.
     *
     * @access public
     * @param array $params
     * @return void
     * @static
     */
    public static function action(array $params = array()) {
		if (Config::get('debug') == 0) {
            self::http(404);
            return;
		}

		$namespace = 'app\controllers';
		if (!empty($params['container'])) {
			$namespace .= NS . $params['container'];
		}

		$params['namespace'] = $namespace;
        $params['controller'] = Inflector::camelize($params['controller']);
        $params['pageTitle'] = Config::get('App.name') .' - Missing Action: '. $params['action'];
        $params['benchmarks'] = Benchmark::get();

        self::__render($params, 'action');
    }

    /**
     * Error page for a missing controller.
     *
     * @access public
     * @param array $params
     * @return void
     * @static
     */
	public static function controller(array $params = array()) {
        if (Config::get('debug') == 0) {
            self::http(404);
            return;
        }

        $params['class'] = Inflector::baseClass($params['namespace']);
		$params['namespace'] = Inflector::baseNamespace($params['namespace']);
        $params['pageTitle'] = Config::get('App.name') .' - Missing Controller: '. $params['controller'];
        $params['benchmarks'] = Benchmark::get();

        self::__render($params, 'controller');
	}

    /**
     * Basic fallback error page, called when debug is disabled.
     *
     * @access public
     * @param int $code
     * @param array $params
     * @return void
     * @static
     */
    public static function http($code = 404, array $params = array()) {
		switch ($code) {
			default:
			case 400: $pageTitle = '400 - Bad Request'; break;
			case 401: $pageTitle = '401 - Unauthorized'; break;
			case 403: $pageTitle = '403 - Forbidden'; break;
			case 404: $pageTitle = '404 - Not Found'; break;
			case 500: $pageTitle = '500 - Internal Server Error'; break;
		}
		
		$params['pageTitle'] = Config::get('App.name') .' - '. $pageTitle;
		$params['code'] = $code;
		$params['url'] = Router::segments(true);

		self::__render($params, $code);
    }

    /**
     * Error page for a missing layout template.
     *
     * @access public
     * @param array $params
     * @return void
     * @static
     */
	public static function layout(array $params = array()) {
        if (Config::get('debug') == 0) {
            self::http(404);
            return;
        }

        $params['pageTitle'] = Config::get('App.name') .' - Missing Layout: '. $params['layout'];
        $params['benchmarks'] = Benchmark::get();

        self::__render($params, 'layout');
	}

    /**
     * Error page for a missing database model.
     *
     * @access public
     * @param array $params
     * @return void
     * @static
     */
	public static function model(array $params = array()) {
        if (Config::get('debug') == 0) {
            self::http(404);
            return;
        }

        $params['pageTitle'] = Config::get('App.name') .' - Missing Model: '. $params['model'];
        $params['benchmarks'] = Benchmark::get();

        self::__render($params, 'model');
	}

    /**
     * Error page for a missing modules: helpers, extensions, etc.
     *
     * @access public
     * @param array $params
	 * @param string $type
     * @return void
     * @static
     */
	public static function module(array $params = array(), $type = null) {
        if (Config::get('debug') == 0) {
            self::http(404);
            return;
        }

		$type = Inflector::camelize($type);
        $architecture = App::architecture($type);

		// Params
        $params['type'] = $type;
		$params['path'] = Inflector::toPath($params['namespace']);
        $params['class'] = Inflector::baseClass($params['namespace']);
        $params['folder'] = strtolower(Inflector::pluralize($type));
		$params['parent'] = $architecture['parent'];
        $params['interface'] = $architecture['interface'];
		$params['namespace'] = Inflector::baseNamespace($params['namespace']);
        $params['pageTitle'] = Config::get('App.name') .' - Missing Module: '. $params['class'] .' '. $type;
        $params['benchmarks'] = Benchmark::get();

        self::__render($params, 'module');
	}

    /**
     * Error page for a missing view template.
     *
     * @access public
     * @param array $params
     * @return void
     * @static
     */
	public static function view(array $params = array()) {
        if (Config::get('debug') == 0) {
            self::http(404);
            return;
		}

        $view = $params['action'];
        if (!empty($params['ext'])) {
            $view .= '.'. $params['ext'];
        }
        $view .= '.tpl';

        $params['view'] = $view;
        $params['controller'] = Inflector::camelize($params['controller']);
        $params['pageTitle'] = Config::get('App.name') .' - Missing View: '. $params['action'];
        $params['benchmarks'] = Benchmark::get();

        self::__render($params, 'view');
	}

    /**
     * Error page for a missing view wrapper.
     *
     * @access public
     * @param array $params
     * @return void
     * @static
     */
	public static function wrapper(array $params = array()) {
        if (Config::get('debug') == 0) {
            self::http(404);
            return;
        }

        $params['pageTitle'] = Config::get('App.name') .' - Missing Wrapper: '. $params['wrapper'];
        $params['benchmarks'] = Benchmark::get();

        self::__render($params, 'wrapper');
	}

    /**
     * Load the standard view engine to render the error views.
     *
     * @access private
     * @param array $data
     * @param string $tpl
     * @return void
     * @static
     */
    private static function __render($data = array(), $tpl = 'http') {
        $View = Registry::factory(
            array('module' => 'Engine', 'class' => null),
            array(
                'type'		=> 'html',
                'data'		=> $data,
                'template'	=> $tpl,
                'render'	=> true,
                'layout'	=> 'error',
                'wrapper'	=> null,
                'error'     => true
            )
        );

        echo $View->render();
        exit;
    }
	
}
