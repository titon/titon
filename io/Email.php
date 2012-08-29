<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\io;

use titon\Titon;
use titon\base\Base;
use titon\libs\engines\Engine;
use titon\libs\transporters\Transporter;
use titon\libs\transporters\core\MailTransporter;
use titon\utility\Uuid;
use titon\utility\Validate;

/**
 * @todo
 *
 * @package	titon.net
 * @link	http://www.faqs.org/rfcs/rfc2822.html
 */
class Email extends Base {

	/**
	 * Client name.
	 */
	const EMAIL_CLIENT = 'Titon Framework: Email';

	/**
	 * Template rendering types.
	 */
	const TEXT = 'text';
	const HTML = 'html';

	/**
	 * Max character limits for body and header lengths.
	 */
	const CHAR_LIMIT_MUST = 998;
	const CHAR_LIMIT_SHOULD = 78;

	/**
	 * Configuration.
	 *
	 * 	validate		- Validate the email before adding it to the list
	 * 	charset			- Charset to use for email encoding
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'validate' => true,
		'charset' => 'UTF-8',
		'template' => ''
	];

	/**
	 * To emails.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_to = [];

	/**
	 * From emails.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_from = [];

	/**
	 * Carbon copy emails.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_cc = [];

	/**
	 * Blind carbon copy emails.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_bcc = [];

	/**
	 * Custom headers; prefixed with X- RFC2822 Section 4.7.5.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_headers = [];

	/**
	 * Subject line; http://www.faqs.org/rfcs/rfc2047.html
	 *
	 * @access protected.
	 * @var string
	 */
	protected $_subject = '';

	/**
	 * Body message.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_body = '';

	/**
	 * Reply to emails.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_replyTo = [];

	/**
	 * Sender emails.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_sender = [];

	/**
	 * Read receipt emails.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_readReceipt = [];

	/**
	 * Return path emails.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_returnPath = [];

	/**
	 * Attached files.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_attachments = [];

	/**
	 * Template rendering object.
	 *
	 * @access protected
	 * @var \titon\libs\engines\Engine
	 */
	protected $_engine;

	/**
	 * Email transporting object.
	 *
	 * @access protected
	 * @var \titon\libs\transporters\Transporter
	 */
	protected $_transporter;

	/**
	 * Add To header emails.
	 *
	 * @access public
	 * @param string|array $email
	 * @param string $name
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function to($email, $name = '') {
		return $this->_addEmails($this->_to, $email, $name);
	}

	/**
	 * Add From header emails.
	 *
	 * @access public
	 * @param string|array $email
	 * @param string $name
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function from($email, $name = '') {
		return $this->_addEmails($this->_from, $email, $name, true);
	}

	/**
	 * Add CC header emails.
	 *
	 * @access public
	 * @param string|array $email
	 * @param string $name
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function cc($email, $name = '') {
		return $this->_addEmails($this->_cc, $email, $name);
	}

	/**
	 * Add BCC header emails.
	 *
	 * @access public
	 * @param string|array $email
	 * @param string $name
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function bcc($email, $name = '') {
		return $this->_addEmails($this->_bcc, $email, $name);
	}

	/**
	 * Add Sender header emails.
	 *
	 * @access public
	 * @param string|array $email
	 * @param string $name
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function sender($email, $name = '') {
		return $this->_addEmails($this->_sender, $email, $name, true);
	}

	/**
	 * Add Reply-To header emails.
	 *
	 * @access public
	 * @param string|array $email
	 * @param string $name
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function replyTo($email, $name = '') {
		return $this->_addEmails($this->_replyTo, $email, $name, true);
	}

	/**
	 * Add Disposition-Notification-To header emails.
	 *
	 * @access public
	 * @param string|array $email
	 * @param string $name
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function readReceipt($email, $name = '') {
		return $this->_addEmails($this->_readReceipt, $email, $name, true);
	}

	/**
	 * Add Return-Path header emails.
	 *
	 * @access public
	 * @param string|array $email
	 * @param string $name
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function returnPath($email, $name = '') {
		return $this->_addEmails($this->_returnPath, $email, $name, true);
	}

	/**
	 * Set the body message.
	 *
	 * @access public
	 * @param string $message
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function body($message) {
		if (is_array($message)) {
			$message = implode("\n", $message);
		}

		$this->_body = wordwrap((string) $message, self::CHAR_LIMIT_SHOULD);

		return $this;
	}

	/**
	 * Set the subject field.
	 *
	 * @access public
	 * @param string $subject
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function subject($subject) {
		$this->_subject = $this->_encode($subject);

		return $this;
	}

	/**
	 * Add an HTTP header into the list awaiting to be written in the response.
	 *
	 * @access public
	 * @param string|array $headers
	 * @param string $value
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function header($headers, $value = null) {
		if (is_array($headers)) {
			foreach ($headers as $header => $value) {
				$this->header($header, $value);
			}
		} else {
			$this->_headers[$headers] = $value;
		}

		return $this;
	}

	/**
	 * Send a message using a Transporter. Can optionally pass the body message through an argument.
	 *
	 * @access public
	 * @param string $message
	 * @return array
	 * @throws \titon\io\IoException
	 */
	public function send($message = null) {
		if (!$this->_from) {
			throw new IoException('From field is not specified.');
		}

		if (!$this->_to && !$this->_cc && !$this->_bcc) {
			throw new IoException('A recipient (to, cc, bcc) is not specified.');
		}

		if ($message) {
			$this->body($message);
		}

		// Set default transporter
		if (!$this->_transporter) {
			$this->setTransporter(new MailTransporter());
		}

		return $this->_transporter->send($this->_getHeaders(), $this->_getBody());
	}

