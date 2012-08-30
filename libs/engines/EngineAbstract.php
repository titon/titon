<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\engines;

use titon\Titon;
use titon\base\Base;
use titon\libs\engines\Engine;
use titon\libs\engines\EngineException;
use titon\libs\traits\Attachable;
use titon\libs\traits\Cacheable;
use titon\utility\Inflector;
use titon\utility\Hash;
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
 * @abstract
 */
abstract class EngineAbstract extends Base implements Engine {
	use Cacheable, Attachable;

	/**
	 * Constants for all the possible types of templates.
	 */
	const VIEW = 1;
	const LAYOUT = 2;
	const WRAPPER = 3;
	const ELEMENT = 4;

	/**
	 * Configuration. Can be overwritten in the Controller.
	 *
	 *	type 		- The content type to respond as (defaults to html)
	 *	template 	- An array containing the module, controller, and action
	 *	render 		- Toggle the rendering process
	 *	layout 		- The layout template to use
	 *	wrapper 	- The wrapper template to use
	 *	folder 		- The folder name to user when templates are overridden (emails, errors, etc)
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'type'		=> 'html',
		'template'	=> [
			'module' => null,
			'controller' => null,
			'action' => null,
			'ext' => null
		],
		'render'	=> true,
		'layout'	=> 'default',
		'wrapper'	=> null,
		'folder'	=> null
	];

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
	protected $_data = [];

	/**
	 * List of added helpers.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_helpers = [];

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
	 * @final
	 */
	final public function addHelper($alias, Closure $helper) {
		$this->_helpers[] = $alias;

		$this->attachObject([
			'alias' => $alias,
			'interface' => 'titon\libs\helpers\Helper'
		], $helper);
	}

	/**
	 * Get the file path for a type of template: layout, wrapper, view, error, include
	 *
	 * @access public
	 * @param int $type
	 * @param string|null $path
	 * @return string|null
	 * @throws \titon\libs\engines\EngineException
	 */
	public function buildPath($type = self::VIEW, $path = null) {
		$paths = [];
		$config = $this->config->get();
		$template = $config['template'];
		$folder = null;
		$view = null;

		switch ($type) {
			case self::LAYOUT:
				if ($config['layout']) {
					$view = $this->_preparePath($config['layout']);
					$folder = 'layouts';
				}
			break;

			case self::WRAPPER:
				if ($config['wrapper']) {
					$view = $this->_preparePath($config['wrapper']);
					$folder = 'wrappers';
				}
			break;

			case self::ELEMENT:
				$view = $this->_preparePath($path);
				$folder = 'includes';
			break;

			case self::VIEW:
			default:
				// If overridden use the folder path
				if ($config['folder']) {
					$view = $this->_preparePath($template['action']);
					$folder = $config['folder'];

				// Else determine full path based off module, controller, action
				} else {
					$view = $this->_preparePath($template['action']);
				}

				if ($template['ext']) {
					$view .= '.' . $template['ext'];
				}
			break;
		}

		// Build array of paths
		if ($folder) {
			if ($template['module']) {
				$paths[] = APP_MODULES . sprintf('%s/views/private/%s/%s.tpl', $template['module'], $folder, $view);
			}

			$paths[] = APP_VIEWS . sprintf('%s/%s.tpl', $folder, $view);

		} else {
			$paths[] = APP_MODULES . sprintf('%s/views/public/%s/%s.tpl', $template['module'], $template['controller'], $view);
		}

		if ($paths) {
			foreach ($paths as $path) {
				if (file_exists($path)) {
					return $path;
				}
			}
		} else {
			return null;
		}

		throw new EngineException(sprintf('View template %s does not exist.', str_replace([APP_VIEWS, APP_MODULES], '', $paths[0])));
	}

	/**
	 * The output of the rendering process. The output changes depending on the current rendering stage.
	 *
	 * @access public
	 * @return string
	 */
	public function content() {
		return $this->_content;
	}

	/**
	 * Return the data based on the given key, or return all data.
	 *
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function get($key = null) {
		return Hash::get($this->_data, $key);
	}

	/**
	 * Return the aliased helper names.
	 *
	 * @access public
	 * @return array
	 */
	public function getHelpers() {
		return $this->_helpers;
	}

	/**
	 * Override the template locations by providing a folder and view name.
	 *
	 * @access public
	 * @param string $folder
	 * @param string $view
	 * @param string $layout
	 * @return \titon\libs\engines\Engine
	 * @chainable
	 */
	public function override($folder, $view, $layout = null) {
		return $this->setup([
			'folder' => $folder,
			'template' => $view,
			'layout' => $layout
		]);
	}

	/**
	 * Triggered before a template is rendered by the engine.
	 *
	 * @access public
	 * @return void
	 */
	public function preRender() {
		$this->notifyObjects('preRender');
	}

	/**
	 * Triggered after a template is rendered by the engine.
	 *
	 * @access public
	 * @return void
	 */
	public function postRender() {
		$this->notifyObjects('postRender');
	}

	/**
	 * Set a variable to the view. The variable name will be inflected if it is invalid.
	 *
	 * @access public
	 * @param string|array $key
	 * @param mixed $value
	 * @return \titon\libs\engines\Engine
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
	 * @return \titon\libs\engines\Engine
	 * @chainable
	 */
	public function setup($options) {
		if ($options === false || $options === true || $options === null) {
			$this->config->render = (bool) $options;

		} else if (is_string($options)) {
			$this->config->set('template.action', $options);

		} else if (is_array($options)) {
			foreach ($options as $key => $value) {
				if ($key === 'template') {
					if (is_array($value)) {
						$this->config->template = $value + $this->config->template;
					} else {
						$this->config->set('template.action', $value);
					}
				} else {
					$this->config->set($key, $value);
				}
			}
		}

		return $this;
	}

	/**
	 * Prepare a path by converting slashes and removing .tpl.
	 *
	 * @access protected
	 * @param $path
	 * @return string
	 */
	protected function _preparePath($path) {
		return $this->cache([__METHOD__, $path], function() use ($path) {
			$path = Titon::loader()->ds($path);

			if (mb_substr($path, -4) === '.tpl') {
				$path = mb_substr($path, 0, (mb_strlen($path) - 4));
			}

			return $path;
		});
	}

}
