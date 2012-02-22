<?php

include 'Exception.php';
include 'utility/Inflector.php';
include 'libs/traits/Decorator.php';
include 'libs/traits/Memoizer.php';
include 'libs/traits/TraitException.php';

use \titon\libs\traits\Decorator;
use \titon\libs\traits\Memoizer;

class TestClass {
	use Decorator, Memoizer;

	public $classPublicProp;
	protected $classProtectedProp;
	private $classPrivateProp;

	public function classPublicMethod() {}
	protected function classProtectedMethod() {}
	private function classPrivateMethod() {}

	public function __construct() {
		$this->attachObject('class2', function($self) {
			return new TestClass2();
		});

		$this->attachObject('class3', function($self) {
			throw new \Exception('Class3');
		});
	}

	public function testCache($var) {
		return $this->cacheMethod(__FUNCTION__, $var, function($self) use ($var) {
			return ($var * 3);
		});
	}

}

class TestClass2 {
	use Decorator;

	public function __construct() {
		$this->attachObject('class3', function($self) {
			return new TestClass3();
		});
	}
}

class TestClass3 {
	use Decorator;

	public function __construct() {
		$this->attachObject('class1', function($self) {
			return new TestClass();
		});
	}
}

$class = new TestClass();
$class->testCache(1);
$class->testCache(2);
$class->testCache(3);
$class->testCache(1);
$class->testCache(2);
$class->testCache(3);

echo '<pre>' . print_r($class, 1) . '</pre>';
echo '<pre>' . print_r(get_class_methods($class), 1) . '</pre>';
echo '<pre>' . print_r(get_object_vars($class), 1) . '</pre>';
echo '<pre>' . print_r($class->class2->class3->class1->class2->class3, 1) . '</pre>';
//echo '<pre>' . print_r($class->class3, 1) . '</pre>';
