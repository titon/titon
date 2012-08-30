<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\io;

use titon\Titon;
use titon\io\Email;
use titon\libs\engines\core\ViewEngine;
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
		$this->object->to('to@domain.com');
		$this->object->subject('Email Test');
	}

	/**
	 * Delete debug log file.
	 */
	protected function tearDown() {
		parent::tearDown();

		@unlink(APP_LOGS . 'debug.log');
	}

	/**
	 * Test that to bcc() adds email addresses to the Bcc header.
	 */
	public function testBcc() {
		$this->object->bcc('email1@domain.com');
		$this->object->bcc('email2@domain.com', 'Name #2');

		$headers = $this->object->send('Testing bcc()')['headers'];
		$this->assertEquals('email1@domain.com, Name #2 <email2@domain.com>', $headers['Bcc']);

		$this->object->bcc([
			'invalid@domain',
			'email3@domain.com',
			'email4@domain.com' => 'Name #4'
		]);

		$headers = $this->object->send('Testing bcc()')['headers'];
		$this->assertEquals('email1@domain.com, Name #2 <email2@domain.com>, email3@domain.com, Name #4 <email4@domain.com>', $headers['Bcc']);
	}

	/**
	 * Test that body() set a message, converts new lines and respects line length.
	 */
	public function testBody() {
		$this->object->body('This is a simple body!');

		$body = $this->object->send()['body'];
		$this->assertEquals('This is a simple body!', $body);

		// array
		$this->object->body(['This is a simple body!', 'With an additional body via array.']);

		$body = $this->object->send()['body'];
		$this->assertEquals("This is a simple body!\r\nWith an additional body via array.", $body);

		// new line
		$this->object->body("This ia line\rAnd this is another line\nCan't stop this");

		$body = $this->object->send()['body'];
		$this->assertEquals("This ia line\r\nAnd this is another line\r\nCan't stop this", $body);

		// word wrap
		$this->object->body('This is a really long message that we want to try and break the individual line character limit.');

		$body = $this->object->send()['body'];
		$this->assertEquals("This is a really long message that we want to try and break the individual\r\nline character limit.", $body);
	}

	/**
	 * Test that to cc() adds email addresses to the Cc header.
	 */
	public function testCc() {
		$this->object->cc('email1@domain.com');
		$this->object->cc('email2@domain.com', 'Name #2');

		$headers = $this->object->send('Testing cc()')['headers'];
		$this->assertEquals('email1@domain.com, Name #2 <email2@domain.com>', $headers['Cc']);

		$this->object->cc([
			'invalid@domain',
			'email3@domain.com',
			'email4@domain.com' => 'Name #4'
		]);

		$headers = $this->object->send('Testing cc()')['headers'];
		$this->assertEquals('email1@domain.com, Name #2 <email2@domain.com>, email3@domain.com, Name #4 <email4@domain.com>', $headers['Cc']);
	}

	/**
	 * Test that from() adds only a single email address to the From header.
	 */
	public function testFrom() {
		$this->object->from('from@domain.com');

		$headers = $this->object->send('Testing from()')['headers'];
		$this->assertEquals('from@domain.com', $headers['From']);

		// With a name
		$this->object->from('from@domain.com', 'With Name');

		$headers = $this->object->send('Testing from()')['headers'];
		$this->assertEquals('With Name <from@domain.com>', $headers['From']);

		// If multiple is passed, only use the first
		$this->object->from([
			'from@domain.com' => 'From Name',
			'from2@domain.com'
		]);

		$headers = $this->object->send('Testing from()')['headers'];
		$this->assertEquals('From Name <from@domain.com>', $headers['From']);
	}

	/**
	 * Test that setEngine() sets an Engine and getEngine() returns it.
	 */
	public function testGetSetEngine() {
		$this->assertEquals(null, $this->object->getEngine());

		$this->object->setEngine(new ViewEngine());
		$this->assertInstanceOf('titon\libs\engines\core\ViewEngine', $this->object->getEngine());
	}

	/**
	 * Test that setTransporter() sets an Transporter and getTransporter() returns it.
	 */
	public function testGetSetTransporter() {
		// already set in setUp()

		$this->object->setTransporter(new DebugTransporter());
		$this->assertInstanceOf('titon\libs\transporters\core\DebugTransporter', $this->object->getTransporter());
	}

	/**
	 * Test that header() can set a single or multiple headers.
	 */
	public function testHeader() {
		$this->object->header('X-Framework', 'Titon');
		$this->object->header([
			'X-Version' => Titon::VERSION,
			'X-Foo' => 'Bar'
		]);

		$headers = $this->object->send('Testing header()')['headers'];
		$this->assertArrayHasKey('X-Framework', $headers);
		$this->assertArrayHasKey('X-Version', $headers);
		$this->assertArrayHasKey('X-Foo', $headers);
	}

	/**
	 * Test that nl() converts new lines.
	 */
	public function testNl() {
		$this->assertEquals("new\r\nline", $this->object->nl("new\rline"));
		$this->assertEquals("new\r\nline", $this->object->nl("new\nline"));
		$this->assertEquals("new\r\nline", $this->object->nl("new\r\nline"));
		$this->assertEquals("new\r\nline", $this->object->nl("new\n\rline"));
	}

	/**
	 * Test that priority() sets the priority level.
	 */
	public function testPriority() {
		$this->object->priority(Email::PRIORITY_HIGH);

		$headers = $this->object->send('Testing priority()')['headers'];
		$this->assertArrayHasKey('X-Priority', $headers);
		$this->assertEquals(Email::PRIORITY_HIGH, $headers['X-Priority']);
	}

	/**
	 * Test that readReceipt() adds only a single email address to the Disposition-Notification-To header.
	 */
	public function testReadReceipt() {
		$this->object->to('to@domain.com'); // for send()
		$this->object->readReceipt('email@domain.com', 'Name');

		$headers = $this->object->send('Testing readReceipt()')['headers'];
		$this->assertEquals('Name <email@domain.com>', $headers['Disposition-Notification-To']);
	}

	/**
	 * Test that renderAs() will render an email template using a rendering engine.
	 */
	public function testRenderAs() {
		try {
			$this->object->renderAs(Email::HTML, 'example');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		$this->object->setEngine(new ViewEngine());

		// text
		$this->object->renderAs(Email::TEXT, 'example');
		$this->assertEquals(Email::TEXT, $this->object->config->type);

		$body = $this->object->send('Testing renderAs()')['body'];
		$this->assertEquals("This is an example email template.\r\nIt is also a plain text email.\r\n- Titon", $body);

		// html
		$this->object->renderAs(Email::HTML, 'example');
		$this->assertEquals(Email::HTML, $this->object->config->type);

		$body = $this->object->send('Testing renderAs()')['body'];
		$this->assertEquals("<!DOCTYPE html>\r\n<html>\r\n<body>\r\n\tThis is an example email template.\r\nIt is an <b>HTML</b> specific <i>template</i>.\t<br>- Titon\r\n</body>\r\n</html>", $body);

		// none
		$this->object->renderAs(Email::NONE);
		$this->assertEquals(Email::NONE, $this->object->config->type);

		$body = $this->object->send('Testing renderAs()')['body'];
		$this->assertEquals("Testing renderAs()", $body);
	}

	/**
	 * Test that replyTo() adds only a single email address to the Reply-To header.
	 */
	public function testReplyTo() {
		$this->object->to('to@domain.com'); // for send()
		$this->object->replyTo('email@domain.com', 'Name');

		$headers = $this->object->send('Testing replyTo()')['headers'];
		$this->assertEquals('Name <email@domain.com>', $headers['Reply-To']);
	}

	/**
	 * Test that reset() resets all data in the object and send() will throw an error if the data is empty.
	 */
	public function testReset() {
		$headers = $this->object->send('Testing reset()')['headers'];
		$this->assertArrayHasKey('To', $headers);
		$this->assertArrayHasKey('From', $headers);

		$this->object->reset();

		try {
			$this->object->send();
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that returnPath() adds only a single email address to the Return-Path header.
	 */
	public function testReturnPath() {
		$this->object->to('to@domain.com'); // for send()
		$this->object->returnPath('email@domain.com', 'Name');

		$headers = $this->object->send('Testing returnPath()')['headers'];
		$this->assertEquals('Name <email@domain.com>', $headers['Return-Path']);
	}

	/**
	 * Test that send() sends an email or throws exceptions.
	 */
	public function testSend() {
		$this->object->reset();

		// no from
		try {
			$this->object->send();
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		$this->object->from('from@domain.com');

		// no to
		try {
			$this->object->send();
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		$this->object->to('to@domain.com');

		// no subject
		try {
			$this->object->send();
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		$this->object->subject('Subject');

		$this->assertTrue(is_array($this->object->send()));
	}

	/**
	 * Test that sender() adds only a single email address to the Sender header.
	 */
	public function testSender() {
		$this->object->to('to@domain.com'); // for send()
		$this->object->sender('email@domain.com', 'Name');

		$headers = $this->object->send('Testing sender()')['headers'];
		$this->assertEquals('Name <email@domain.com>', $headers['Sender']);
	}

	/**
	 * Test that subject() sets the subject and send() throws an exception if it is not set.
	 */
	public function testSubject() {
		$this->object->subject(null);

		try {
			$this->object->send();
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		$this->object->subject('Test Subject');

		$headers = $this->object->send('Testing subject()')['headers'];
		$this->assertEquals('Test Subject', $headers['Subject']);
	}

	/**
	 * Test that to to() adds email addresses to the To header.
	 */
	public function testTo() {
		$this->object->to('email1@domain.com');
		$this->object->to('email2@domain.com', 'Name #2');

		$headers = $this->object->send('Testing to()')['headers'];
		$this->assertEquals('to@domain.com, email1@domain.com, Name #2 <email2@domain.com>', $headers['To']);

		$this->object->to([
			'invalid@domain',
			'email3@domain.com',
			'email4@domain.com' => 'Name #4'
		]);

		$headers = $this->object->send('Testing to()')['headers'];
		$this->assertEquals('to@domain.com, email1@domain.com, Name #2 <email2@domain.com>, email3@domain.com, Name #4 <email4@domain.com>', $headers['To']);
	}

}