<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\validators;

use titon\base\Base;
use titon\libs\validators\ValidatorException;
use titon\utility\String;

/**
 *
 * @package	titon.libs.validators
 * @abstract
 */
abstract class ValidatorAbstract extends Base implements Validator {

	protected $_current;

	protected $_data = [];

	protected $_errors = [];

	protected $_fields = [];

	protected $_rules = [];

	public function __construct(array $data) {
		$this->_data = $data;

		parent::__construct();
	}

	public function addError($field, $message) {
		$this->_errors[$field] = $message;

		return $this;
	}

	public function addField($field, $title, array $rules = []) {
		$this->_fields[$field] = $title;
		$this->_current = $field;

		/**
		 * rule
		 * rule => message
		 * rule => [message, opt, opt]
		 */
		if ($rules) {
			foreach ($rules as $rule => $params) {
				$message = null;
				$options = [];

				if (is_numeric($rule)) {
					$rule = (string) $params;

				} else if (is_array($params)) {
					$message = array_shift($params);
					$options = $params;

				} else {
					$message = $params;
				}

				$this->addRule($rule, $message, $options);
			}
		}

		return $this;
	}

	public function addRule($rule, $message = null, $options = []) {
		if (!$this->_current) {
			throw new ValidatorException('No field has been defined.');
		}

		$this->_rules[$this->_current][$rule] = [
			'message' => $message ?: \titon\msg('validation.' + $rule),
			'options' => (array) $options
		];

		return $this;
	}

	public function getErrors() {
		return $this->_errors;
	}

	public function getFields() {
		return $this->_fields;
	}

	public function getRules() {
		return $this->_rules;
	}

	public function validate() {
		if ($this->_data) {
			foreach ($this->_data as $field => $value) {

				// Validate the rules
				if ($this->_rules[$field]) {
					foreach ($this->_rules[$field] as $rule => $options) {
						$params = $options['options'];
						array_unshift($params, $value);

						if (!call_user_func_array(['titon\utility\Validate', $rule], $params)) {
							$this->addError($field, String::insert($options['message'], $params));
							break;
						}
					}
				}

			}
		}

		// Return false if errors exist
		if ($this->_errors) {
			return false;
		}

		return true;
	}

}