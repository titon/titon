<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\models;

use \titon\libs\models\Model;
use \titon\libs\models\ModelException;

/**
 * @todo
 *
 * @package	titon.libs.models
 */
class ModelAbstract implements Model {

	/**
	 * Current data representation of a database row result.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_data = array();

	/**
	 * Mapping of getters for fields.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_getters = array();

	/**
	 * Mapping of setters for fields.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_setters = array();

	/**
	 * Database schema that this model represents.
	 *
	 * @access private
	 * @var array
	 */
	private $__schema = array();

	/**
	 * Dynamically set a data row result through the constructor.
	 *
	 * @access public
	 * @param array $data
	 */
	public function __construct(array $data = array()) {
		foreach ($data as $field => $value) {
			$this->set($field, $value);
		}
	}

	/**
	 * Magic method for Model::get().
	 *
	 * @access public
	 * @param string $field
	 * @return mixed
	 * @final
	 */
	final public function __get($field) {
		return $this->get($field);
	}

	/**
	 * Magic method for Model::set().
	 *
	 * @access public
	 * @param string $field
	 * @param mixed $value
	 * @return void
	 */
	final public function __set($field, $value) {
		$this->set($field, $value);
	}

	/**
	 * Magic method for Model::has().
	 *
	 * @access public
	 * @param string $field
	 * @return boolean
	 * @final
	 */
	final public function __isset($field) {
		return $this->has($field);
	}

	/**
	 * Magic method for Model::remove().
	 *
	 * @access public
	 * @param $field
	 * @return void
	 * @final
	 */
	final public function __unset($field) {
		$this->remove($field);
	}

	/**
	 * Get the value of a field if it exists. If a getter function has been mapped, execute it.
	 *
	 * @access public
	 * @param string $field
	 * @return mixed
	 * @throws \titon\libs\models\ModelException
	 */
	public function get($field) {
		if (!$this->hasField($field)) {
			throw new ModelException(sprintf('%s does not contain the %s field.', get_class($this), $field));
		}

		if (!$this->has($field)) {
			return null;
		}

		$value = $this->_data[$field];

		if (isset($this->_getters[$field])) {
			$value = call_user_func_array(array($this, $this->_getters[$field]), array($value));
		}

		return $value;
	}

	/**
	 * Check if a field exists in the data result.
	 *
	 * @access public
	 * @param string $field
	 * @return boolean
	 */
	public function has($field) {
		return isset($this->_data[$field]);
	}

	/**
	 * Check if a field exists in the schema.
	 *
	 * @access public
	 * @param string $field
	 * @return boolean
	 */
	public function hasField($field) {
		return isset($this->__schema[$field]);
	}

	/**
	 * Set the value for a field if it exists in the schema. If a setter function has been mapped, execute it.
	 *
	 * @access public
	 * @param string $field
	 * @param mixed $value
	 * @return void
	 * @throws \titon\libs\models\ModelException
	 */
	public function set($field, $value) {
		if (!$this->hasField($field)) {
			throw new ModelException(sprintf('%s does not contain the %s field.', get_class($this), $field));
		}

		if (isset($this->_setters[$field])) {
			$value = call_user_func_array(array($this, $this->_setters[$field]), array($value));
		}

		$this->_data[$field] = $value;
	}

	/**
	 * Remove a field from the data result.
	 *
	 * @access public
	 * @param $field
	 * @return void
	 */
	public function remove($field) {
		unset($this->_data[$field]);
	}

	/**
	 * Return the database schema.
	 *
	 * @access public
	 * @return array
	 */
	public function schema() {
		return $this->__schema;
	}

}
