<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\core;

use titon\core\CoreException;
use titon\utility\Inflector;
use titon\utility\Hash;

/**
 * A hub that allows you to store different environment configurations, which can be detected and initialized on runtime.
 *
 * @package	titon.core
 */
class Environment {

	/**
	 * Types of environments.
	 */
	const DEVELOPMENT = 1;
	const STAGING = 2;
	const PRODUCTION = 3;

	/**
	 * Currently active environment.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_current = null;

	/**
	 * Holds the list of possible environment configurations.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_environments = [
		'dev' => [
			'name' => 'dev',
			'type' => self::DEVELOPMENT,
			'hosts' => ['localhost', '127.0.0.1', '::1']
		]
	];

	/**
	 * Sets the fallback environment; defaults to development.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_fallback = 'dev';

	/**
	 * Relate hostnames to environment configurations.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_hostMapping = [
		'localhost' => 'dev',
		'127.0.0.1' => 'dev',
		'::1' => 'dev'
	];

	/**
	 * Return the current environment config, or a certain value.
	 *
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function current($key = null) {
		return Hash::get($this->_environments[$this->_current], $key);
	}

	/**
	 * Set the fallback environment; fallback must exist in the $_environments array.
	 *
	 * @access public
	 * @param string $key
	 * @return \titon\core\Environment
	 * @throws \titon\core\CoreException
	 * @chainable
	 */
	public function fallbackAs($key) {
		if (empty($this->_environments[$key])) {
			throw new CoreException(sprintf('Environment %s does not exist.', $key));
		}

		$this->_fallback = $key;

		return $this;
	}

	/**
	 * Initialize the environment by including the configuration.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		foreach ([$_SERVER['HTTP_HOST'], $_SERVER['SERVER_ADDR']] as $host) {
			if (isset($this->_hostMapping[$host])) {
				$this->_current = $this->_hostMapping[$host];
			}
		}

		if (!$this->_current) {
			$this->_current = $this->_fallback;
		}

		$path = APP_CONFIG . 'environments/' . Inflector::fileName($this->_current, 'php', false);

		if (file_exists($path)) {
			include $path;
		}
	}

	/**
	 * Does the current environment match the passed key?
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function is($key) {
		return ($this->current('name') === $key);
	}

	/**
	 * Is the current environment development?
	 *
	 * @access public
	 * @return boolean
	 */
	public function isDevelopment() {
		return ($this->current('type') === self::DEVELOPMENT);
	}

	/**
	 * Is the current environment production?
	 *
	 * @access public
	 * @return boolean
	 */
	public function isProduction() {
		return ($this->current('type') === self::PRODUCTION);
	}

	/**
	 * Is the current environment staging?
	 *
	 * @access public
	 * @return boolean
	 */
	public function isStaging() {
		return ($this->current('type') === self::STAGING);
	}

	/**
	 * Add an environment and its configured hosts and type.
	 *
	 * @access public
	 * @param string $key
	 * @param int $type
	 * @param array $hosts
	 * @return \titon\core\Environment
	 * @throws \titon\core\CoreException
	 * @chainable
	 */
	public function setup($key, $type, array $hosts) {
		if (empty($hosts)) {
			throw new CoreException(sprintf('A host mapping is required for the %s environment.', $key));
		}

		if ($type !== self::DEVELOPMENT && $type !== self::PRODUCTION && $type !== self::STAGING) {
			throw new CoreException(sprintf('Invalid environment type detected for %s.', $key));
		}

		$this->_environments[$key] = [
			'name' => $key,
			'type' => $type,
			'hosts' => $hosts
		];

		foreach ($hosts as $host) {
			$this->_hostMapping[$host] = $key;
		}

		return $this;
	}

}
