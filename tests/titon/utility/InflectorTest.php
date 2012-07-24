<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\utility;

use titon\Titon;
use titon\tests\TestCase;
use titon\utility\Inflector;

/**
 * Test class for titon\utility\Inflector.
 */
class InflectorTest extends TestCase {

	/**
	 * Prepare G11n.
	 */
	protected function setUp() {
		Titon::g11n()->setup('en')->setup('en-us')->set('en');
	}

	/**
	 * Test that camelCase() returns strings as camel case.
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
	 * Test that strings are returned as formatted file names (including extension).
	 */
	public function testFilename() {
		$this->assertEquals('CamelCase.php', Inflector::fileName('camel Case'));
		$this->assertEquals('StudlyCase.php', Inflector::fileName('StuDly CaSe'));
		$this->assertEquals('TitleCase.php', Inflector::fileName('Title Case'));
		$this->assertEquals('NormalCase.php', Inflector::fileName('Normal case'));
		$this->assertEquals('Lowercase.php', Inflector::fileName('lowercase'));
		$this->assertEquals('Uppercase.php', Inflector::fileName('UPPERCASE'));
		$this->assertEquals('UnderScore.php', Inflector::fileName('under_score'));
		$this->assertEquals('DashEs.php', Inflector::fileName('dash-es'));
		$this->assertEquals('123Numbers.php', Inflector::fileName('123 numbers'));
		$this->assertEquals('WithExt.php', Inflector::fileName('with EXT.xml'));
		$this->assertEquals('LotsOfWhiteSpace.php', Inflector::fileName('lots  of     white space'));

		// Alternate extension and lowercase first character
		$this->assertEquals('camelCase.xml', Inflector::fileName('camel Case', 'xml', false));
		$this->assertEquals('studlyCase.xml', Inflector::fileName('StuDly CaSe', 'xml', false));
		$this->assertEquals('titleCase.xml', Inflector::fileName('Title Case', 'xml', false));
		$this->assertEquals('normalCase.xml', Inflector::fileName('Normal case', 'xml', false));
		$this->assertEquals('lowercase.xml', Inflector::fileName('lowercase', 'xml', false));
		$this->assertEquals('uppercase.xml', Inflector::fileName('UPPERCASE', 'xml', false));
		$this->assertEquals('underScore.xml', Inflector::fileName('under_score', 'xml', false));
		$this->assertEquals('dashEs.xml', Inflector::fileName('dash-es', 'xml', false));
		$this->assertEquals('123Numbers.xml', Inflector::fileName('123 numbers', 'xml', false));
		$this->assertEquals('withExt.xml', Inflector::fileName('with EXT.xml', 'xml', false));
		$this->assertEquals('lotsOfWhiteSpace.xml', Inflector::fileName('lots  of     white space', 'xml', false));
	}

	/**
	 * Test that className() returns a singular camel cased form.
	 */
	public function testClassName() {
		$this->assertEquals('CamelCase', Inflector::className('camel Case'));
		$this->assertEquals('StudlyCase', Inflector::className('StuDly CaSe'));
		$this->assertEquals('TitleCase', Inflector::className('Title Case'));
		$this->assertEquals('NormalCase', Inflector::className('Normal case'));
		$this->assertEquals('Lowercase', Inflector::className('lowercase'));
		$this->assertEquals('Uppercase', Inflector::className('UPPERCASE'));
		$this->assertEquals('UnderScore', Inflector::className('under_score'));
		$this->assertEquals('DashE', Inflector::className('dash-es'));
		$this->assertEquals('123Number', Inflector::className('123 numbers'));
		$this->assertEquals('WithExtxml', Inflector::className('with EXT.xml'));
		$this->assertEquals('LotsOfWhiteSpace', Inflector::className('lots  of     white space'));

		// real words
		$this->assertEquals('Person', Inflector::className('people'));
		$this->assertEquals('Man', Inflector::className('men'));
		$this->assertEquals('Atlas', Inflector::className('atlases'));
		$this->assertEquals('Octopus', Inflector::className('octopus'));
		$this->assertEquals('Ox', Inflector::className('OX'));
		$this->assertEquals('Ox', Inflector::className('oXeN'));
	}

