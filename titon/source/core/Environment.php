<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

use \titon\source\utility\Inflector;
use \titon\source\log\Exception;

/**
 * A hub that allows you to store different environment configurations, which can be detected and initialized on runtime.
 *
 * @package	titon.source.core
 * @uses	titon\source\utility\Inflector
 * @uses	titon\source\log\Exception
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
	 * Return the current environment name, based on hostname.
	 *
	 * @access public
	 * @return string
	 */
	public function current() {
		$host = $_SERVER['HTTP_HOST'];

		if (isset($this->__hostMapping[$host])) {
			return $this->__hostMapping[$host];
		}

		return $this->__default;
	}

	/**
	 * Set the default environment, must exist in the $__environments array.
	 *
	 * @access public
	 * @param string $name
	 * @return this
	 * @chainable
	 */
	public function fallback($name) {
		if (!in_array($name, $this->__environments)) {
			throw new Exception(sprintf('Environment %s does not exist.', $name));
		}

		$this->__default = $name;

		return $this;
	}

	/**
	 * Initialize the environment by including the configuration.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$path = APP_CONFIG .'environments'. DS . Inflector::filename($this->current());

		if (is_file($path)) {
			include_once $path;
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
