<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

include_once '../../../../../bootstrap.php';

use titon\libs\bundles\messages\core\JsonMessageBundle;

/**
 * Test class for titon\libs\bundles\messages\core\JsonMessageBundle.
 */
class JsonMessageBundleTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup message bundles.
	 */
	public function setUp() {
		$this->object = new JsonMessageBundle(array(
			'bundle' => 'ex'
		));
	}

	/**
	 * Test that exceptions are thrown if a bundle doesn't exist.
	 */
	public function testBundleDetection() {
		try {
			$fakeBundle = new JsonMessageBundle(array(
				'bundle' => 'en'
			));

			$fakeBundle = new JsonMessageBundle(array(
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
		$messages = $this->object->get('default');

		$this->assertTrue(is_array($messages));
		$this->assertEquals(array('titon', 'test', 'type'), array_keys($messages));
		$this->assertEquals(array(
			'titon' => 'Titon',
			'test' => 'Test',
			'type' => 'json'
		), $messages);

		$messages = $this->object->get('doesntExist');

		$this->assertTrue(is_array($messages));
		$this->assertEmpty($messages);
	}

}
