<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\listeners\security;

use titon\Titon;
use titon\libs\listeners\security\InputFilterListener;
use titon\tests\TestCase;

/**
 * Test class for titon\libs\listeners\security\InputFilterListener.
 */
class InputFilterListenerTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$_GET = [
			'escape' => '<html>Html</html>',
			'newlines' => "Excessive\n\n\n\r\r\rnewlines",
			'whitespace' => "Excessive   \t\t\twhitespace"
		];

		$_POST = [
			'callback' => 'foo'
		];

		$this->object = new InputFilterListener([
			'clean' => ['get', 'post'],
			'escape' => true,
			'newlines' => true,
			'whitespace' => true,
			'callback' => function($value) {
				return str_replace('foo', 'bar', $value);
			}
		]);
	}

	/**
	 * Test that startup() and clean() sanitize globals.
	 */
	public function testStartupAndClean() {
		$this->object->startup();

		$this->assertEquals([
			'escape' => '&lt;html&gt;Html&lt;/html&gt;',
			'newlines' => "Excessive\n\rnewlines",
			'whitespace' => "Excessive \twhitespace"
		], $_GET);

		$this->assertEquals(['callback' => 'bar'], $_POST);
	}

}