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
use titon\utility\Inflector;

/**
 * Test class for titon\utility\Inflector.
 */
class InflectorTest extends TestCase {

	/**
	 * Test that strings are returned as camel case.
	 */
	public function testCamelCase() {
		$camelCase = [
			'foo Bar', 'fOo Bar', 'foo_Bar', ' foo-_--_BAR',
			'foo-BAR', 'FOO-BAR', 'foo     bar   '
		];

		foreach ($camelCase as $value) {
			$this->assertEquals('FooBar', Inflector::camelCase($value));
		}
	}

	/**
	 * Test that strings are returned as formatted filenames (including extension).
	 */
	public function testFilename() {
		$this->assertEquals('CamelCase.php', Inflector::filename('camel Case'));
		$this->assertEquals('StudlyCase.php', Inflector::filename('StuDly CaSe'));
		$this->assertEquals('TitleCase.php', Inflector::filename('Title Case'));
		$this->assertEquals('NormalCase.php', Inflector::filename('Normal case'));
		$this->assertEquals('Lowercase.php', Inflector::filename('lowercase'));
		$this->assertEquals('Uppercase.php', Inflector::filename('UPPERCASE'));
		$this->assertEquals('UnderScore.php', Inflector::filename('under_score'));
		$this->assertEquals('DashEs.php', Inflector::filename('dash-es'));
		$this->assertEquals('123Numbers.php', Inflector::filename('123 numbers'));
		$this->assertEquals('WithExt.php', Inflector::filename('with EXT.xml'));
		$this->assertEquals('LotsOfWhiteSpace.php', Inflector::filename('lots  of     white space'));

		// Alternate extension and lowercase first character
		$this->assertEquals('camelCase.xml', Inflector::filename('camel Case', 'xml', false));
		$this->assertEquals('studlyCase.xml', Inflector::filename('StuDly CaSe', 'xml', false));
		$this->assertEquals('titleCase.xml', Inflector::filename('Title Case', 'xml', false));
		$this->assertEquals('normalCase.xml', Inflector::filename('Normal case', 'xml', false));
		$this->assertEquals('lowercase.xml', Inflector::filename('lowercase', 'xml', false));
		$this->assertEquals('uppercase.xml', Inflector::filename('UPPERCASE', 'xml', false));
		$this->assertEquals('underScore.xml', Inflector::filename('under_score', 'xml', false));
		$this->assertEquals('dashEs.xml', Inflector::filename('dash-es', 'xml', false));
		$this->assertEquals('123Numbers.xml', Inflector::filename('123 numbers', 'xml', false));
		$this->assertEquals('withExt.xml', Inflector::filename('with EXT.xml', 'xml', false));
		$this->assertEquals('lotsOfWhiteSpace.xml', Inflector::filename('lots  of     white space', 'xml', false));
	}

	public function testModelize() {

	}

	/**
	 * Test that strings are returned as normal case sentences.
	 */
	public function testNormalCase() {
		$this->assertEquals('This is a string with studly case', Inflector::normalCase('This is A sTring wIth sTudly cAse'));
		$this->assertEquals('And this one has underscores', Inflector::normalCase('and_this_ONE_has_underscores'));
		$this->assertEquals('While this one contains -- dashes', Inflector::normalCase('WHILE this one contains -- DASHES'));
		$this->assertEquals('This is a mix of underscores -- and dashes', Inflector::normalCase('This_is A_MIX oF undeRscores -- aNd_dashes'));
		$this->assertEquals('Lastly, this string contains "punctuation"!', Inflector::normalCase('LaStlY, this STRING contains "punctuation"!'));
	}

	public function testPluralize() {

	}

	public function testSingularize() {

	}

	/**
	 * Test that strings are returned slugged.
	 */
	public function testSlug() {
		$this->assertEquals('this-is-a-string-with-studly-case', Inflector::slug('This is A sTring wIth sTudly cAse'));
		$this->assertEquals('andthisonehasunderscores', Inflector::slug('and_this_ONE_has_underscores'));
		$this->assertEquals('while-this-one-contains-__-dashes', Inflector::slug('WHILE this one contains -- DASHES'));
		$this->assertEquals('thisis-amix-of-underscores-__-anddashes', Inflector::slug('This_is A_MIX oF undeRscores -- aNd_dashes'));
		$this->assertEquals('lastly-this-string-contains-punctuation', Inflector::slug('LaStlY, this STRING contains "punctuation"!'));
	}

	public function testTableize() {

	}

	/**
	 * Test that strings are returned as title case sentences.
	 */
	public function testTitleCase() {
		$this->assertEquals('This Is A String With Studly Case', Inflector::titleCase('This is A sTring wIth sTudly cAse'));
		$this->assertEquals('And This One Has Underscores', Inflector::titleCase('and_this_ONE_has_underscores'));
		$this->assertEquals('While This One Contains -- Dashes', Inflector::titleCase('WHILE this one contains -- DASHES'));
		$this->assertEquals('This Is A Mix Of Underscores -- And Dashes', Inflector::titleCase('This_is A_MIX oF undeRscores -- aNd_dashes'));
		$this->assertEquals('Lastly, This String Contains "punctuation"!', Inflector::titleCase('LaStlY, this STRING contains "punctuation"!'));
	}

	/**
	 * Test that strings are returned as underscored slugs.
	 */
	public function testUnderscore() {
		$this->assertEquals('camel_case', Inflector::underscore('camel Case'));
		$this->assertEquals('stu_dly_ca_se', Inflector::underscore('StuDly CaSe'));
		$this->assertEquals('title_case', Inflector::underscore('Title Case'));
		$this->assertEquals('normal_case', Inflector::underscore('Normal case'));
		$this->assertEquals('lowercase', Inflector::underscore('lowercase'));
		$this->assertEquals('u_p_p_e_r_c_a_s_e', Inflector::underscore('UPPERCASE'));
		$this->assertEquals('under_score', Inflector::underscore('under_score'));
		$this->assertEquals('dashes', Inflector::underscore('dash-es'));
		$this->assertEquals('123_numbers', Inflector::underscore('123 numbers'));
		$this->assertEquals('with_e_x_txml', Inflector::underscore('with EXT.xml'));
		$this->assertEquals('lots_of_white_space', Inflector::underscore('lots  of     white space'));
	}

}
