<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\system;

use \titon\Titon;
use \titon\base\Prototype;
use \titon\system\SystemException;
use \titon\utility\Inflector;

/**
 * @todo
 *
 * @package	titon.system
 */
class View extends Prototype {

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
	 *	type - The content type to respond as (defaults to html).
	 *	template - An array containing the module, controller, and action.
	 *	render - Toggle the rendering process.
	 *	layout - The layout template to use.
	 *	wrapper - The wrapper template to use.
	 *	error - True if its an error page.
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
	 * Dynamic data set from the controller.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_data = array();

	/**
	 * The rendering engine.
	 * 
	 * @access protected
	 * @var Engine
	 */
	protected $_engine;
	
	/**
	 * Add a helper to the rendering engine, through the view layer.
	 * 
	 * @access public
	 * @param string $alias
	 * @param Closure $helper 
	 * @return void
	 */
	public function addHelper($alias, Closure $helper) {
		if (!$this->_engine) {
			throw new SystemException('You must have an engine loaded to add helpers.');
		}
		
		$self->_engine->attachObject(array(
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
		$config = $this->config();
		$template = $config['template'];

		if ($config['error']) {
			$type = self::TYPE_ERROR;
		}

		switch ($type) {
			case self::TYPE_LAYOUT:
				$layout = Titon::loader()->ds($config['layout']);

				$paths = array(
					APP_MODULES . $template['module'] .'/views/private/layouts/'. $layout .'.tpl',
					APP_VIEWS .'layouts/'. $layout .'.tpl'
				);
			break;

			case self::TYPE_WRAPPER:
				$wrapper = Titon::loader()->ds($config['wrapper']);

				$paths = array(
					APP_MODULES . $template['module'] .'/views/private/wrappers/'. $wrapper .'.tpl',
					APP_VIEWS .'wrappers/'. $wrapper .'.tpl'
				);
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

		return $paths;
	}

	/**
	 * Check to see that the template file exists, else throw an error.
	 *
	 * @access public
	 * @param string $type
	 * @return bool
	 */
	public function checkPath($type = self::TYPE_TPL) {
		$paths = $this->buildPath($type);

		foreach ($paths as $path) {
			if (file_exists($path)) {
				return $path;
			}
		}

		return false;
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
	 * Attach the request and response objects.
	 * 
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('request', function() {
			return Titon::registry()->factory('titon\net\Request');
		});

		$this->attachObject('response', function() {
			return Titon::registry()->factory('titon\net\Response');
		});
	}

	/**
	 * Triggered before a template is rendered by the engine.
	 *
	 * @access public
	 * @return void
	 */
	public function preRender() {
		if ($this->_engine) {
			$this->_engine->triggerObjects('preRender');
		}
	}

	/**
	 * Triggered after a template is rendered by the engine.
	 *
	 * @access public
	 * @return void
	 */
	public function postRender() {
		if ($this->_engine) {
			$this->_engine->triggerObjects('postRender');
		}
	}

	/**
	 * Custom method to overwrite and configure the view engine manually.
	 *
	 * @access public
	 * @param mixed $options
	 * @return void
	 */
	public function render($options) {
		if (($options === false) || ($options === null)) {
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

	/**
	 * Render the template using the rendering engine, and output the result using the response.
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		$config = $this->config();

		if (!$config['render']) {
			return;
		}

		// Check for engine
		if (!$this->_engine) {
			throw new SystemException('You must have an engine loaded to render the page.');
		}

		// Get content type automatically
		if (is_array($config['template']) && isset($config['template']['ext'])) {
			$this->configure('type', $config['template']['ext']);

		} else if (empty($config['type'])) {
			$this->configure('type', 'html');
		}

		// Output the response!
		$this->response->contentType($this->config('type'));
		$this->response->contentBody($this->_engine->run());
		$this->response->respond();
	}

	/**
	 * Set a variable to the view. The variable name will be inflected if it is invalid.
	 *
	 * @access public
	 * @param string|array $key
	 * @param mixed $value
	 * @return View
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
	 * Set the rendering engine.
	 * 
	 * @access public
	 * @param Engine $engine
	 * @return View 
	 * @chainable
	 */
	public function setEngine(Engine $engine) {
		$this->_engine = $engine;
		$this->_engine->setView($this);

		return $this;
	}

}