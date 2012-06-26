<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base\types;

use titon\base\types\Type;
use titon\base\BaseException;
use \Closure;

/**
 * The Integer type allows for the twiddling and calculation of numbers (non-floats) and provides support for generic integer bases.
 * One can also modify the integer using a series of chained method calls that sequentially alter the initial value.
 *
 * @package	titon.base.types
 */
class Integer extends Type {

	/**
	 * The integer base; default decimal.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_base = 10;

	/**
	 * Store the number value and base.
	 *
	 * @access public
	 * @param mixed $value
	 * @param int $base
	 * @throws titon\base\BaseException
	 */
	public function __construct($value, $base = 10) {
		parent::__construct((int) $value);

		if (!in_array($base, [2, 8, 10, 16, 32])) {
			throw new BaseException(sprintf('Unsupported base type %d, allowed types: 2, 8, 10, 16, 32', $base));
		}

		$this->_base = $base;
	}

	/**
	 * Add to the current value.
	 *
	 * @access public
	 * @param int $no
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function add($no) {
		$this->_value = $this->_value + $no;

		return $this;
	}

	/**
	 * Returns the total 1's in the binary representation of the specified value.
	 * This function is sometimes referred to as the population count.
	 *
	 * @access public
	 * @return int
	 */
	public function bitCount() {
		$value = ($this->_base !== 2) ? $this->toBinary() : $this->_value;

		return substr_count($value, '1');
	}

	/**
	 * Run an advanced expression/calculation through the use of a closure.
	 *
	 * @access public
	 * @param Closure $expr
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function calculate(Closure $expr) {
		$this->_value = $expr($this->_value);

		return $this;
	}

	/**
	 * Compares against another number. If both values are equal, 0 is returned,
	 * if base value is greater 1 is returned, if base value is less -1 is returned.
	 *
	 * @access public
	 * @param int $no
	 * @return boolean
	 */
	public function compare($no) {
		return ($this->_value == $no) ? 0 : (($this->_value < $no) ? -1 : 1);
	}

	/**
	 * Convert a number from one base to another.
	 *
	 * @access public
	 * @param int $no
	 * @param int $fromBase
	 * @param int $toBase
	 * @return int
	 * @static
	 */
	public static function convert($no, $fromBase, $toBase) {
		if ($toBase == $fromBase) {
			return $no;
		}

		return base_convert($no, $fromBase, $toBase);
	}

	/**
	 * Divide the current value.
	 *
	 * @access public
	 * @param int $no
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function divide($no) {
		$this->_value = $this->_value / $no;

		return $this;
	}

	/**
	 * Does the passed value match the current value?
	 *
	 * @access public
	 * @param int $no
	 * @return boolean
	 */
	public function equals($no) {
		return ($this->_value == $no);
	}

	/**
	 * Evaluate an expression to return a boolean.
	 *
	 * @access public
	 * @param Closure $expr
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function evaluate(Closure $expr) {
		return $expr($this->_value);
	}

	/**
	 * Is the current value greater than the passed value.
	 *
	 * @access public
	 * @param int $no
	 * @return boolean
	 */
	public function greaterThan($no) {
		return ($this->_value > $no);
	}

	/**
	 * Is the current value greater than or equals to the passed value.
	 *
	 * @access public
	 * @param int $no
	 * @return boolean
	 */
	public function greaterThanEquals($no) {
		return ($this->_value >= $no);
	}

	/**
	 * Is the current value within a range of two numbers.
	 *
	 * @access public
	 * @param int $start
	 * @param int $end
	 * @return boolean
	 */
	public function inRange($start, $end) {
		return ($this->_value >= $start && $this->_value <= $end);
	}

	/**
	 * Is the current value even?
	 *
	 * @access public
	 * @return boolean
	 */
	public function isEven() {
		return ($this->_value % 2 === 0);
	}

	/**
	 * Is the current value negative; less than zero.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isNegative() {
		return ($this->_value < 0);
	}

	/**
	 * Is the current value odd?
	 *
	 * @access public
	 * @return boolean
	 */
	public function isOdd() {
		return !$this->isEven();
	}

	/**
	 * Is the current value positive; greater than or equal to zero.
	 *
	 * @access public
	 * @param boolean $zero
	 * @return boolean
	 */
	public function isPositive($zero = true) {
		return ($zero) ? ($this->_value >= 0) : ($this->_value > 0);
	}

	/**
	 * The number of bits in the value in binary form.
	 *
	 * @access public
	 * @param boolean $reset
	 * @return int
	 */
	public function length($reset = false) {
		if ($this->_length === null || $reset) {
			$this->_length = (int) strlen($this->toBinary());
		}

		return $this->_length;
	}

	/**
	 * Is the current value less than the passed value.
	 *
	 * @access public
	 * @param int $no
	 * @return boolean
	 */
	public function lessThan($no) {
		return ($this->_value < $no);
	}

	/**
	 * Is the current value less than or equals to the passed value.
	 *
	 * @access public
	 * @param int $no
	 * @return boolean
	 */
	public function lessThanEquals($no) {
		return ($this->_value <= $no);
	}

