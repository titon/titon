<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\helpers;

use titon\tests\TestCase;
use titon\tests\fixtures\HelperFixture;
use \Exception;

/**
 * Test class for titon\libs\helpers\Helper.
 */
class HelperTest extends TestCase {

	/**
	 * Initialize helper.
	 */
	protected function setUp() {
		$this->object = new HelperFixture();
	}

	/**
	 * Test that attributes() generates the correct HTML attributes, taking into accord escaping and removal.
	 */
	public function testAttributes() {
		$this->assertEquals('', $this->object->attributes([]));
		$this->assertEquals(' key="value"', $this->object->attributes(['key' => 'value']));
		$this->assertEquals(' foo="bar" key="value" number="1"', $this->object->attributes(['key' => 'value', 'foo' => 'bar', 'number' => 1]));
		$this->assertEquals(' bool="1" key="value" null=""', $this->object->attributes(['key' => 'value', 'null' => null, 'bool' => true]));

		// escaping
		$this->assertEquals(' double="&quot;quotes&quot;" key="value" single="&#039;quotes&#039;"', $this->object->attributes(['key' => 'value', 'double' => '"quotes"', 'single' => "'quotes'"]));
		$this->assertEquals(' double=""quotes"" key="value" single="\'quotes\'"', $this->object->attributes(['key' => 'value', 'double' => '"quotes"', 'single' => "'quotes'", 'escape' => false]));
		$this->assertEquals(' double=""quotes"" key="value" single="&#039;quotes&#039;"', $this->object->attributes(['key' => 'value', 'double' => '"quotes"', 'single' => "'quotes'", 'escape' => ['double']]));
		$this->assertEquals(' double="&quot;quotes&quot;" key="value" single="\'quotes\'"', $this->object->attributes(['key' => 'value', 'double' => '"quotes"', 'single' => "'quotes'", 'escape' => ['single']]));
		$this->assertEquals(' double=""quotes"" key="value" single="\'quotes\'"', $this->object->attributes(['key' => 'value', 'double' => '"quotes"', 'single' => "'quotes'", 'escape' => ['single', 'double']]));

		// remove
		$this->assertEquals('', $this->object->attributes(['key' => 'value'], ['key']));
		$this->assertEquals(' foo="bar" number="1"', $this->object->attributes(['key' => 'value', 'foo' => 'bar', 'number' => 1], ['key']));
		$this->assertEquals(' bool="1"', $this->object->attributes(['key' => 'value', 'null' => null, 'bool' => true], ['key', 'null']));
	}

	/**
	 * Test that escape() escapes and toggles when necessary.
	 */
	public function testEscape() {
		$value = 'This is "double" and \'single\' quotes.';

		$this->assertEquals('This is &quot;double&quot; and &#039;single&#039; quotes.', $this->object->escape($value));
		$this->assertEquals('This is "double" and \'single\' quotes.', $this->object->escape($value, false));

		$this->object->config->escape = false;
		$this->assertEquals('This is "double" and \'single\' quotes.', $this->object->escape($value));
		$this->assertEquals('This is &quot;double&quot; and &#039;single&#039; quotes.', $this->object->escape($value, true));

		$this->object->config->escape = true;
		$this->assertEquals('This is &quot;double&quot; and &#039;single&#039; quotes.', $this->object->escape($value));
	}

	/**
	 * Test that tag() places params in the correct sequence.
	 */
	public function testTag() {
		$this->assertEquals('<tag>{body}</tag>' . PHP_EOL, $this->object->tag('noattr'));
		$this->assertEquals('<tag>body</tag>' . PHP_EOL, $this->object->tag('noattr', ['fake' => 'attr', 'body' => 'body']));
		$this->assertEquals('<tag value />' . PHP_EOL, $this->object->tag('nobody', ['attr' => ' value']));
		$this->assertEquals('<tag 1 2>3</tag>' . PHP_EOL, $this->object->tag('custom', ['one' => 1, 'two' => 2, 'three' => 3]));
		$this->assertEquals('<tag 1>2</tag>3' . PHP_EOL, $this->object->tag('default', [1, 2, 3]));
	}

	/**
	 * Test that url() returns generated router URLs.
	 */
	public function testUrl() {
		$this->assertEquals('/', $this->object->url());
		$this->assertEquals('/', $this->object->url('/'));
		$this->assertEquals('/static/url', $this->object->url('/static/url'));
		$this->assertEquals('/pages/index/action', $this->object->url(['action' => 'action']));
		$this->assertEquals('/pages/index/index/123/abc', $this->object->url([123, 'abc']));
	}

}