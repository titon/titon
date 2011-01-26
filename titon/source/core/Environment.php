<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

use titon\source\utility\Inflector;

/**
 * A hub that allows you to store different environment configurations, which can be detected and initialized on runtime.
 *
 * @package	titon.source.core
 * @uses	titon\source\utility\Inflector
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
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$path = APP_CONFIG .'environments'. DS . Inflector::filename($this->current());

		if (file_exists($path)) {
			include_once $path;
		}
	}

	/**
	 * Return the current environment name, based on hostname.
	 *
	 * @access public
	 * @return string
	 */
	public function current() {
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
		if (in_array($name, $this->__environments)) {
			$this->__default = $name;
		}
	}

	/**
	 * Add an environment and its hosts to the application.
	 *
	 * @access public
	 * @param string $name
	 * @param array $hosts
	 * @return this
	 * @chainable
	 */
	public function setup($name, array $hosts) {
		$this->__environments[] = $name;

		foreach ($hosts as $host) {
			$this->__hostMapping[$host] = $name;
		}

		return $this;
	}

}
