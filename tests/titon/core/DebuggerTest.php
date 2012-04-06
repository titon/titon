<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

include_once dirname(dirname(__DIR__)) . '/bootstrap.php';

/**
 * Test class for titon\core\Debugger.
 */
class DebuggerTest extends \PHPUnit_Framework_TestCase {

	protected $object;

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = titon\Titon::debugger();
	}

	/**
	 * Test that enabling or disabling error reporting works.
	 */
	public function testEnable() {
		$this->object->enable(true);
		$this->assertTrue(ini_get('display_errors') == 1);
		$this->assertEquals(32767, ini_get('error_reporting'));

		$this->object->enable(false);
		$this->assertTrue(ini_get('display_errors') == 0);
		$this->assertEquals(0, ini_get('error_reporting'));

		$this->object->enable(true);
	}

	/**
	 * Test that the correct error type is returned.
	 */
	public function testErrorType() {
		$this->assertEquals('Error', $this->object->errorType(E_ERROR));
		$this->assertEquals('Core Warning', $this->object->errorType(E_CORE_WARNING));
		$this->assertEquals('Unknown Error', $this->object->errorType('Foobar'));
	}

	/**
	 * Test that types are casted correctly.
	 */
	public function testParseArg() {
		$this->assertEquals(12345, $this->object->parseArg('12345'));
		$this->assertEquals('true', $this->object->parseArg(true));
		$this->assertEquals('null', $this->object->parseArg(null));
		$this->assertEquals('titon\core\Debugger', $this->object->parseArg($this->object));
		$this->assertEquals("'string'", $this->object->parseArg('string'));
		$this->assertEquals("'" . htmlentities('<b>string</b>') . "'", $this->object->parseArg('<b>string</b>'));
		$this->assertEquals('array()', $this->object->parseArg(array()));
		$this->assertEquals("array(123, 'foo', null, true)", $this->object->parseArg(array(123, 'foo', null, true)));
		$this->assertEquals("array([Truncated])", $this->object->parseArg(array(123, 'foo', null, true), true));
	}

	/**
	 * Test that certain file paths are replaced with constant shortcuts.
	 */
	public function testParseFile() {
		$this->assertEquals('[internal]', $this->object->parseFile(null));
		$this->assertEquals('[vendors]', $this->object->parseFile(TITON_VENDORS));
		$this->assertEquals('[vendors]titon/', $this->object->parseFile(TITON_SOURCE));
		$this->assertEquals('[vendors]titon/core/Debugger.php', $this->object->parseFile(TITON_SOURCE . 'core/Debugger.php'));
		$this->assertEquals('[app]', $this->object->parseFile(TITON_APP));
		$this->assertEquals('[app]modules/controllers/TestController.php', $this->object->parseFile(TITON_APP . 'modules/controllers/TestController.php'));
		$this->assertEquals('[libs]', $this->object->parseFile(TITON_LIBS));
		$this->assertEquals('[libs]traits/Decorator.php', $this->object->parseFile(TITON_LIBS . 'traits/Decorator.php'));
	}

}
