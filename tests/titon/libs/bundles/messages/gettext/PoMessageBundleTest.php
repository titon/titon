<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

include_once '../../../../../bootstrap.php';

use titon\libs\bundles\messages\gettext\PoMessageBundle;

/**
 * Test class for titon\libs\bundles\messages\gettext\PoMessageBundle.
 */
class PoMessageBundleTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup message bundles.
	 */
	public function setUp() {
		$this->object = new PoMessageBundle(array(
			'bundle' => 'en_US'
		));
	}

	/**
	 * Test that exceptions are thrown if a bundle doesn't exist.
	 */
	public function testBundleDetection() {
		try {
			$fakeBundle = new PoMessageBundle(array(
				'bundle' => 'en'
			));

			$fakeBundle = new PoMessageBundle(array(
				'bundle' => 'doesntExist'
			));

			$this->assertTrue(false);
		} catch (\Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that loading messages from a catalog work.
	 */
	public function testMessageLoading() {
		$messages = $this->object->loadFile('default');

		$this->assertTrue(is_array($messages));
		$this->assertEquals(array('basic', 'multiline', 'plurals', 'context'), array_keys($messages));
		$this->assertEquals(array(
			'basic' => 'Basic message',
			'multiline' => "Multiline message\nMore message here\nAnd more message again",
			'plurals' => array('plural', 'plurals'),
			'context' => 'Context message'
		), $messages);

		$messages = $this->object->loadFile('doesntExist');

		$this->assertTrue(is_array($messages));
		$this->assertEmpty($messages);
	}

}
