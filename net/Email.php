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
		$this->_addEmails($this->_to, $email, $name);
	}

	public function from($email, $name = '') {
		$this->_addEmails($this->_from, $email, $name);
	}

	public function cc($email, $name = '') {
		$this->_addEmails($this->_cc, $email, $name);
	}

	public function bcc($email, $name = '') {
		$this->_addEmails($this->_bcc, $email, $name);
	}

	public function replyTo($email, $name = '') {
		$this->_addEmails($this->_replyTo, $email, $name);
	}

	public function sender($email, $name = '') {
		$this->_addEmails($this->_sender, $email, $name);
	}

	public function body($message) {
		$this->_body = wordwrap($message, self::CHAR_LIMIT_SHOULD);
	}

	protected function _addEmails(&$source, $email, $name = '') {
		$config = $this->config->get();

		if (is_array($email)) {
			foreach ($email as $key => $value) {
				$mail = $key;
				$name = $value;

				if (is_numeric($key)) {
					$mail = $value;
					$name = '';
				}

				if ($config['validEmailOnly'] && !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
					continue;
				}

				$source[$email] = $name;
			}
		} else {
			if ($config['validEmailOnly'] && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
				return;
			}


			$source[$email] = $name;
		}
	}

}