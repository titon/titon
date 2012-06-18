<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\augments;

use titon\Titon;
use titon\libs\traits\Memoizer;
use \ReflectionClass;
use \ReflectionMethod;
use \ReflectionProperty;

/**
 * An augment that supplies configuration options for primary classes.
 * The augment can take a optional secondary default configuration,
 * which can be used to autobox values anytime a config is written.
 *
 * @package	titon.libs.augments
 */
class InfoAugment {
	use Memoizer;

	/**
	 * Class to introspect.
	 *
	 * @access protected
	 * @var object
	 */
	protected $_class;

	/**
	 * Reflection object.
	 *
	 * @access protected
	 * @var \ReflectionClass
	 */
	protected $_reflection;

	/**
	 * Store the class to grab information on and its reflection.
	 *
	 * @access public
	 * @param object $class
	 */
	public function __construct($class) {
		$this->_class = $class;
		$this->_reflection = new ReflectionClass($class);
	}

	/**
	 * Access InfoAugment methods as properties.
	 *
	 * @access public
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 * @throws titon\libs\augments\AugmentException
	 */
	public function __call($name, $args) {
		return $this->cacheMethod($name, null, function($self) use ($name, $args) {
			if (method_exists($self, $name)) {
				return call_user_func_array(array($self, $name), $args);
			}

			throw new AugmentException(sprintf('Information descriptor %s does not exist.', $name));
		});
	}

	/**
	 * Return the class name with the namespace.
	 *
	 * @access public
	 * @return string
	 */
	public function className() {
		return $this->_reflection->getName();
	}

	/**
	 * Return the class name without the namespace.
	 *
	 * @access public
	 * @return string
	 */
	public function shortClassName() {
		return $this->_reflection->getShortName();
	}

	/**
	 * Return the namespace without the class name.
	 *
	 * @access public
	 * @return string
	 */
	public function namespaceName() {
		return $this->_reflection->getNamespaceName();
	}

	/**
	 * Return the file system path to the class.
	 *
	 * @access public
	 * @return string
	 */
	public function filePath() {
		return Titon::loader()->toPath(get_class($this->_class));
	}

	/**
	 * Return an array of public, protected, private and static methods.
	 *
	 * @access public
	 * @return array
	 */
	public function methods() {
		return $this->cacheMethod(__METHOD__, null, function($self) {
			return array_merge(
				$self->publicMethods(),
				$self->protectedMethods(),
				$self->privateMethods(),
				$self->staticMethods()
			);
		});
	}

	/**
	 * Return an array of public methods.
	 *
	 * @access public
	 * @return array
	 */
	public function publicMethods() {
		return $this->_reflection->getMethods(ReflectionMethod::IS_PUBLIC);
	}

	/**
	 * Return an array of protected methods.
	 *
	 * @access public
	 * @return array
	 */
	public function protectedMethods() {
		return $this->_reflection->getMethods(ReflectionMethod::IS_PROTECTED);
	}

	/**
	 * Return an array of private methods.
	 *
	 * @access public
	 * @return array
	 */
	public function privateMethods() {
		return $this->_reflection->getMethods(ReflectionMethod::IS_PRIVATE);
	}

	/**
	 * Return an array of static methods.
	 *
	 * @access public
	 * @return array
	 */
	public function staticMethods() {
		return $this->_reflection->getMethods(ReflectionMethod::IS_STATIC);
	}

	/**
	 * Return an array of public, protected, private and static properties.
	 *
	 * @access public
	 * @return array
	 */
	public function properties() {
		return $this->cacheMethod(__METHOD__, null, function($self) {
			return array_merge(
				$self->publicProperties(),
				$self->protectedProperties(),
				$self->privateProperties(),
				$self->staticProperties()
			);
		});
	}

	/**
	 * Return an array of public properties.
	 *
	 * @access public
	 * @return array
	 */
	public function publicProperties() {
		return $this->_properties(ReflectionProperty::IS_PUBLIC);
	}

	/**
	 * Return an array of protected properties.
	 *
	 * @access public
	 * @return array
	 */
	public function protectedProperties() {
		return $this->_properties(ReflectionProperty::IS_PROTECTED);
	}

	/**
	 * Return an array of private properties.
	 *
	 * @access public
	 * @return array
	 */
	public function privateProperties() {
		return $this->_properties(ReflectionProperty::IS_PRIVATE);
	}

	/**
	 * Return an array of static properties.
	 *
	 * @access public
	 * @return array
	 */
	public function staticProperties() {
		return $this->_properties(ReflectionProperty::IS_STATIC);
	}

	/**
	 * Return an array of constants defined in the class.
	 *
	 * @access public
	 * @return array
	 */
	public function constants() {
		return $this->_reflection->getConstants();
	}

	/**
	 * Return an array of interfaces that the class implements.
	 *
	 * @access public
	 * @return array
	 */
	public function interfaces() {
		return $this->_reflection->getInterfaceNames();
	}

	/**
	 * Return an array of traits that the class implements.
	 *
	 * @access public
	 * @return array
	 */
	public function traits() {
		return $this->_reflection->getTraitNames();
	}

	/**
	 * Return an array of interfaces that the class implements.
	 *
	 * @access public
	 * @return array
	 */
	public function parent() {
		return $this->_reflection->getParentClass()->getName();
	}

	/**
	 * Return an array of properties for the defined scope.
	 *
	 * @access protected
	 * @param mixed $scope
	 * @return array
	 */
	protected function _properties($scope) {
		return $this->cacheMethod(__METHOD__, null, function($self) use ($scope) {
			$props = array();

			foreach ($self->_reflection->getProperties($scope) as $prop) {
				$props[] = $prop->getName();
			}

			return $props;
		});
	}

}