	/**
	 * Test that normalCase() returns strings as normal case sentences.
	 */
	public function testNormalCase() {
		$this->assertEquals('This is a string with studly case', Inflector::normalCase('This is A sTring wIth sTudly cAse'));
		$this->assertEquals('And this one has underscores', Inflector::normalCase('and_this_ONE_has_underscores'));
		$this->assertEquals('While this one contains -- dashes', Inflector::normalCase('WHILE this one contains -- DASHES'));
		$this->assertEquals('This is a mix of underscores -- and dashes', Inflector::normalCase('This_is A_MIX oF undeRscores -- aNd_dashes'));
		$this->assertEquals('Lastly, this string contains "punctuation"!', Inflector::normalCase('LaStlY, this STRING contains "punctuation"!'));
	}

	/**
	 * Test that ordinal() returns the number with the proper suffix.
	 */
	public function testOrdinal() {
		$this->assertEquals('1st', Inflector::ordinal(1));
		$this->assertEquals('2nd', Inflector::ordinal(2));
		$this->assertEquals('3rd', Inflector::ordinal(3));
		$this->assertEquals('4th', Inflector::ordinal(4));
		$this->assertEquals('5th', Inflector::ordinal(5));

		// teens
		$this->assertEquals('12th', Inflector::ordinal(12));
		$this->assertEquals('15th', Inflector::ordinal(15));
		$this->assertEquals('18th', Inflector::ordinal(18));
		$this->assertEquals('20th', Inflector::ordinal(20));

		// high numbers
		$this->assertEquals('91st', Inflector::ordinal(91));
		$this->assertEquals('342nd', Inflector::ordinal(342));
		$this->assertEquals('8534th', Inflector::ordinal(8534));
		$this->assertEquals('92343rd', Inflector::ordinal(92343));
		$this->assertEquals('678420th', Inflector::ordinal(678420));

		// casting
		$this->assertEquals('11th', Inflector::ordinal('11th'));
		$this->assertEquals('98th', Inflector::ordinal('98s'));
		$this->assertEquals('438th', Inflector::ordinal('438lbs'));
		$this->assertEquals('-12th', Inflector::ordinal('-12$'));
	}

	/**
	 * Test that pluralize() returns a plural form, respecting irregularities and other locale specific rules.
	 */
	public function testPluralize() {
		// irregular
		$this->assertEquals('opuses', Inflector::pluralize('opus'));
		$this->assertEquals('penises', Inflector::pluralize('penis'));
		$this->assertEquals('loaves', Inflector::pluralize('loaf'));
		$this->assertEquals('mythoi', Inflector::pluralize('mythos'));
		$this->assertEquals('men', Inflector::pluralize('man'));

		// uninflected
		$this->assertEquals('information', Inflector::pluralize('information'));
		$this->assertEquals('corps', Inflector::pluralize('corps'));
		$this->assertEquals('gallows', Inflector::pluralize('gallows'));
		$this->assertEquals('maltese', Inflector::pluralize('maltese'));
		$this->assertEquals('rice', Inflector::pluralize('rice'));

		// plural
		$this->assertEquals('matrices', Inflector::pluralize('matrix'));
		$this->assertEquals('buses', Inflector::pluralize('bus'));
		$this->assertEquals('perches', Inflector::pluralize('perch'));
		$this->assertEquals('people', Inflector::pluralize('person'));
		$this->assertEquals('bananas', Inflector::pluralize('banana'));

		// already plural
		$this->assertEquals('opuses', Inflector::pluralize('opuses'));
		$this->assertEquals('penises', Inflector::pluralize('penises'));
		$this->assertEquals('loaves', Inflector::pluralize('loaves'));
		$this->assertEquals('mythoi', Inflector::pluralize('mythoi'));
		$this->assertEquals('men', Inflector::pluralize('men'));
	}

	/**
	 * Test that route() returns a URL friendly slug.
	 */
	public function testRoute() {
		$this->assertEquals('camel-Case', Inflector::route('camel Case'));
		$this->assertEquals('StuDly-CaSe', Inflector::route('StuDly CaSe'));
		$this->assertEquals('Title-Case', Inflector::route('Title Case'));
		$this->assertEquals('Normal-case', Inflector::route('Normal case'));
		$this->assertEquals('lowercase', Inflector::route('lowercase'));
		$this->assertEquals('UPPERCASE', Inflector::route('UPPERCASE'));
		$this->assertEquals('under-score', Inflector::route('under_score'));
		$this->assertEquals('dash-es', Inflector::route('dash-es'));
		$this->assertEquals('123-numbers', Inflector::route('123 numbers'));
		$this->assertEquals('with-EXTxml', Inflector::route('with EXT.xml'));
		$this->assertEquals('lots-of-white-space', Inflector::route('lots  of     white space'));
	}

