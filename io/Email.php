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
 * @package	titon.io
 *
 * @link	http://tools.ietf.org/html/rfc5322 - RFC 5322
 */
class Email extends Base {

	/**
	 * Template rendering types.
	 */
	const NONE = 'none';
	const TEXT = 'text';
	const HTML = 'html';
	const BOTH = 'both';

	/**
	 * Priority levels.
	 */
	const PRIORITY_LOW = 'Low';
	const PRIORITY_NORMAL = 'Normal';
	const PRIORITY_HIGH = 'High';

	/**
	 * Encoding types.
	 */
	const ENCODING_7BIT = '7bit';
	const ENCODING_8BIT = '8bit';
	const ENCODING_BASE64 = 'base64';
	const ENCODING_QUOTED_PRINTABLE = 'quoted-printable';

	/**
	 * Max character limits for body and header lengths.
	 */
	const CHAR_LIMIT_MUST = 998;
	const CHAR_LIMIT_SHOULD = 78;

	/**
	 * Configuration.
	 *
	 * 	type		- The type of email to send: none, text, html, both
	 * 	validate	- Validate the email before adding it to the list
	 * 	charset		- Charset to use for email encoding
	 * 	newline		- The newline combination to parse the fields and body with
	 * 	encoding	- Transfer encoding scheme
	 * 	template	- Template location when rendering with views
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'type' => self::NONE,
		'validate' => true,
		'charset' => 'UTF-8',
		'newline' => "\r\n",
		'encoding' => self::ENCODING_BASE64,
		'template' => [
			'module' => null,
			'action' => null,
			'ext' => null
		]
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
	 * Set the body message.
	 *
	 * @access public
	 * @param string|array $message
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function body($message) {

		// If message is an array, we are sending a multipart message
		if (is_array($message)) {
			$body = '';
			$boundary = str_replace('-', '', Uuid::v4());
			$newline = $this->config->newline;
			$encoding = $this->config->encoding;

			$this->header('Content-Type', 'multipart/alternative; boundary=' . $boundary);

			foreach ($message as $type => $msg) {
				$body .= '--' . $boundary;
				$body .= 'Content-type: ' . $this->_getType($type) . $newline;
				$body .= 'Content-Transfer-Encoding: ' . $this->config->encoding . $newline . $newline;
				$body .= $this->_encodeData($this->nl($msg), $encoding);
			}

			$body .= '--' . $boundary . '--';
		} else {
			$body = $this->nl($message);
		}

		// http://tools.ietf.org/html/rfc5322#section-2.3
		$this->_body = wordwrap($body, self::CHAR_LIMIT_SHOULD, $this->config->newline);

		return $this;
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
	 * Return the Engine.
	 *
	 * @access public
	 * @return \titon\libs\engines\Engine
	 */
	public function getEngine() {
		return $this->_engine;
	}

