<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use \titon\core\CoreException;
use \titon\utility\Inflector;

/**
 * A hub that allows you to store different environment configurations, which can be detected and initialized on runtime.
 *
 * @package	titon.core
 * @uses	titon\core\CoreException
 * @uses	titon\utility\Inflector
 */
class Environment {

	/**
	 * Sets the default environment; defaults to development.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_default = 'development';

	/**
	 * Holds the list of possible environment configurations.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_environments = array();

	/**
	 * Relate hostnames to environment configurations.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_hostMapping = array(
		'localhost' => 'development',
		'127.0.0.1' => 'development',
		'::1' => 'development'
	);

	/**
	 * Return the current environment name, based on hostname.
	 *
	 * @access public
	 * @return string
	 */
	public function current() {
		$host = $_SERVER['HTTP_HOST'];

		if (isset($this->_hostMapping[$host])) {
			return $this->_hostMapping[$host];
		}

		return $this->_default;
	}

	/**
	 * Set the default environment, must exist in the $__environments array.
	 *
	 * @access public
	 * @param string $name
	 * @return Environment
	 * @chainable
	 */
	public function fallback($name) {
		if (!in_array($name, $this->_environments)) {
			throw new CoreException(sprintf('Environment %s does not exist.', $name));
		}

		$this->_default = $name;

		return $this;
	}

	/**
	 * Initialize the environment by including the configuration.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$path = APP_CONFIG .'environments'. DS . Inflector::filename($this->current(), 'php', false);

		if (file_exists($path)) {
			include_once $path;
		}
	}

	/**
	 * Add an environment and its hosts to the application.
	 *
	 * @access public
	 * @param string $name
	 * @param array $hosts
	 * @return Environment
	 * @chainable
	 */
	public function setup($name, array $hosts) {
		if (!in_array($name, $this->_environments)) {
			$this->_environments[] = $name;
		}

		foreach ($hosts as $host) {
			$this->_hostMapping[$host] = $name;
		}

		return $this;
	}

}
