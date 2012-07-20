<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base;

use titon\base\BaseException;

/**
 * The Enum type provides a basic interface to mimic enum based classes. Enums are first defined using class constants,
 * with their values denoted as integers, and finally added as an argument to the $_enums property. This property also
 * accepts an array of arguments that will be triggered using initialize() when the class is instantiated.
 *
 * @package	titon.base.types
 */
class Enum {

	/**
	 * Mapping of enum constructor arguments.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_enums = [];

	/**
	 * The current enum type.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_type;

	/**
	 * Construct the enum based on the class constants.
	 * Initialize the arguments if any exist.
	 *
	 * @access public
	 * @param int $type
	 * @throws titon\base\BaseException
	 * @final
	 */
	final public function __construct($type) {
		if (!is_int($type) || !isset($this->_enums[$type])) {
			throw new BaseException(sprintf('Invalid enum type detected for %s.', get_class($this)));
		}

		$this->_type = (int) $type;

		if (method_exists($this, 'initialize')) {
			call_user_func_array([$this, 'initialize'], $this->_enums[$type]);
		}
	}

	/**
	 * Return type when called as a string.
	 *
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return (string) $this->_type;
	}

	/**
	 * Validate the current type matches one of the enum constants.
	 *
	 * @access public
	 * @param int $type
	 * @return boolean
	 * @final
	 */
	final public function is($type) {
		return ($this->_type === $type);
	}

	/**
	 * Return the selected types value.
	 *
	 * @access public
	 * @return int
	 * @final
	 */
	final public function value() {
		return $this->_type;
	}

}
