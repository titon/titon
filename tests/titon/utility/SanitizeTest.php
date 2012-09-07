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

/**
 * Test class for titon\utility\Sanitize.
 */
class SanitizeTest extends TestCase {

	/**
	 * Test that email() removes unwanted characters.
	 */
	public function testEmail() {
		$this->assertEquals('email@domain.com', Sanitize::email('em<a>il@domain.com'));
		$this->assertEquals('email+tag@domain.com', Sanitize::email('email+t(a)g@domain.com'));
		$this->assertEquals('email+tag@domain.com', Sanitize::email('em"ail+t(a)g@domain.com'));
	}

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

	/**
	 * Test that float() removes unwanted characters.
	 */
	public function testFloat() {
		$this->assertEquals(100.25, Sanitize::float('1[0]0.25'));
		$this->assertEquals(-125.55, Sanitize::float('-abc125.55'));
		$this->assertEquals(1203.11, Sanitize::float('+1203.11'));
	}

	/**
	 * Test that html() strips tags and escapes entities.
	 */
	public function testHtml() {
		$this->assertEquals('String with b &amp; i tags.', Sanitize::html('String <b>with</b> b & i <i>tags</i>.'));
		$this->assertEquals('String &lt;b&gt;with&lt;/b&gt; b &amp; i &lt;i&gt;tags&lt;/i&gt;.', Sanitize::html('String <b>with</b> b & i <i>tags</i>.', ['strip' => false]));
		$this->assertEquals('String &lt;b&gt;with&lt;/b&gt; b &amp; i tags.', Sanitize::html('String <b>with</b> b & i <i>tags</i>.', ['whitelist' => '<b>']));
		$this->assertEquals('String with b &amp;amp; i tags.', Sanitize::html('String <b>with</b> b &amp; i <i>tags</i>.', ['double' => true]));
	}

	/**
	 * Test that integer() removes unwanted characters.
	 */
	public function testInteger() {
		$this->assertEquals(1292932, Sanitize::integer('129sdja2932'));
		$this->assertEquals(-1275452, Sanitize::integer('-12,754.52'));
		$this->assertEquals(18840, Sanitize::integer('+18#840'));
	}

	/**
	 * Test that newlines() removes extraneous CRLF.
	 */
	public function testNewlines() {
		$this->assertEquals("Testing\rCarriage\rReturns", Sanitize::newlines("Testing\rCarriage\r\rReturns"));
		$this->assertEquals("Testing\r\rCarriage\rReturns", Sanitize::newlines("Testing\r\rCarriage\r\r\rReturns", ['limit' => 3]));
		$this->assertEquals("TestingCarriageReturns", Sanitize::newlines("Testing\r\rCarriage\r\r\rReturns", ['limit' => 0]));

		$this->assertEquals("Testing\nLine\nFeeds", Sanitize::newlines("Testing\nLine\n\nFeeds"));
		$this->assertEquals("Testing\nLine\n\nFeeds", Sanitize::newlines("Testing\n\n\nLine\n\nFeeds", ['limit' => 3]));
		$this->assertEquals("TestingLineFeeds", Sanitize::newlines("Testing\n\nLine\n\nFeeds", ['limit' => 0]));

		$this->assertEquals("Testing\r\nBoth\r\nLineFeeds\r\n\r\nAnd\r\nCarriageReturns", Sanitize::newlines("Testing\r\nBoth\r\r\n\nLineFeeds\r\n\r\r\n\nAnd\r\nCarriageReturns"));
		$this->assertEquals("Testing\r\nBoth\r\nLineFeeds\r\nAnd\r\nCarriageReturns", Sanitize::newlines("Testing\r\nBoth\r\n\r\nLineFeeds\r\n\r\n\r\nAnd\r\nCarriageReturns"));
		$this->assertEquals("Testing\r\nBoth\r\n\r\nLineFeeds\r\n\r\n\r\nAnd\r\nCarriageReturns", Sanitize::newlines("Testing\r\nBoth\r\n\r\nLineFeeds\r\n\r\n\r\nAnd\r\nCarriageReturns", ['crlf' => false]));
	}

	/**
	 * Test that whitespace() removes extraneous whitespace.
	 */
	public function testWhitespace() {
		$this->assertEquals("Testing White Space", Sanitize::whitespace("Testing  White Space"));
		$this->assertEquals("Testing  White Space", Sanitize::whitespace("Testing  White    Space", ['limit' => 3]));
		$this->assertEquals("TestingWhiteSpace", Sanitize::whitespace("Testing  White    Space", ['limit' => 0]));

		$this->assertEquals("Testing\tTabs", Sanitize::whitespace("Testing\t\t\tTabs", ['tab' => true]));
		$this->assertEquals("Testing\t\tTabs", Sanitize::whitespace("Testing\t\tTabs", ['tab' => true, 'limit' => 3]));
		$this->assertEquals("TestingTabs", Sanitize::whitespace("Testing\tTabs", ['tab' => true, 'limit' => 0]));
	}

}