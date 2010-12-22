<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

/**
 * A hub that allows you to store different environment configurations, which can be detected and initialized on runtime.
 *
 * @package		Titon
 * @subpackage	Core
 */
class Environment {

	/**
	 * Sets the default environment; defaults to development.
	 *
	 * @access private
	 * @var string
	 */
	private $__default = 'development';

	/**
	 * Holds the list of possible environment configurations.
	 *
	 * @access private
	 * @var array
	 */
	private $__environments = array();

	/**
	 * Relate hostnames to environment configurations.
	 *
	 * @access private
	 * @var array
	 */
	private $__hostMapping = array(
		'localhost' => 'development',
		'127.0.0.1' => 'development'
	);

	/**
	 * Initialize the environment by applying the configuration.
	 * Load the environment variables into the class; strtolower() all keys first.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$config = $this->__environments[$this->detect()];

		foreach ($config as $key => $value) {
			$app->config->set($key, $value);
		}

		$path = CONFIG .'environments'. DS . Inflector::filename($setup);

		if (file_exists($path)) {
			include $path;
		}
	}

	/**
	 * Return the current environment name, based on hostname.
	 *
	 * @access public
	 * @return string
	 */
	public function detect() {
		return $this->__hostMapping[$_SERVER['HTTP_HOST']] ?: $this->getDefault();
	}

	/**
	 * Get the default environment.
	 *
	 * @access public
	 * @return string
	 */
	public function getDefault() {
		return $this->__default;
	}

	/**
	 * Set the default environment, must exist in the $__environments array.
	 *
	 * @access public
	 * @param string $name
	 * @return void
	 */
	public function setDefault($name) {
		if (isset($this->__environments[$name])) {
			$this->__default = $name;
		}
	}

	/**
	 * Add an environment and its settings to the application.
	 *
	 * @access public
	 * @param string $name
	 * @param array $options
	 * @return void
	 */
	public function setup($name, array $options = array()) {
		if (isset($this->__environments[$name])) {
			$this->__environments[$name] = $options + $this->__environments[$name];
		} else {
			$this->__environments[$name] = $options;
		}

		if (!empty($this->__environments[$name]['hosts'])) {
			foreach ($this->__environments[$name]['hosts'] as $host) {
				$this->__hostMapping[$host] = $name;
			}
		}
	}

}