	/**
	 * Return the Transporter.
	 *
	 * @access public
	 * @return \titon\libs\transporters\Transporter
	 */
	public function getTransporter() {
		return $this->_transporter;
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
	 * Convert all combinations of newlines to the format specified in the config.
	 *
	 * @access public
	 * @param string $string
	 * @param string $newline
	 * @return string
	 */
	public function nl($string, $newline = null) {
		$string = str_replace(["\r\n", "\n\r", "\r"], "\n", $string);
		$string = str_replace("\n", $newline ?: $this->config->newline, $string);

		return $string;
	}

	/**
	 * Set the priority level.
	 *
	 * @access public
	 * @param string $priority
	 * @return \titon\io\Email
	 * @throws \titon\io\IoException
	 */
	public function priority($priority) {
		return $this->header('X-Priority', $priority);
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
	 * Set the rendering type, the module location and the action template.
	 *
	 * @access public
	 * @param string $type
	 * @param string $action
	 * @param string $module
	 * @return \titon\io\Email
	 * @throws \titon\io\IoException
	 */
	public function renderAs($type, $action = null, $module = null) {
		if ($type === self::NONE) {
			$this->config->type = self::NONE;

			return $this;

		} else if ($type !== self::TEXT && $type !== self::HTML) {
			throw new IoException(sprintf('Invalid rendering type %s.', $type));
		}

		if (!$action) {
			throw new IoException('A template name is required for rendering.');
		}

		$template = [
			'action' => $action,
			'controller' => null,
			'module' => $module,
			'ext' => null
		];

		if ($type === self::HTML) {
			$template['ext'] = self::HTML;
		}

		$this->config->type = $type;
		$this->config->template = $template;

		if ($this->getEngine()) {
			$this->getEngine()->override('emails', $template, 'email');
		} else {
			throw new IoException('A view engine must be set to render custom templates.');
		}

		return $this;
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
	 * Reset the object and all properties.
	 *
	 * @access public
	 * @return \titon\io\Email
	 * @chainable
	 */
	public function reset() {
		$this->_to = [];
		$this->_from = [];
		$this->_cc = [];
		$this->_bcc = [];
		$this->_headers = [];
		$this->_readReceipt = [];
		$this->_replyTo = [];
		$this->_returnPath = [];
		$this->_sender = [];
		$this->_attachments = [];
		$this->_subject = '';
		$this->_body = '';
		$this->config->set($this->_config);

		return $this;
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

		if (!$this->_subject) {
			throw new IoException('Subject field is not specified.');
		}

		if ($message) {
			$this->body($message);
		}

		// Set default transporter
		if (!$this->getTransporter()) {
			$this->setTransporter(new MailTransporter());
		}

		return $this->getTransporter()->send($this->_getHeaders(), $this->_getBody());
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
	 * Set the Engine object.
	 *
	 * @access public
	 * @param \titon\libs\engines\Engine $engine
	 * @return \titon\io\Email
	 */
	public function setEngine(Engine $engine) {
		$this->_engine = $engine;
		$this->_engine->config->folder = 'emails';

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

			$source[$email] = $this->_encode($name);

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
		$encoding = ($this->config->encoding === self::ENCODING_QUOTED_PRINTABLE) ? 'Q' : 'B';

		if (strtolower($this->config->charset) !== strtolower($charset)) {
			$string = mb_convert_encoding($string, $this->config->charset, $charset);
		}

		return mb_encode_mimeheader($string, $this->config->charset, $encoding);
	}

	/**
	 * Encode a string to the given format.
	 *
	 * @access protected
	 * @param string $string
	 * @param string $encoding
	 * @return string
	 * @throws \titon\io\IoException
	 */
	protected function _encodeData($string, $encoding) {
		switch ($encoding) {
			case self::ENCODING_7BIT:
			case self::ENCODING_8BIT:
				return $this->nl(rtrim($string));
			break;
			case self::ENCODING_BASE64:
				return chunk_split(base64_encode($string), 76, $this->config->newline);
			break;
			case self::ENCODING_QUOTED_PRINTABLE:
				return quoted_printable_encode($string);
			break;
			default:
				throw new IoException(sprintf('Invalid encoding type %s.', $encoding));
			break;
		}
	}

	/**
	 * Format a list of email and name combos to be used in a header field.
	 *
	 * @access protected
	 * @param array $source
	 * @return array
	 */
	protected function _formatEmails(array $source) {
		$emails = [];

		if ($source) {
			foreach ($source as $email => $name) {
				if (!$name) {
					$emails[] = $email;
				} else {
					$emails[] = sprintf('%s <%s>', $name, $email);
				}
			}
		}

		return $emails;
	}

	/**
	 * Get the body by calculating boundaries and attachments.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _getBody() {
		$type = $this->config->type;
		$template = $this->config->template;

		// If engine is set use it for rendering
		if ($this->getEngine() && $type !== self::NONE) {
			if ($type === self::BOTH) {
				$modes = [self::HTML, self::TEXT];
			} else {
				$modes = (array) $type;
			}

			// Gather a body from each type
			$body = [];

			foreach ($modes as $mode) {
				$this->renderAs($mode, $template['action'], $template['module']);

				$body[$mode] = $this->getEngine()->run(false);
			}

			$this->body($body);

			// Reset back to base type
			$this->config->type = $type;
		}

		// @todo attachments

		return $this->_body;
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
		$headers['MIME-Version'] = '1.0';
		$headers['Message-ID'] = sprintf('<%s@%s>', Uuid::v4(), php_uname('n'));

		if (empty($headers['X-Mailer'])) {
			$headers['X-Mailer'] = sprintf('Titon %s, A PHP 5.4 Modular Framework', Titon::VERSION);
		}

		if (empty($headers['Date'])) {
			$headers['Date'] = date(DATE_RFC2822);
		}

		if ($this->config->type === self::TEXT) {
			$headers['Content-Type'] = 'text/plain; charset=' . $this->config->charset;
		} else if ($this->config->type === self::HTML) {
			$headers['Content-Type'] = 'text/html; charset=' . $this->config->charset;
		}

		// @todo attachment headers

		return $headers;
	}

	/**
	 * Return the email mime type and charset.
	 *
	 * @access protected
	 * @param string $type
	 * @return string
	 */
	protected function _getType($type) {
		switch ($type) {
			default:
			case self::TEXT:	$mime = 'text/plain'; break;
			case self::HTML:	$mime = 'text/html'; break;
		}

		$mime .= '; charset=' . $this->config->charset;

		return $mime;
	}

}