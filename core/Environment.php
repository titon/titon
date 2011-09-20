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
	 * Types of environments.
	 */
	const DEVELOPMENT = 1;
	const STAGING = 2;
	const PRODUCTION = 3;

	/**
	 * Holds the list of possible environment configurations.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_environments = array();

	/**
	 * Sets the default environment; defaults to development.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_fallback = 'development';

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

		return $this->_fallback;
	}

	/**
	 * Set the fallback (default) environment, must exist in the $_environments array.
	 *
	 * @access public
	 * @param string $name
	 * @return Environment
	 * @throws CoreException
	 * @chainable
	 */
	public function fallback($name) {
		if (empty($this->_environments[$name])) {
			throw new CoreException(sprintf('Environment %s does not exist.', $name));
		}

		$this->_fallback = $name;

		return $this;
	}

	/**
	 * Initialize the environment by including the configuration.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$path = APP_CONFIG . 'environments' . DS . Inflector::filename($this->current(), 'php', false);

		if (file_exists($path)) {
			include_once $path;
		}
	}
	
	/**
	 * Does the current environment match the passed key?
	 * 
	 * @access public
	 * @param string $name
	 * @return boolean
	 */
	public function is($name) {
		$current = $this->current();
		
		return ($current['name'] == $name);
	}
	
	/**
	 * Is the current environment development?
	 * 
	 * @access public
	 * @return boolean
	 */
	public function isDevelopment() {
		$current = $this->current();
		
		return ($current['type'] == self::DEVELOPMENT);
	}
	
	/**
	 * Is the current environment production?
	 * 
	 * @access public
	 * @return boolean
	 */
	public function isProduction() {
		$current = $this->current();
		
		return ($current['type'] == self::PRODUCTION);
	}
	
	/**
	 * Is the current environment staging?
	 * 
	 * @access public
	 * @return boolean
	 */
	public function isStaging() {
		$current = $this->current();
		
		return ($current['type'] == self::STAGING);
	}

	/**
	 * Add an environment and its configured hosts and type.
	 *
	 * @access public
	 * @param string $name
	 * @param int $type
	 * @param array $hosts
	 * @return Environment
	 * @throws CoreException
	 * @chainable
	 */
	public function setup($name, $type, array $hosts) {
		if (empty($hosts)) {
			throw new CoreException(sprintf('A host mapping is required for the %s environment.', $name));
		}
		
		if ($type != self::DEVELOPMENT && $type != self::PRODUCTION && $type != self::STAGING) {
			throw new CoreException(sprintf('Invalid environment type detected for %s.', $name));
		}
		
		$this->_environments[$name] = array(
			'name' => $name,
			'type' => $type,
			'hosts' => $hosts
		);

		foreach ($hosts as $host) {
			$this->_hostMapping[$host] = $name;
		}

		return $this;
	}

}
