<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\utility;

use titon\tests\TestCase;
use titon\utility\Sanitize;
use titon\utility\UtilityException;

/**
 * Test class for titon\utility\Sanitize.
 */
class SanitizeTest extends TestCase {

	/**
	 * Test that escape() escapes HTML and entities.
	 */
	public function testEscape() {
		$this->assertEquals('"Double" quotes', Sanitize::escape('"Double" quotes', ['flags' => ENT_NOQUOTES]));
		$this->assertEquals('&quot;Double&quot; quotes', Sanitize::escape('"Double" quotes', ['flags' => ENT_COMPAT]));
		$this->assertEquals('&quot;Double&quot; quotes', Sanitize::escape('"Double" quotes', ['flags' => ENT_QUOTES]));
		$this->assertEquals('&quot;Double&quot; quotes', Sanitize::escape('"Double" quotes', ['flags' => ENT_QUOTES | ENT_HTML5]));
		$this->assertEquals('&quot;Double&quot; quotes', Sanitize::escape('"Double" quotes', ['flags' => ENT_QUOTES | ENT_XHTML]));

		$this->assertEquals("'Single' quotes", Sanitize::escape("'Single' quotes", ['flags' => ENT_NOQUOTES]));
		$this->assertEquals("'Single' quotes", Sanitize::escape("'Single' quotes", ['flags' => ENT_COMPAT]));
		$this->assertEquals("&#039;Single&#039; quotes", Sanitize::escape("'Single' quotes", ['flags' => ENT_QUOTES]));
		$this->assertEquals("&apos;Single&apos; quotes", Sanitize::escape("'Single' quotes", ['flags' => ENT_QUOTES | ENT_HTML5]));
		$this->assertEquals("&#039;Single&#039; quotes", Sanitize::escape("'Single' quotes", ['flags' => ENT_QUOTES | ENT_XHTML]));

		$this->assertEquals('&lt;Html&gt; tags', Sanitize::escape('<Html> tags', ['flags' => ENT_NOQUOTES]));
		$this->assertEquals('&lt;Html&gt; tags', Sanitize::escape('<Html> tags', ['flags' => ENT_COMPAT]));
		$this->assertEquals('&lt;Html&gt; tags', Sanitize::escape('<Html> tags', ['flags' => ENT_QUOTES]));
		$this->assertEquals('&lt;Html&gt; tags', Sanitize::escape('<Html> tags', ['flags' => ENT_QUOTES | ENT_HTML5]));
		$this->assertEquals('&lt;Html&gt; tags', Sanitize::escape('<Html> tags', ['flags' => ENT_QUOTES | ENT_XHTML]));
	}

}