	/**
	 * Test that singularize() returns a single form, respecting irregularities and other locale specific rules.
	 */
	public function testSingularize() {
		// irregular
		$this->assertEquals('atlas', Inflector::singularize('atlases'));
		$this->assertEquals('corpus', Inflector::singularize('corpuses'));
		$this->assertEquals('octopus', Inflector::singularize('octopuses'));
		$this->assertEquals('ox', Inflector::singularize('oxen'));
		$this->assertEquals('goose', Inflector::singularize('geese'));

		// uninflected
		$this->assertEquals('money', Inflector::singularize('money'));
		$this->assertEquals('flounder', Inflector::singularize('flounder'));
		$this->assertEquals('moose', Inflector::singularize('moose'));
		$this->assertEquals('species', Inflector::singularize('species'));
		$this->assertEquals('wildebeest', Inflector::singularize('wildebeest'));

		// singular
		$this->assertEquals('quiz', Inflector::singularize('quizzes'));
		$this->assertEquals('alias', Inflector::singularize('aliases'));
		$this->assertEquals('shoe', Inflector::singularize('shoes'));
		$this->assertEquals('person', Inflector::singularize('people'));
		$this->assertEquals('apple', Inflector::singularize('apples'));

		// already singular
		$this->assertEquals('atlas', Inflector::singularize('atlas'));
		$this->assertEquals('corpus', Inflector::singularize('corpus'));
		$this->assertEquals('octopus', Inflector::singularize('octopus'));
		$this->assertEquals('ox', Inflector::singularize('ox'));
		$this->assertEquals('goose', Inflector::singularize('goose'));
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

	/**
	 * Test that tableName() returns a plural lower-camel-cased form.
	 */
	public function testTableName() {
		$this->assertEquals('camelCases', Inflector::tableName('camel Case'));
		$this->assertEquals('studlyCases', Inflector::tableName('StuDly CaSe'));
		$this->assertEquals('titleCases', Inflector::tableName('Title Case'));
		$this->assertEquals('normalCases', Inflector::tableName('Normal case'));
		$this->assertEquals('lowercases', Inflector::tableName('lowercase'));
		$this->assertEquals('uppercases', Inflector::tableName('UPPERCASE'));
		$this->assertEquals('underScores', Inflector::tableName('under_score'));
		$this->assertEquals('dashEs', Inflector::tableName('dash-es'));
		$this->assertEquals('123Numbers', Inflector::tableName('123 numbers'));
		$this->assertEquals('withExtxmls', Inflector::tableName('with EXT.xml'));
		$this->assertEquals('lotsOfWhiteSpaces', Inflector::tableName('lots  of     white space'));

		// real words
		$this->assertEquals('people', Inflector::tableName('people'));
		$this->assertEquals('women', Inflector::tableName('woman'));
		$this->assertEquals('users', Inflector::tableName('User'));
		$this->assertEquals('octopuses', Inflector::tableName('octopus'));
		$this->assertEquals('oxen', Inflector::tableName('OX'));
		$this->assertEquals('oxen', Inflector::tableName('oXeN'));
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
	 * Test that transliterate() replaces non-ASCII chars.
	 */
	public function testTransliterate() {
		$this->assertEquals('Ingles', Inflector::transliterate('Inglés'));
		$this->assertEquals('Uber', Inflector::transliterate('Über'));
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

	/**
	 * Test that variable() returns strings as acceptable $variable names.
	 */
	public function testVariable() {
		$this->assertEquals('camelCase', Inflector::variable('camel Case'));
		$this->assertEquals('StuDlyCaSe', Inflector::variable('StuDly CaSe'));
		$this->assertEquals('TitleCase', Inflector::variable('Title Case'));
		$this->assertEquals('Normalcase', Inflector::variable('Normal case'));
		$this->assertEquals('lowercase', Inflector::variable('lowercase'));
		$this->assertEquals('UPPERCASE', Inflector::variable('UPPERCASE'));
		$this->assertEquals('under_score', Inflector::variable('under_score'));
		$this->assertEquals('dashes', Inflector::variable('dash-es'));
		$this->assertEquals('_123numbers', Inflector::variable('123 numbers'));
		$this->assertEquals('withEXTxml', Inflector::variable('with EXT.xml'));
		$this->assertEquals('lotsofwhitespace', Inflector::variable('lots  of     white space'));
	}

}
