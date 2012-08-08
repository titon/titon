<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\net;

use titon\log\Logger;
use titon\tests\TestCase;

/**
 * Test class for titon\log\Logger.
 */
class LoggerTest extends TestCase {

	/**
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		foreach (glob(APP_LOGS . '*.log') as $log) {
			@unlink($log);
		}
	}

	/**
	 * Test that emergency() logs a message and creates the log file.
	 */
	public function testEmergency() {
		Logger::emergency('Emergency!');

		$this->assertFileExists(APP_LOGS . 'emergency.log');
	}

	/**
	 * Test that alert() logs a message and creates the log file.
	 */
	public function testAlert() {
		Logger::alert('Alert!');

		$this->assertFileExists(APP_LOGS . 'alert.log');
	}

	/**
	 * Test that critical() logs a message and creates the log file.
	 */
	public function testCritical() {
		Logger::critical('Critical!');

		$this->assertFileExists(APP_LOGS . 'critical.log');
	}

	/**
	 * Test that error() logs a message and creates the log file.
	 */
	public function testError() {
		Logger::error('Error!');

		$this->assertFileExists(APP_LOGS . 'error.log');
	}

	/**
	 * Test that warning() logs a message and creates the log file.
	 */
	public function testWarning() {
		Logger::warning('Warning!');

		$this->assertFileExists(APP_LOGS . 'warning.log');
	}

	/**
	 * Test that notice() logs a message and creates the log file.
	 */
	public function testNotice() {
		Logger::notice('Notice!');

		$this->assertFileExists(APP_LOGS . 'notice.log');
	}

	/**
	 * Test that info() logs a message and creates the log file.
	 */
	public function testInfo() {
		Logger::info('Info!');

		$this->assertFileExists(APP_LOGS . 'info.log');
	}

	/**
	 * Test that debug() logs a message and creates the log file.
	 */
	public function testDebug() {
		Logger::debug('Debug!');

		$this->assertFileExists(APP_LOGS . 'debug.log');
	}

	/**
	 * Test that write() logs a message and creates the log file.
	 */
	public function testWrite() {
		Logger::write('Debug!', 'customLevel');

		$this->assertFileExists(APP_LOGS . 'internal.log');
	}

}