	/**
	 * Set the Engine object.
	 *
	 * @access public
	 * @param \titon\libs\engines\Engine $engine
	 * @return \titon\io\Email
	 */
	public function setEngine(Engine $engine) {
		$this->_engine = $engine;

		return $this;
	}

	/**
	 * Set the Transporter object.
	 *
	 * @access public
	 * @param \titon\libs\transporters\Transporter $transporter
	 * @return \titon\io\Email
	 */
	public function setTransporter(Transporter $transporter) {
		$this->_transporter = $transporter;

		return $this;
	}

	/**
	 * Add emails to the specific source. If $single is true, only one emailed is allowed.
	 *
	 * @access protected
	 * @param array $source
	 * @param string|array $emails
	 * @param string $name
	 * @param boolean $single
	 * @return \titon\io\Email
	 * @chainable
	 */
	protected function _addEmails(&$source, $emails, $name = '', $single = false) {
		$validate = $this->config->validate;

		if (!is_array($emails)) {
			$emails = [$emails => $name];
		}

		if ($single) {
			$source = [];
		}

		foreach ($emails as $email => $name) {
			if (is_numeric($email)) {
				$email = $name;
				$name = '';
			}

			if ($validate && !Validate::email($email, false)) {
				continue;
			}

			$source[$email] = $name;

			if ($single) {
				break;
			}
		}

		return $this;
	}

	/**
	 * Encode a string. If the email encoding is different than the app encoding, convert the string.
	 *
	 * @access protected
	 * @param string $string
	 * @return string
	 */
	protected function _encode($string) {
		$charset = Titon::config()->encoding();

		if (strtolower($this->config->charset) !== strtolower($charset)) {
			$string = mb_convert_encoding($string, $this->config->charset, $charset);
		}

		return mb_encode_mimeheader($string, $this->config->charset, 'B');
	}

	/**
	 * Format a list of email and name combos to be used in a header field.
	 *
	 * @access protected
	 * @param array $source
	 * @return array
	 */
	protected function _formatEmails($source) {
		$emails = [];

		if ($source) {
			foreach ($source as $email => $name) {
				if (!$name) {
					$emails[] = $email;
				} else {
					$emails[] = sprintf('%s <%s>', $this->_encode($name), $email);
				}
			}
		}

		return $emails;
	}

	protected function _getBody() {
		$body = $this->_body;

		// @todo - engine rendering

		return $body;
	}

	/**
	 * Gather and format all email headers.
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getHeaders() {
		$headers = $this->_headers;
		$headers['Subject'] = $this->_subject;

		// Gather email headers
		$emailSources = [
			'To' => $this->_to,
			'From' => $this->_from,
			'Cc' => $this->_cc,
			'Bcc' => $this->_bcc,
			'Sender' => $this->_sender,
			'Reply-To' => $this->_replyTo,
			'Return-Path' => $this->_returnPath,
			'Disposition-Notification-To' => $this->_readReceipt
		];

		foreach ($emailSources as $header => $emails) {
			if ($emails) {
				$headers[$header] = implode(', ', $this->_formatEmails($emails));
			}
		}

		if (isset($headers['Sender']) && $headers['Sender'] === $headers['From']) {
			unset($headers['Sender']);
		}

		// Check for missing headers
		if (!isset($headers['X-Mailer'])) {
			$headers['X-Mailer'] = self::EMAIL_CLIENT;
		}

		if (!isset($headers['Date'])) {
			$headers['Date'] = date(DATE_RFC2822);
		}

		if (!isset($headers['Message-ID'])) {
			$headers['Message-ID'] = sprintf('<%s@%s>', Uuid::v4(), php_uname('n'));
		}

		// @todo attachment headers

		return $headers;
	}

}