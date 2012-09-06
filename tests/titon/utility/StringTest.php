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
use titon\utility\String;
use titon\utility\UtilityException;

/**
 * Test class for titon\utility\String.
 */
class StringTest extends TestCase {

	/**
	 * Test strings.
	 */
	public $string = 'Titon: A PHP 5.4 Modular Framework';
	public $lipsum = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi eget tellus nibh.';

	/**
	 * Test that charAt() returns the character at the index in the string.
	 */
	public function testCharAt() {
		$this->assertEquals('T', String::charAt('Titon', 0));
		$this->assertEquals('i', String::charAt('Titon', 1));
		$this->assertEquals('t', String::charAt('Titon', 2));
		$this->assertEquals('o', String::charAt('Titon', 3));
		$this->assertEquals('n', String::charAt('Titon', 4));
		$this->assertEquals(null, String::charAt('Titon', 5));
		$this->assertEquals(null, String::charAt('Titon', -1));
	}

	/**
	 * Test that contains() returns true if the needle is found within the string.
	 */
	public function testContains() {
		$this->assertTrue(String::contains($this->string, 'Titon'));
		$this->assertFalse(String::contains($this->string, 'Zend'));

		// case-insensitive
		$this->assertTrue(String::contains($this->string, 'TITON', false));

		// offset
		$this->assertFalse(String::contains($this->string, 'Titon', true, 5));
	}

	/**
	 * Test that endsWith() returns true if the end of the string matches the needle.
	 */
	public function testEndsWith() {
		$this->assertTrue(String::endsWith($this->string, 'work'));
		$this->assertFalse(String::endsWith($this->string, 'works'));

		// case-insensitive
		$this->assertTrue(String::endsWith($this->string, 'WORK', false));
		$this->assertFalse(String::endsWith($this->string, 'WORKS', false));
	}

	/**
	 * Test that extract() returns certain sections of a string.
	 */
	public function testExtract() {
		$this->assertEquals('Titon: A PHP 5.4 Modular Framework', String::extract($this->string, 0));
		$this->assertEquals('Titon', String::extract($this->string, 0, 5));
		$this->assertEquals('Framework', String::extract($this->string, -9));
		$this->assertEquals('Frame', String::extract($this->string, -9, 5));
		$this->assertEquals('Modular', String::extract($this->string, 17, 7));
	}

	/**
	 * Test that generate() returns a string of random characters.
	 */
	public function testGenerate() {
		$this->assertNotEquals('ABCDEFGHIJ', String::generate(10));
		$this->assertTrue(strlen(String::generate(10)) == 10);

		$this->assertNotEquals('ABCDEFGHIJ12345', String::generate(15));
		$this->assertTrue(strlen(String::generate(15)) == 15);

		$this->assertEquals('aaaaa', String::generate(5, 'aaaaa'));
		$this->assertTrue(strlen(String::generate(5)) == 5);
	}

	/**
	 * Test that indexOf() returns that index of the first matched character.
	 */
	public function testIndexOf() {
		$this->assertEquals(0, String::indexOf($this->string, 'T'));
		$this->assertEquals(2, String::indexOf($this->string, 't'));
		$this->assertEquals(7, String::indexOf($this->string, 'A'));
		$this->assertEquals(13, String::indexOf($this->string, '5'));
		$this->assertEquals(17, String::indexOf($this->string, 'M'));
		$this->assertEquals(25, String::indexOf($this->string, 'F'));

		// case-insensitive
		$this->assertEquals(0, String::indexOf($this->string, 'T', false));
		$this->assertEquals(0, String::indexOf($this->string, 't', false));

		// offset
		$this->assertEquals(3, String::indexOf($this->string, 'o'));
		$this->assertEquals(18, String::indexOf($this->string, 'o', true, 5));
	}

	/**
	 * Test that insert() replaces tokens with their value.
	 */
	public function testInsert() {
		$this->assertEquals('Titon is the best PHP framework around!', String::insert('{framework} is the best {lang} framework around!', [
			'framework' => 'Titon',
			'lang' => 'PHP'
		]));

		$this->assertEquals('Titon is the best PHP framework around!', String::insert(':framework is the best :lang framework around!', [
			'framework' => 'Titon',
			'lang' => 'PHP'
		], [
			'before' => ':',
			'after' => ''
		]));
	}

	/**
	 * Test that lastIndexOf() returns that index of the last matched character.
	 */
	public function testLastIndexOf() {
		$this->assertEquals(0, String::lastIndexOf($this->string, 'T'));
		$this->assertEquals(2, String::lastIndexOf($this->string, 't'));
		$this->assertEquals(7, String::lastIndexOf($this->string, 'A'));
		$this->assertEquals(13, String::lastIndexOf($this->string, '5'));
		$this->assertEquals(17, String::lastIndexOf($this->string, 'M'));
		$this->assertEquals(25, String::lastIndexOf($this->string, 'F'));

		// case-insensitive
		$this->assertEquals(2, String::lastIndexOf($this->string, 'T', false));
		$this->assertEquals(28, String::lastIndexOf($this->string, 'M', false));
		$this->assertEquals(28, String::lastIndexOf($this->string, 'm', false));

		// offset
		$this->assertEquals(31, String::lastIndexOf($this->string, 'o'));
		$this->assertEquals(31, String::lastIndexOf($this->string, 'o', true, 5));
	}

