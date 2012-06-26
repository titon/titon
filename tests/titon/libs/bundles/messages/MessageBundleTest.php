<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\libs\bundles\messages\MessageBundle;

/**
 * Test class for titon\libs\bundles\messages\MessageBundle.
 */
class MessageBundleTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test that exceptions are thrown if a bundle doesn't exist.
	 */
	public function testBundleDetection() {
		try {
			$fakeBundle = new MessageBundle(array(
				'bundle' => 'en'
			));

			$fakeBundle = new MessageBundle(array(
				'bundle' => 'doesntExist'
			));

			$this->assertTrue(false);
		} catch (\Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that loading messages from php files work.
	 */
	public function testPhpBundles() {
		$bundle = new MessageBundle(array('bundle' => 'ex'));
		$bundle->addReader(new titon\libs\readers\core\PhpReader());

		$messages = $bundle->loadResource('default');

		$this->assertTrue(is_array($messages));
		$this->assertEquals(array('titon', 'test', 'type', 'format'), array_keys($messages));
		$this->assertEquals(array(
			'titon' => 'Titon',
			'test' => 'Test',
			'type' => 'php',
			'format' => '{0,number,integer} health, {1,number,integer} energy, {2,number} damage'
		), $messages);

		$messages = $bundle->loadResource('doesntExist');

		$this->assertTrue(is_array($messages));
		$this->assertEmpty($messages);
	}

	/**
	 * Test that loading messages from ini files work.
	 */
	public function testIniBundles() {
		$bundle = new MessageBundle(array('bundle' => 'ex'));
		$bundle->addReader(new titon\libs\readers\core\IniReader());

		$messages = $bundle->loadResource('default');

		$this->assertTrue(is_array($messages));
		$this->assertEquals(array('titon', 'test', 'type', 'format'), array_keys($messages));
		$this->assertEquals(array(
			'titon' => 'Titon',
			'test' => 'Test',
			'type' => 'ini',
			'format' => '{0,number,integer} health, {1,number,integer} energy, {2,number} damage'
		), $messages);

		$messages = $bundle->loadResource('doesntExist');

		$this->assertTrue(is_array($messages));
		$this->assertEmpty($messages);
	}

	/**
	 * Test that loading messages from json files work.
	 */
	public function testJsonBundles() {
		$bundle = new MessageBundle(array('bundle' => 'ex'));
		$bundle->addReader(new titon\libs\readers\core\JsonReader());

		$messages = $bundle->loadResource('default');

		$this->assertTrue(is_array($messages));
		$this->assertEquals(array('titon', 'test', 'type', 'format'), array_keys($messages));
		$this->assertEquals(array(
			'titon' => 'Titon',
			'test' => 'Test',
			'type' => 'json',
			'format' => '{0,number,integer} health, {1,number,integer} energy, {2,number} damage'
		), $messages);

		$messages = $bundle->loadResource('doesntExist');

		$this->assertTrue(is_array($messages));
		$this->assertEmpty($messages);
	}

	/**
	 * Test that loading messages from xml files work.
	 */
	public function testXmlBundles() {
		$bundle = new MessageBundle(array('bundle' => 'ex'));
		$bundle->addReader(new titon\libs\readers\core\XmlReader());

		$messages = $bundle->loadResource('default');

		$this->assertTrue(is_array($messages));
		$this->assertEquals(array('titon', 'test', 'type', 'format'), array_keys($messages));
		$this->assertEquals(array(
			'titon' => 'Titon',
			'test' => 'Test',
			'type' => 'xml',
			'format' => '{0,number,integer} health, {1,number,integer} energy, {2,number} damage'
		), $messages);

		$messages = $bundle->loadResource('doesntExist');

		$this->assertTrue(is_array($messages));
		$this->assertEmpty($messages);
	}

	/**
	 * Test that loading messages from xml files work.
	 */
	public function testPoBundles() {
		$bundle = new MessageBundle(array('bundle' => 'ex'));
		$bundle->addReader(new titon\libs\readers\gettext\PoReader());

		$messages = $bundle->loadResource('default');

		$this->assertTrue(is_array($messages));
		$this->assertEquals(array('basic', 'multiline', 'plurals', 'context', 'format'), array_keys($messages));
		$this->assertEquals(array(
			'basic' => 'Basic message',
			'multiline' => "Multiline message\nMore message here\nAnd more message again",
			'plurals' => array('plural', 'plurals'),
			'context' => 'Context message',
			'format' => '{0,number,integer} health, {1,number,integer} energy, {2,number} damage'
		), $messages);

		$messages = $bundle->loadResource('doesntExist');

		$this->assertTrue(is_array($messages));
		$this->assertEmpty($messages);
	}

}
