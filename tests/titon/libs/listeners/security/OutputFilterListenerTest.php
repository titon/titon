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
use titon\libs\engines\core\ViewEngine;
use titon\libs\listeners\security\OutputFilterListener;
use titon\tests\TestCase;

/**
 * Test class for titon\libs\listeners\security\OutputFilterListener.
 */
class OutputFilterListenerTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new OutputFilterListener([
			'escape' => true,
			'xss' => true
		]);
	}

	/**
	 * Test that preRender() and clean() engine variables.
	 */
	public function testPreRenderAndClean() {
		$engine = new ViewEngine();
		$engine->set([
			'html' => '<html>Tag</html>',
			'xss' => 'XSS <script>alert("XSS!");</script> attack!'
		]);

		$this->object->preRender($engine);

		$this->assertEquals([
			'html' => '&lt;html&gt;Tag&lt;/html&gt;',
			'xss' => 'XSS alert(&quot;XSS!&quot;); attack!'
		], $engine->get());
	}

}