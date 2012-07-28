<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\base;

use titon\base\String;
use titon\tests\TestCase;

/**
 * Test class for titon\base\String.
 */
class StringTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new String('titon');
	}

	/**
	 * Test that append() appends a string to the end.
	 */
	public function testAppend() {
		$this->object->append(' framework');
		$this->assertEquals('titon framework', $this->object->value());
	}

	/**
	 * Test that capitalize() capitalizes the first char.
	 */
	public function testCapitalize() {
		$this->object->capitalize();
		$this->assertEquals('Titon', $this->object->value());
	}

	/**
	 * Test that charAt() returns the char at the index, or null.
	 */
	public function testCharAt() {
		$this->assertEquals('o', $this->object->charAt(3));
		$this->assertEquals(null, $this->object->charAt(10));
	}

	/**
	 * Test that clean() removes extraneous spaces.
	 */
	public function testClean() {
		$string = new String('  titon       framework' . PHP_EOL);
		$string->clean();

		$this->assertEquals('titon framework', $string->value());
	}

	/**
	 * Test that compare() compares the lengths of strings and returns the difference.
	 */
	public function testCompare() {
		$this->assertEquals(0, $this->object->compare('titon'));
		$this->assertEquals(-10, $this->object->compare('titon framework'));
		$this->assertEquals(2, $this->object->compare('tit'));
	}

	/**
	 * Test that concat() returns a new String object.
	 */
	public function testConcat() {
		$append = $this->object->concat(' append');

		$this->assertInstanceOf('titon\base\String', $append);
		$this->assertEquals('titon append', $append->value());

		$prepend = $this->object->concat('prepend ', false);

		$this->assertInstanceOf('titon\base\String', $prepend);
		$this->assertEquals('prepend titon', $prepend->value());
	}

	/**
	 * Test that contains() returns true if the value is found in the string.
	 */
	public function testContains() {
		$this->assertTrue($this->object->contains('tit'));
		$this->assertFalse($this->object->contains('tin'));
	}

	/**
	 * Test that endsWith() returns true if the string ends in the value.
	 */
	public function testEndsWith() {
		$this->assertTrue($this->object->endsWith('ton'));
		$this->assertFalse($this->object->endsWith('tan'));
	}

	/**
	 * Test that equals() returns true if the strings are equal.
	 */
	public function testEquals() {
		$this->assertTrue($this->object->equals('titon'));
		$this->assertFalse($this->object->equals('titan'));
	}

	/**
	 * Test that escape() escapes entities and HTML.
	 */
	public function testEscape() {
		$string = new String('"Titon"');
		$string->escape();

		$this->assertEquals('&quot;Titon&quot;', $string->value());
	}

	/**
	 * Test that extract() removes a portion of the string.
	 */
	public function testExtract() {
		$this->assertEquals('tit', $this->object->extract(0, 3));
		$this->assertEquals('ton', $this->object->extract(2));
		$this->assertEquals('on', $this->object->extract(-2));
	}

	/**
	 * Test that indexOf() returns the index of the char.
	 */
	public function testIndexOf() {
		$this->assertEquals(0, $this->object->indexOf('t'));
		$this->assertEquals(4, $this->object->indexOf('n'));
		$this->assertEquals(false, $this->object->indexOf('r'));
	}

	/**
	 * Test that isBlank() returns true if the string is empty (without trimming).
	 */
	public function testIsBlank() {
		$string = new String('   ');

		$this->assertFalse($string->isBlank());

		$string->trim();

		$this->assertTrue($string->isBlank());
	}

	/**
	 * Test that isEmpty() returns true if the string is empty (with trimming).
	 */
	public function testIsEmpty() {
		$string = new String('   ');

		$this->assertTrue($string->isEmpty());

		$string->set('foo');

		$this->assertFalse($string->isEmpty());
	}

	/**
	 * Test that isNotBlank() returns true if the string is not empty (without trimming).
	 */
	public function testIsNotBlank() {
		$string = new String('foo');

		$this->assertTrue($string->isNotBlank());

		$string->set('   ');

		$this->assertTrue($string->isNotBlank());
	}

	/**
	 * Test that isNotEmpty() returns true if the string is not empty (with trimming).
	 */
	public function testIsNotEmpty() {
		$string = new String('');

		$this->assertFalse($string->isNotEmpty());

		$string->set('   ');

		$this->assertFalse($string->isNotEmpty());
	}

	/**
	 * Test that lastIndexOf() returns the index of the last char found.
	 */
	public function testLastIndexOf() {
		$this->assertEquals(2, $this->object->lastIndexOf('t'));
		$this->assertEquals(4, $this->object->lastIndexOf('n'));
		$this->assertEquals(false, $this->object->lastIndexOf('r'));
	}

	/**
	 * Test that length() returns the length of the string.
	 */
	public function testLength() {
		$this->assertEquals(5, $this->object->length());

		$this->object->append(' framework');
		$this->assertEquals(15, $this->object->length());
	}

	/**
	 * Test that matches() returns the regex result.
	 */
	public function testMatches() {
		$this->assertEquals(1, $this->object->matches('/tit/'));
		$this->assertEquals(['tit'], $this->object->matches('/tit/', true));
		$this->assertEquals(0, $this->object->matches('/tat/'));
	}

	/**
	 * Test that pad() pads both sides with a token.
	 */
	public function testPad() {
		$this->assertEquals('--titon---', $this->object->pad(10, '-'));
	}

	/**
	 * Test that padLeft() pads the left side with a token.
	 */
	public function testPadLeft() {
		$this->assertEquals('-----titon', $this->object->padLeft(10, '-'));
	}

	/**
	 * Test that padRight() pads the right side with a token.
	 */
	public function testPadRight() {
		$this->assertEquals('titon-----', $this->object->padRight(10, '-'));
	}

	/**
	 * Test that prepend() prepends a string to the beginning.
	 */
	public function testPrepend() {
		$this->object->prepend('php framework: ');
		$this->assertEquals('php framework: titon', $this->object->value());
	}

	/**
	 * Test that replace() replaces parts of the string.
	 */
	public function testReplace() {
		$this->object->replace('tit', 'TIT');
		$this->assertEquals('TITon', $this->object->value());

		$this->object->replace('tit', 'tit');
		$this->assertEquals('TITon', $this->object->value());

		$this->object->replace('tit', 'tit', false);
		$this->assertEquals('titon', $this->object->value());
	}

	/**
	 * Test that reverse() reverses the string.
	 */
	public function testReverse() {
		$this->object->reverse();
		$this->assertEquals('notit', $this->object->value());
	}

	/**
	 * Test that shuffle() randomizes the chars.
	 */
	public function testShuffle() {
		$this->object->shuffle();
		$this->assertNotEquals('titon', $this->object->value());
	}

	/**
	 * Test that startsWith() returns true if the string starts with the value.
	 */
	public function testStartsWith() {
		$this->assertTrue($this->object->startsWith('tit'));
		$this->assertFalse($this->object->startsWith('tot'));
	}

	/**
	 * Test that set() will overwrite the string.
	 */
	public function testSet() {
		$this->object->set('framework');
		$this->assertEquals('framework', $this->object->value());
	}

	/**
	 * Test that strip() removes HTML.
	 */
	public function testStrip() {
		$this->object->set('<b>titon</b>')->strip();
		$this->assertEquals('titon', $this->object->value());
	}

	/**
	 * Test that split() will split the string into an array.
	 */
	public function testSplit() {
		$this->assertEquals(['t', 'i', 't', 'o', 'n'], $this->object->split());
		$this->assertEquals(['ti', 'to', 'n'], $this->object->split(null, 2));
		$this->assertEquals(['', 'i', 'on'], $this->object->split('t'));
	}

	/**
	 * Test that toCamelCase() will change the string to camel case.
	 */
	public function testToCamelCase() {
		$this->object->set('titon framework')->toCamelCase();
		$this->assertEquals('TitonFramework', $this->object->value());
	}

	/**
	 * Test that toLowerCase() will change the string to lowercase.
	 */
	public function testToLowerCase() {
		$this->object->set('TITON fRamEwork')->toLowerCase();
		$this->assertEquals('titon framework', $this->object->value());
	}

	/**
	 * Test that toUpperCase() will change the string to uppercase.
	 */
	public function testToUpperCase() {
		$this->object->set('titon framework')->toUpperCase();
		$this->assertEquals('TITON FRAMEWORK', $this->object->value());
	}

	/**
	 * Test that toUpperWords() will change all words first char to uppercase.
	 */
	public function testToUpperWords() {
		$this->object->set('titon framework')->toUpperWords();
		$this->assertEquals('Titon Framework', $this->object->value());
	}

	/**
	 * Test that trim() removes whitespace or chars.
	 */
	public function testTrim() {
		$this->object->set(' titon ')->trim();
		$this->assertEquals('titon', $this->object->value());

		$this->object->trim('t');
		$this->assertEquals('iton', $this->object->value());
	}

	/**
	 * Test that trimLeft() removes whitespace or chars on the left.
	 */
	public function testTrimLeft() {
		$this->object->set(' titon ')->trimLeft();
		$this->assertEquals('titon ', $this->object->value());

		$this->object->set('-titon-')->trimLeft('-');
		$this->assertEquals('titon-', $this->object->value());
	}

	/**
	 * Test that trimRight() removes whitespace or chars on the right.
	 */
	public function testTrimRight() {
		$this->object->set(' titon ')->trimRight();
		$this->assertEquals(' titon', $this->object->value());

		$this->object->set('-titon-')->trimRight('-');
		$this->assertEquals('-titon', $this->object->value());
	}

	/**
	 * Test that uncapitalize() will lowercase the first char.
	 */
	public function testUncapitalize() {
		$this->object->set('Titon Framework')->uncapitalize();
		$this->assertEquals('titon Framework', $this->object->value());
	}

	/**
	 * Test that wordCount() returns a number of how many words in the string.
	 */
	public function testWordCount() {
		$this->assertEquals(1, $this->object->wordCount());

		$this->object->set('Titon Framework');
		$this->assertEquals(2, $this->object->wordCount());
	}

}