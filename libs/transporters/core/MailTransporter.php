<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\transporters\core;

use titon\Titon;
use titon\libs\transporters\TransporterAbstract;
use titon\libs\transporters\TransporterException;

/**
 * Send an email using the built-in PHP function mail().
 *
 * @package	titon.libs.transporters.core
 *
 * @link	http://php.net/manual/function.mail.php
 */
class MailTransporter extends TransporterAbstract {

	/**
	 * Configuration.
	 *
	 * 	params	- Additional command line flags
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'params' => ''
	];

	/**
	 * Dispatch an email using the pre-processed headers and body.
	 *
	 * @access public
	 * @param array $headers
	 * @param string $body
	 * @return array
	 * @throws \titon\libs\transporters\TransporterException
	 */
	public function send(array $headers, $body) {
		if (!mail($headers['To'], $headers['Subject'], $body, $this->formatHeaders($headers), $this->config->params)) {
			throw new TransporterException('Email failed to send while using mail().');
		}

		return [
			'headers' => $headers,
			'body' => $body
		];
	}

}