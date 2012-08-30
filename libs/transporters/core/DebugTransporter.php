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
use titon\log\Logger;
use titon\libs\transporters\TransporterAbstract;

/**
 * A transporter that does nothing expect return the current headers and body.
 * Used primarily for easy debugging.
 *
 * @package	titon.libs.transporters.core
 */
class DebugTransporter extends TransporterAbstract {

	/**
	 * Configuration.
	 *
	 * 	logger	- Log level to write to
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'logger' => Logger::DEBUG
	];

	/**
	 * Dispatch an email using the pre-processed headers and body.
	 *
	 * @access public
	 * @param array $headers
	 * @param string $body
	 * @return array
	 */
	public function send(array $headers, $body) {
		if ($this->config->logger >= 0) {
			$message = $this->formatHeaders($headers, "\n") . "\n";
			$message .= $body;

			$output = "Email output:\n";

			foreach (explode("\n", $message) as $line) {
				$output .= '# ' . $line . "\n";
			}

			Logger::write(trim($output), $this->config->logger);
		}

		return [
			'headers' => $headers,
			'body' => $body
		];
	}

}