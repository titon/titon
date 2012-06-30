<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\net;

use titon\base\Base;

/**
 * @todo
 *
 * @package	titon.net
 * @link	http://www.faqs.org/rfcs/rfc2822.html
 */
class Email extends Base {

	const CHAR_LIMIT_MUST = 998;

	const CHAR_LIMIT_SHOULD = 78;

	protected $_config = [
		'validEmailOnly' => true
	];

	protected $_to = [];

	protected $_from = [];

	protected $_cc = [];

	protected $_bcc = [];

	// prefixed with X- RFC2822 Section 4.7.5
	protected $_headers = [];

	// http://www.faqs.org/rfcs/rfc2047.html
	protected $_subject = '';

	protected $_body = '';

	protected $_replyTo = [];

	protected $_sender = [];

	protected $_readReceipt = [];

	protected $_attachments = [];

	public function to($email, $name = '') {
		$this->_to = $this->_formatEmails($email, $name) + $this->_to;
	}

	public function from($email, $name = '') {
		$this->_from = $this->_formatEmails($email, $name) + $this->_from;
	}

	public function cc($email, $name = '') {
		$this->_cc = $this->_formatEmails($email, $name) + $this->_cc;
	}

	public function bcc($email, $name = '') {
		$this->_bcc = $this->_formatEmails($email, $name) + $this->_bcc;
	}

	public function replyTo($email, $name = '') {
		$this->_replyTo = $this->_formatEmails($email, $name) + $this->_replyTo;
	}

	public function sender($email, $name = '') {
		$this->_sender = $this->_formatEmails($email, $name) + $this->_sender;
	}

	public function body($message) {
		$this->_body = wordwrap($message, self::CHAR_LIMIT_SHOULD);
	}

	protected function _formatEmails($email, $name = '') {
		$emails = [];
		$config = $this->config->get();

		if (is_array($email)) {
			foreach ($email as $key => $value) {
				if (is_numeric($key)) {
					$mail = $value;
					$name = '';

				} else if (is_string($key)) {
					$mail = $key;
					$name = $value;
				}

				if ($config['validEmailOnly'] && !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
					continue;
				}

				$emails[] = [
					'email' => $mail,
					'name' => $name
				];
			}
		} else {
			if ($config['validEmailOnly'] && filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$emails[] = [
					'email' => $email,
					'name' => $name
				];
			}
		}

		return $emails;
	}

}