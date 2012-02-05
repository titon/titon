<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\base\types;

use \titon\base\types\Type;
use \titon\base\BaseException;

/**
 * The Float type works in a similar fashion to the Integer type, but with support for floating integer functions.
 *
 * @package	titon.base.types
 */
class Float extends Integer {

	/**
	 * Type cast to a float.
	 *
	 * @access public
	 * @param float $value
	 * @param int $base
	 * @return void
	 * @throws BaseException
	 */
	public function __construct($value, $base = 10) {
		parent::__construct((float) $value);

		if (!in_array($base, array(2, 8, 10, 16, 32))) {
			throw new BaseException(sprintf('Unsupported base type %d, allowed types: 2, 8, 10, 16, 32', $base));
		}

		$this->_base = $base;
	}

	/**
	 * Absolute value. Alias for abs().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function abs() {
		$this->_value = abs($this->_value);

		return $this;
	}

	/**
	 * Arc cosine. Alias for acos().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function acos() {
		$this->_value = acos($this->_value);

		return $this;
	}

	/**
	 * Inverse hyperbolic cosine. Alias for acosh().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function acosh() {
		$this->_value = acosh($this->_value);

		return $this;
	}

	/**
	 * Arc sine. Alias for asin().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function asin() {
		$this->_value = asin($this->_value);

		return $this;
	}

	/**
	 * Inverse hyperbolic sine. Alias for asinh().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function asinh() {
		$this->_value = asinh($this->_value);

		return $this;
	}

	/**
	 * Arc tangent of two variables. Alias for atan2().
	 *
	 * @access public
	 * @param float $float
	 * @return Float
	 * @chainable
	 */
	public function atan2($float) {
		$this->_value = atan2($this->_value, $float);

		return $this;
	}

	/**
	 * Inverse hyperbolic tangent. Alias for atanh().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function atanh() {
		$this->_value = atanh($this->_value);

		return $this;
	}

	/**
	 * Arc tangent. Alias for atan().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function atan() {
		$this->_value = atan($this->_value);

		return $this;
	}

	/**
	 * Round fractions up. Alias for ceil().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function ceil() {
		$this->_value = ceil($this->_value);

		return $this;
	}

	/**
	 * Cosine. Alias for cos().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function cos() {
		$this->_value = cos($this->_value);

		return $this;
	}

	/**
	 * Hyperbolic cosine. Alias for cosh().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function cosh() {
		$this->_value = cosh($this->_value);

		return $this;
	}

	/**
	 * Converts the number in degrees to the radian equivalent. Alias for deg2rad().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function deg2rad() {
		$this->_value = deg2rad($this->_value);

		return $this;
	}

	/**
	 * Calculates the exponent of e. Alias for exp().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function exp() {
		$this->_value = exp($this->_value);

		return $this;
	}

	/**
	 * Returns exp(number) - 1, computed in a way that is accurate even when the value of number is close to zero. Alias for expm1().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function expm1() {
		$this->_value = expm1($this->_value);

		return $this;
	}

	/**
	 * Round fractions down. Alias for floor().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function floor() {
		$this->_value = floor($this->_value);

		return $this;
	}

	/**
	 * Returns the floating point remainder (modulo) of the division of the arguments. Alias for fmod().
	 *
	 * @access public
	 * @param float $float
	 * @return Float
	 * @chainable
	 */
	public function fmod($float) {
		$this->_value = fmod($this->_value, $float);

		return $this;
	}
	
	/**
	 * Check to see if the current float is finite.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isFinite() {
		return is_finite($this->_value);
	}

	/**
	 * Check to see if the current float is infinite.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isInfinite() {
		return is_infinite($this->_value);
	}

	/**
	 * Check to see if the current float is not a number.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isNaN() {
		return is_nan($this->_value);
	}

	/**
	 * Converts the radian number to the equivalent number in degrees. Alias for rad2deg().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function rad2deg() {
		$this->_value = rad2deg($this->_value);

		return $this;
	}

	/**
	 * Sine. Alias for sin().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function sin() {
		$this->_value = sin($this->_value);

		return $this;
	}

	/**
	 * Hyperbolic sine. Alias for sinh().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function sinh() {
		$this->_value = sinh($this->_value);

		return $this;
	}

	/**
	 * Square root. Alias for sqrt().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function sqrt() {
		$this->_value = sqrt($this->_value);

		return $this;
	}

	/**
	 * Tangent. Alias for tan().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function tan() {
		$this->_value = tan($this->_value);

		return $this;
	}

	/**
	 * Hyperbolic tangent. Alias for tanh().
	 *
	 * @access public
	 * @return Float
	 * @chainable
	 */
	public function tanh() {
		$this->_value = tanh($this->_value);

		return $this;
	}

}
