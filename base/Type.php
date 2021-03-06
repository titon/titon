<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base;

use \Serializable;

/**
 * Acts as the base for all generic types.
 *
 * @package	titon.base
 */
class Type implements Serializable {

	/**
	 * Raw unchanged value.
	 *
	 * @access protected
	 * @var mixed
	 */
	protected $_raw;

	/**
	 * Modified value.
	 *
	 * @access protected
	 * @var mixed
	 */
	protected $_value;

	/**
	 * Save the value.
	 *
	 * @access public
	 * @param mixed $value
	 */
	public function __construct($value) {
		$this->_value = $value;
		$this->_raw = $value;
	}

	/**
	 * Define magic to string.
	 *
	 * @access public
	 * @return mixed
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * Does the current value match the raw value?
	 *
	 * @access public
	 * @return boolean
	 */
	public function isRaw() {
		return ($this->_value === $this->_raw);
	}

	/**
	 * Return the value instead of serializing it.
	 *
	 * @access public
	 * @return mixed
	 */
	public function serialize() {
		return serialize($this->_value);
	}

	/**
	 * Return the raw value.
	 *
	 * @access public
	 * @return mixed
	 */
	public function raw() {
		return $this->_raw;
	}

	/**
	 * Return a new instance based on the raw value.
	 *
	 * @access public
	 * @return \titon\base\Type
	 */
	public function rawOf() {
		return new self($this->_raw);
	}

	/**
	 * Define basic to string.
	 *
	 * @access public
	 * @return mixed
	 */
	public function toString() {
		return (string) $this->_value;
	}

	/**
	 * Set the value after unserialization.
	 *
	 * @access public
	 * @param mixed $value
	 * @return void
	 */
	public function unserialize($value) {
		$this->_value = unserialize($value);
	}

	/**
	 * Return the current modified value.
	 *
	 * @access public
	 * @return mixed
	 */
	public function value() {
		return $this->_value;
	}

	/**
	 * Return a new instance based on the current value.
	 *
	 * @access public
	 * @return \titon\base\Type
	 */
	public function valueOf() {
		return new self($this->_value);
	}

}