	/**
	 * Test that listing() returns a comma separate list of items as a string.
	 */
	public function testListing() {
		$this->assertEquals('red, blue &amp; green', String::listing(['red', 'blue', 'green']));
		$this->assertEquals('red &amp; green', String::listing(['red', 'green']));
		$this->assertEquals('blue', String::listing(['blue']));
		$this->assertEquals('green', String::listing('green'));

		// custom
		$this->assertEquals('red, blue, and green', String::listing(['red', 'blue', 'green'], ', and '));
		$this->assertEquals('red - blue and green', String::listing(['red', 'blue', 'green'], ' and ', ' - '));
	}

	/**
	 * Test that shorten() returns a shortened string (in the middle) if it is too long.
	 */
	public function testShorten() {
		$this->assertEquals('Lorem &hellip; nibh.', String::shorten($this->lipsum, 10));
		$this->assertEquals('Lorem ipsum &hellip; tellus nibh.', String::shorten($this->lipsum, 25));
		$this->assertEquals('Lorem ipsum dolor sit &hellip; Morbi eget tellus nibh.', String::shorten($this->lipsum, 50));
		$this->assertEquals($this->lipsum, String::shorten($this->lipsum, 100));

		// custom
		$this->assertEquals('Lorem ... nibh.', String::shorten($this->lipsum, 10, ' ... '));
	}

	/**
	 * Test that startsWith() returns true if the start of the string matches the needle.
	 */
	public function testStartsWith() {
		$this->assertTrue(String::startsWith($this->string, 'Titon'));
		$this->assertFalse(String::startsWith($this->string, 'Titan'));

		// case-insensitive
		$this->assertTrue(String::startsWith($this->string, 'TITON', false));
		$this->assertFalse(String::startsWith($this->string, 'TITAN', false));
	}

	/**
	 * Test that truncate() trims text while preserving HTML and trailing words.
	 */
	public function testTruncate() {
		$string = 'This has <a href="/" class="link">anchor tags</a> &amp; entities in it. It has &quot;quotes&quot; as well.';

		// Preserve HTML and word
		$this->assertEquals('This has <a href="/" class="link">anchor</a>&hellip;', String::truncate($string, 15));
		$this->assertEquals('This has <a href="/" class="link">anchor tags</a> &amp;&hellip;', String::truncate($string, 25));
		$this->assertEquals('This has <a href="/" class="link">anchor tags</a> &amp; entities in&hellip;', String::truncate($string, 35));
		$this->assertEquals('This has <a href="/" class="link">anchor tags</a> &amp; entities in it. It&hellip;', String::truncate($string, 45));
		$this->assertEquals('This has <a href="/" class="link">anchor tags</a> &amp; entities in it. It has &quot;quotes&quot; as well.', String::truncate($string, false));

		// Preserve HTML
		$this->assertEquals('This has <a href="/" class="link">anch</a>&hellip;', String::truncate($string, 13, ['word' => false]));
		$this->assertEquals('This has <a href="/" class="link">anchor tags</a> &amp; en&hellip;', String::truncate($string, 25, ['word' => false]));
		$this->assertEquals('This has <a href="/" class="link">anchor tags</a> &amp; entities in&hellip;', String::truncate($string, 35, ['word' => false]));
		$this->assertEquals('This has <a href="/" class="link">anchor tags</a> &amp; entities in it. It has&hellip;', String::truncate($string, 45, ['word' => false]));
		$this->assertEquals('This has <a href="/" class="link">anchor tags</a> &amp; entities in it. It has &quot;quotes&quot; as well.', String::truncate($string, false, ['word' => false]));

		// Preserve none
		$this->assertEquals('This has anchor tags &amp; en&hellip;', String::truncate($string, 25, ['word' => false, 'html' => false]));
		$this->assertEquals('This has anchor tags &amp; entities in&hellip;', String::truncate($string, 35, ['word' => false, 'html' => false]));
		$this->assertEquals('This has anchor tags &amp; entities in it. It has&hellip;', String::truncate($string, 45, ['word' => false, 'html' => false]));
		$this->assertEquals('This has anchor tags &amp; entities in it. It has &quot;quotes&quot; as well.', String::truncate($string, false, ['word' => false, 'html' => false]));

		// Preserve none with custom suffix
		$this->assertEquals('This has anchor tags &amp; en...', String::truncate($string, 25, ['word' => false, 'html' => false, 'suffix' => '...']));
		$this->assertEquals('This has anchor tags &amp; entities in...', String::truncate($string, 35, ['word' => false, 'html' => false, 'suffix' => '...']));
		$this->assertEquals('This has anchor tags &amp; entities in it. It has...', String::truncate($string, 45, ['word' => false, 'html' => false, 'suffix' => '...']));
		$this->assertEquals('This has anchor tags &amp; entities in it. It has &quot;quotes&quot; as well.', String::truncate($string, false, ['word' => false, 'html' => false, 'suffix' => '...']));

		// Custom tags (BB code)
		$string = 'This has [url="/"]anchor tags[/url] &amp; entities in it. It has &quot;quotes&quot; as well.';

		$this->assertEquals('This has [url="/"]anchor[/url]&hellip;', String::truncate($string, 15, ['open' => '[', 'close' => ']']));
		$this->assertEquals('This has [url="/"]anchor tags[/url] &amp;&hellip;', String::truncate($string, 25, ['open' => '[', 'close' => ']']));
		$this->assertEquals('This has [url="/"]anchor tags[/url] &amp; entities in&hellip;', String::truncate($string, 35, ['open' => '[', 'close' => ']']));
		$this->assertEquals('This has [url="/"]anchor tags[/url] &amp; entities in it. It&hellip;', String::truncate($string, 45, ['open' => '[', 'close' => ']']));
		$this->assertEquals('This has [url="/"]anchor tags[/url] &amp; entities in it. It has &quot;quotes&quot; as well.', String::truncate($string, false, ['open' => '[', 'close' => ']']));
	}

}