	/**
	 * Limits this number between two bounds.
	 *
	 * @access public
	 * @param int $min
	 * @param int $max
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function limit($min, $max) {
		return $this->min($min)->max($max);
	}

	/**
	 * Increase the number to the minimum if below threshold.
	 *
	 * @access public
	 * @param int $min
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function min($min) {
		if ($this->_value < $min) {
			$this->_value = $min;
		}

		return $this;
	}

	/**
	 * Decrease the number to the maximum if above threshold.
	 *
	 * @access public
	 * @param int $max
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function max($max) {
		if ($this->_value > $max) {
			$this->_value = $max;
		}

		return $this;
	}

	/**
	 * Multiply the current value.
	 *
	 * @access public
	 * @param int $no
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function multiply($no) {
		$this->_value = $this->_value * $no;

		return $this;
	}

	/**
	 * Modulus the current value.
	 *
	 * @access public
	 * @param int $no
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function modulus($no) {
		$this->_value = $this->_value % $no;

		return $this;
	}

	/**
	 * Convert the value to its negative equivalent.
	 *
	 * @access public
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function negate() {
		$this->_value = -$this->_value;

		return $this;
	}

	/**
	 * Is the current value not equal to the passed value.
	 *
	 * @access public
	 * @param int $no
	 * @return boolean
	 */
	public function notEquals($no) {
		return !$this->equals($no);
	}

	/**
	 * Is the current value outside of the current range.
	 *
	 * @access public
	 * @param int $start
	 * @param int $end
	 * @return boolean
	 */
	public function outRange($start, $end) {
		return ($this->_value < $start && $this->_value > $end);
	}

	/**
	 * Reverse the order of the bits in the binary representation of the specified value.
	 * Use a hacky way of simply reversing the string.
	 *
	 * @access public
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function reverse() {
		$value = $this->_value;

		if ($this->_base !== 2) {
			$value = $this->toBinary();
		}

		$this->_value = base_convert(strrev($value), 2, $this->_base);

		return $this;
	}

	/**
	 * Rounds a float.
	 *
	 * @access public
	 * @param int $precision
	 * @param int $mode
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function round($precision = 0, $mode = PHP_ROUND_HALF_UP) {
		$this->_value = round($this->_value, $precision, $mode);

		return $this;
	}

	/**
	 * Shift the value to the left (multiply by two per step).
	 *
	 * @access public
	 * @param int $step
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function shiftLeft($step) {
		$this->_value = $this->_value << (int) $step;

		return $this;
	}

	/**
	 * Shift the value to the left (divide by two per step).
	 *
	 * @access public
	 * @param int $step
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function shiftRight($step) {
		$this->_value = $this->_value >> (int) $step;

		return $this;
	}

	/**
	 * Returns -1 if the value is negative, 0 if the value equals 0, or 1 if the value is positive.
	 *
	 * @access public
	 * @return int
	 */
	public function signum() {
		if ($this->_value < 0) {
			return -1;
		} else if ($this->_value === 0) {
			return 0;
		} else {
			return 1;
		}
	}

	/**
	 * Alias for length().
	 *
	 * @access public
	 * @param boolean $reset
	 * @return int
	 */
	public function size($reset = false) {
		return $this->length($reset);
	}

	/**
	 * Subtract from the current value.
	 *
	 * @access public
	 * @param int $no
	 * @return titon\base\types\Number
	 * @chainable
	 */
	public function subtract($no) {
		$this->_value = $this->_value - $no;

		return $this;
	}

	/**
	 * Returns as an unsigned integer in base 2 (binary).
	 *
	 * @access public
	 * @return int
	 */
	public function toBinary() {
		return $this->convert($this->_value, $this->_base, 2);
	}

	/**
	 * Returns as a byte (or what the PHP integer representation would be).
	 *
	 * @access public
	 * @return int
	 */
	public function toByte() {
		return ($this->_value << (64 - 8)) / (1 << (64 - 8));
	}

	/**
	 * Returns as an unsigned integer in base 10 (decimal).
	 *
	 * @access public
	 * @return int
	 */
	public function toDecimal() {
		return $this->convert($this->_value, $this->_base, 10);
	}

	/**
	 * Converts the number to a float.
	 *
	 * @access public
	 * @return float
	 */
	public function toFloat() {
		return (float) $this->_value;
	}

	/**
	 * Returns as an unsigned integer in base 16 (hexadecimal).
	 *
	 * @access public
	 * @return string
	 */
	public function toHex() {
		return $this->convert($this->_value, $this->_base, 16);
	}

	/**
	 * Converts the number to an integer.
	 *
	 * @access public
	 * @return int
	 */
	public function toInt() {
		return (int) $this->_value;
	}

	/**
	 * Returns as an unsigned integer in base 8 (octal).
	 *
	 * @access public
	 * @return string
	 */
	public function toOctal() {
		return $this->convert($this->_value, $this->_base, 8);
	}

	/**
	 * Return a new instance based on the current value.
	 *
	 * @access public
	 * @return titon\base\types\Number
	 */
	public function valueOf() {
		return new self($this->_value, $this->_base);
	}

}