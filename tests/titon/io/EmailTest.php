<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\io;

use titon\io\Email;
use titon\libs\transporters\core\DebugTransporter;
use titon\tests\TestCase;
use \Exception;

/**
 * Test class for titon\io\Email.
 */
class EmailTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new Email();
		$this->object->setTransporter(new DebugTransporter());
		$this->object->from('from@domain.com');
	}

	/**
	 * Test that to to() adds email addresses to the To header.
	 */
	public function testTo() {
		$this->object->to('email1@domain.com');
		$this->object->to('email2@domain.com', 'Name #2');

		$headers = $this->object->send()['headers'];
		$this->assertEquals('email1@domain.com, Name #2 <email2@domain.com>', $headers['To']);

		$this->object->to([
			'invalid@domain',
			'email3@domain.com',
			'email4@domain.com' => 'Name #4'
		]);

		$headers = $this->object->send()['headers'];
		$this->assertEquals('email1@domain.com, Name #2 <email2@domain.com>, email3@domain.com, Name #4 <email4@domain.com>', $headers['To']);
	}

	/**
	 * Test that from() adds only a single email address to the From header.
	 */
	public function testFrom() {
		$this->object->to('to@domain.com'); // for send()
		$this->object->from('from@domain.com');

		$headers = $this->object->send()['headers'];
		$this->assertEquals('from@domain.com', $headers['From']);

		// With a name
		$this->object->from('from@domain.com', 'With Name');

		$headers = $this->object->send()['headers'];
		$this->assertEquals('With Name <from@domain.com>', $headers['From']);

		// If multiple is passed, only use the first
		$this->object->from([
			'from@domain.com' => 'From Name',
			'from2@domain.com'
		]);

		$headers = $this->object->send()['headers'];
		$this->assertEquals('From Name <from@domain.com>', $headers['From']);
	}

	/**
	 * Test that to cc() adds email addresses to the Cc header.
	 */
	public function testCc() {
		$this->object->cc('email1@domain.com');
		$this->object->cc('email2@domain.com', 'Name #2');

		$headers = $this->object->send()['headers'];
		$this->assertEquals('email1@domain.com, Name #2 <email2@domain.com>', $headers['Cc']);

		$this->object->cc([
			'invalid@domain',
			'email3@domain.com',
			'email4@domain.com' => 'Name #4'
		]);

		$headers = $this->object->send()['headers'];
		$this->assertEquals('email1@domain.com, Name #2 <email2@domain.com>, email3@domain.com, Name #4 <email4@domain.com>', $headers['Cc']);
	}

	/**
	 * Test that to bcc() adds email addresses to the Bcc header.
	 */
	public function testBcc() {
		$this->object->bcc('email1@domain.com');
		$this->object->bcc('email2@domain.com', 'Name #2');

		$headers = $this->object->send()['headers'];
		$this->assertEquals('email1@domain.com, Name #2 <email2@domain.com>', $headers['Bcc']);

		$this->object->bcc([
			'invalid@domain',
			'email3@domain.com',
			'email4@domain.com' => 'Name #4'
		]);

		$headers = $this->object->send()['headers'];
		$this->assertEquals('email1@domain.com, Name #2 <email2@domain.com>, email3@domain.com, Name #4 <email4@domain.com>', $headers['Bcc']);
	}

	/**
	 * Test that sender() adds only a single email address to the Sender header.
	 */
	public function testSender() {
		$this->object->to('to@domain.com'); // for send()
		$this->object->sender('email@domain.com', 'Name');

		$headers = $this->object->send()['headers'];
		$this->assertEquals('Name <email@domain.com>', $headers['Sender']);
	}

	/**
	 * Test that readReceipt() adds only a single email address to the Disposition-Notification-To header.
	 */
	public function testReadReceipt() {
		$this->object->to('to@domain.com'); // for send()
		$this->object->readReceipt('email@domain.com', 'Name');

		$headers = $this->object->send()['headers'];
		$this->assertEquals('Name <email@domain.com>', $headers['Disposition-Notification-To']);
	}

	/**
	 * Test that replyTo() adds only a single email address to the Reply-To header.
	 */
	public function testReplyTo() {
		$this->object->to('to@domain.com'); // for send()
		$this->object->replyTo('email@domain.com', 'Name');

		$headers = $this->object->send()['headers'];
		$this->assertEquals('Name <email@domain.com>', $headers['Reply-To']);
	}

	/**
	 * Test that returnPath() adds only a single email address to the Return-Path header.
	 */
	public function testReturnPath() {
		$this->object->to('to@domain.com'); // for send()
		$this->object->returnPath('email@domain.com', 'Name');

		$headers = $this->object->send()['headers'];
		$this->assertEquals('Name <email@domain.com>', $headers['Return-Path']);
	}

	public function testBody() {

	}

	public function testSubject() {

	}

}