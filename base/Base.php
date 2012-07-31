<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base;

use titon\Titon;
use titon\base\BaseException;
use titon\libs\augments\ConfigAugment;
use titon\libs\augments\InfoAugment;
use \Serializable;

/**
 * Primary class for all framework classes to extend. All child classes will inherit the $_config property,
 * allowing any configuration settings to be automatically passed and set through the constructor.
 *
 * @package	titon.base
 */
class Base implements Serializable {

	/**
	 * The configuration object.
	 *
	 * @access public
	 * @var titon\libs\augments\ConfigAugment
	 */
	public $config;

	/**
	 * The information object.
	 *
	 * @access public
	 * @var titon\libs\augments\InfoAugment
	 */
	public $info;

	/**
	 * An array of configuration settings for the current class.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [];

	/**
	 * Merges the custom configuration with the defaults.
	 * Trigger initialize method if setting is true.
	 *
	 * @access public
	 * @param array $config
	 */
	public function __construct(array $config = []) {
		$this->config = new ConfigAugment($config, $this->_config + ['initialize' => true]);
		$this->info = new InfoAugment($this);

		if ($this->config->initialize) {
			$this->initialize();
		}
	}

	/**
	 * Magic method for toString().
	 *
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * Serialize the configuration.
	 *
	 * @access public
	 * @return string
	 */
	public function serialize() {
		return serialize($this->config->get());
	}

	/**
	 * Reconstruct the class once unserialized.
	 *
	 * @access public
	 * @param array $data
	 * @return mixed|void
	 */
	public function unserialize($data) {
		$this->__construct(unserialize($data));
	}

	/**
	 * Primary initialize method that is triggered during instantiation.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		return;
	}

	/**
	 * A dummy function for no operation.
	 *
	 * @access public
	 * @return void
	 */
	public function noop() {
		return;
	}

	/**
	 * Return the class name when called as a string.
	 *
	 * @access public
	 * @return string
	 */
	public function toString() {
		return $this->info->className;
	}

}