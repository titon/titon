<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\validators;

/**
 * Interface for the validators library.
 *
 * @package	titon.libs.validators
 */
interface Validator {

	/**
	 * Validate the data against the rules schema. Return true if all fields passed validation.
	 *
	 * @access public
	 * @return boolean
	 */
	public function validate();

}