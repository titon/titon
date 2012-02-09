<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\engines;

use \titon\Titon;
use \titon\base\Base;
use \titon\libs\engines\Engine;
use \titon\libs\engines\EngineException;
use \titon\libs\traits\Decorator;
use \titon\utility\Inflector;
use \Closure;

/**
 * The Engine acts as a base for all child Engines to inherit. The view engine acts as the renderer of data
 * (set by the controller) to markup (the view templates), using a templating system. 
 * The order of process is as follows:
 *
 *  - The engine inherits the configuration and variables that were set in the Controller
 *  - The engine applies the configuration and loads any defined helpers and classes
 *  - Once loaded, begins the staged rendering process
 *  - Will trigger any callbacks and shutdown
 *
 * @package	titon.libs.engines
 * @uses	titon\libs\engines\EngineException
 * @uses	titon\utility\Inflector
 * @abstract
 */
abstract class EngineAbstract extends Base implements Engine {
	use Decorator;

	/**
	 * Constants for all the possible types of templates.
	 */
	const TYPE_TPL = 1;
	const TYPE_LAYOUT = 2;
	const TYPE_WRAPPER = 3;
	const TYPE_INCLUDE = 4;
	const TYPE_ERROR = 5;

	/**
	 * Configuration. Can be overwritten in the Controller.
	 * 
	 *	type 		- The content type to respond as (defaults to html)
	 *	template 	- An array containing the module, controller, and action
	 *	render 		- Toggle the rendering process
	 *	layout 		- The layout template to use
	 *	wrapper 	- The wrapper template to use
	 *	error 		- True if its an error page
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'type'		=> null,
		'template'	=> array(),
		'render'	=> true,
		'layout'	=> 'default',
		'wrapper'	=> null,
		'error'		=> false
	);

	/**
	 * The rendered content used within the wrapper or the layout.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_content = null;

	/**
	 * Dynamic data set from the controller.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_data = array();

	/**
	 * List of added helpers.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_helpers = array();

	/**
	 * Has the view been rendered.
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_rendered = false;

	/**
	 * Add a helper to the view rendering engine.
	 * 
	 * @access public
	 * @param string $alias
	 * @param Closure $helper 
	 * @return void
	 */
	public function addHelper($alias, Closure $helper) {
		$this->_helpers[] = $alias;

		$this->attachObject(array(
			'alias' => $alias,
			'interface' => '\titon\libs\helpers\Helper'
		), $helper);
	}

	/**
	 * Get the filepath for a type of template: layout, wrapper, view, error, include
	 *
	 * @access public
	 * @param string $type
	 * @param string $path
	 * @return string
	 */
	public function buildPath($type = self::TYPE_TPL, $path = null) {
		$paths = array();
		$config = $this->config();
		$template = $config['template'];

		if ($config['error']) {
			$type = self::TYPE_ERROR;
		}

		switch ($type) {
			case self::TYPE_LAYOUT:
				if (!empty($config['layout'])) {
					$layout = Titon::loader()->ds($config['layout']);

					$paths = array(
						APP_MODULES . $template['module'] .'/views/private/layouts/'. $layout .'.tpl',
						APP_VIEWS .'layouts/'. $layout .'.tpl'
					);
				}
			break;

			case self::TYPE_WRAPPER:
				if (!empty($config['wrapper'])) {
					$wrapper = Titon::loader()->ds($config['wrapper']);

					$paths = array(
						APP_MODULES . $template['module'] .'/views/private/wrappers/'. $wrapper .'.tpl',
						APP_VIEWS .'wrappers/'. $wrapper .'.tpl'
					);
				}
			break;

			case self::TYPE_INCLUDE:
				$path = Titon::loader()->ds($path);

				if (substr($path, -4) == '.tpl') {
					$path = substr($path, 0, (strlen($path) - 4));
				}

				$paths = array(
					APP_MODULES . $template['module'] .'/views/private/includes/'. $path .'.tpl',
					APP_VIEWS .'includes/'. $path .'.tpl'
				);
			break;

			case self::TYPE_ERROR:
				$error = Titon::loader()->ds($template['action']);

				$paths = array(
					APP_MODULES . $template['module'] .'/views/private/errors/'. $error .'.tpl',
					APP_VIEWS .'errors/'. $error .'.tpl'
				);
			break;

			case self::TYPE_TPL:
			default:
				$parts = array(
					$template['module'],
					'views',
					'public',
					$template['controller'],
					Titon::loader()->ds($template['action'])
				);

				$path  = APP_MODULES . implode(DS, $parts);
				$path .= empty($ext) ? '.tpl' : '.'. $ext .'.tpl';

				$paths = array($path);
			break;
		}

		if (!empty($paths)) {
			foreach ($paths as $path) {
				if (file_exists($path)) {
					return $path;
				}
			}
		}

		return false;
	}

	/**
	 * The output of the rendering process. The output changes depending on the current rendering stage.
	 *
	 * @access public
	 * @return void
	 */
	public function content() {
		return $this->_content;
	}

	/**
	 * Return the data based on the given key.
	 * 
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function data($key = null) {
		return isset($this->_data[$key]) ? $this->_data[$key] : $this->_data;
	}

	/**
	 * Opens and renders a partial view element within the current document.
	 *
	 * @access public
	 * @param string $path
	 * @param array $variables
	 * @return string
	 * @throws EngineException
	 */
	public function open($path, array $variables = array()) {
		throw new EngineException('You must define the open() method within your engine.');
	}

	/**
	 * Triggered before a template is rendered by the engine.
	 *
	 * @access public
	 * @return void
	 */
	public function preRender() {
		$this->triggerObjects('preRender');
	}

	/**
	 * Triggered after a template is rendered by the engine.
	 *
	 * @access public
	 * @return void
	 */
	public function postRender() {
		$this->triggerObjects('postRender');
	}

	/**
	 * Primary method to render a single view template.
	 *
	 * @access public
	 * @param string $path
	 * @param array $variables
	 * @return void
	 * @throws EngineException
	 */
	public function render($path, array $variables = array()) {
		throw new EngineException('You must define the render() method within your Engine.');
	}

	/**
	 * Begins the staged rendering process. First stage, the system must render the template based on the module, 
	 * controller and action path. Second stage, wrap the first template in any wrappers. Third stage, 
	 * wrap the current template ouput with the layout. Return the final result.
	 *
	 * @access public
	 * @return string
	 * @throws EngineException
	 */
	public function run() {
		throw new EngineException('You must define the run() method within your Engine.');
	}

	/**
	 * Set a variable to the view. The variable name will be inflected if it is invalid.
	 *
	 * @access public
	 * @param string|array $key
	 * @param mixed $value
	 * @return Engine
	 * @chainable
	 */
	public function set($key, $value = null) {
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->set($k, $v);
			}
		} else {
			$this->_data[Inflector::variable($key)] = $value;
		}

		return $this;
	}

	/**
	 * Custom method to overwrite and configure the view engine manually.
	 *
	 * @access public
	 * @param mixed $options
	 * @return void
	 */
	public function setup($options) {
		if ($options === false || $options === null) {
			$this->configure('render', false);

		} else if (is_string($options)) {
			$this->configure('template.action', $options);

		} else if (is_array($options)) {
			foreach ($options as $key => $value) {
				if ($key == 'template') {
					if (is_array($value)) {
						$this->configure('template', $value + $this->config('template'));
					} else {
						$this->configure('template.action', $value);
					}
				} else {
					$this->configure($key, $value);
				}
			}
		}
	}

}
