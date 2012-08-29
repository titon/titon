<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\validators;

use titon\Titon;
use titon\base\Base;
use titon\libs\traits\Attachable;
use titon\libs\validators\ValidatorException;

/**
 * @todo
 *
 * @package	titon.libs.validators
 * @abstract
 */
abstract class ValidatorAbstract extends Base implements Validator {
	use Attachable;

	/**
	 * Current field.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_current;

	/**
	 * Data to validate against.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_data = [];

	/**
	 * Errors gathered during validation.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_errors = [];

	/**
	 * Mapping of fields and titles.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_fields = [];

	/**
	 * Model name to map to the view.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_model;

	/**
	 * Mapping of fields and validation rules.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_rules = [];

	/**
	 * Store the data to validate.
	 *
	 * @access public
	 * @param string $model
	 * @param array $data
	 */
	public function __construct($model, array $data) {
		parent::__construct();

		$this->_model = $model;
		$this->_data = $data;

		$this->attachObject('request', function() {
			return Titon::registry()->factory('titon\net\Request');
		});
	}

	/**
	 * Mark a field has an error.
	 *
	 * @access public
	 * @param string $field
	 * @param string $message
	 * @return \titon\libs\validators\Validator
	 */
	public function addError($field, $message) {
		$this->_errors[$field] = $message;

		return $this;
	}

	/**
	 * Add a field to be used in validation. Can optionally apply an array of validation rules.
	 *
	 * @access public
	 * @param string $field
	 * @param string $title
	 * @param array $rules
	 * @return \titon\libs\validators\Validator
	 */
	public function addField($field, $title, array $rules = []) {
		$this->_fields[$field] = $title;
		$this->_current = $field;

		/**
		 * # => rule
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

	/**
	 * Add a validation rule to a field. Can supply an optional error message and options.
	 *
	 * @access public
	 * @param string $rule
	 * @param string $message
	 * @param array $options
	 * @return \titon\libs\validators\Validator
	 * @throws \titon\libs\validators\ValidatorException
	 */
	public function addRule($rule, $message = null, $options = []) {
		if (!$this->_current) {
			throw new ValidatorException('No field has been defined.');
		}

		$msgOptions = $options;

		if ($msgOptions) {
			foreach ($msgOptions as &$msgOption) {
				if (is_array($msgOption)) {
					$msgOption = implode(', ', $msgOption);
				}
			}
		}

		$this->_rules[$this->_current][$rule] = [
			'message' => $message ?: \titon\msg('validation.' . $rule, $msgOptions),
			'options' => (array) $options
		];

		return $this;
	}

	/**
	 * Return the errors.
	 *
	 * @access public
	 * @return array
	 */
	public function getErrors() {
		return $this->_errors;
	}

	/**
	 * Return the fields.
	 *
	 * @access public
	 * @return array
	 */
	public function getFields() {
		return $this->_fields;
	}

	/**
	 * Return the rules.
	 *
	 * @access public
	 * @return array
	 */
	public function getRules() {
		return $this->_rules;
	}

	/**
	 * Validate the data against the rules schema. Return true if all fields passed validation.
	 *
	 * @access public
	 * @return boolean
	 */
	public function validate() {
		if ($this->_data) {
			foreach ($this->_data as $field => $value) {

				// Validate the rules
				if (isset($this->_rules[$field])) {
					foreach ($this->_rules[$field] as $rule => $options) {
						$params = $options['options'];
						array_unshift($params, $value);

						if (!call_user_func_array(['titon\utility\Validate', $rule], $params)) {
							$this->addError($field, $options['message']);
							break;
						}
					}
				}

			}
		}

		// Store the errors in the request so they may be used in other locations
		$this->request->set('Validator.' . $this->_model, [
			'fields' => $this->getFields(),
			'errors' => $this->getErrors()
		]);

		// Return false if errors exist
		if ($this->_errors) {
			return false;
		}

		return true;
	}

}