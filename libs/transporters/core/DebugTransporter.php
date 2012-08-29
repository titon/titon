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

/**
 * A transporter that does nothing expect return the current headers and body.
 * Used primarily for easy debugging.
 *
 * @package	titon.libs.transporters.core
 */
class DebugTransporter extends TransporterAbstract {

	/**
	 * Dispatch an email using the pre-processed headers and body.
	 *
	 * @access public
	 * @param array $headers
	 * @param string $body
	 * @return array
	 */
	public function send(array $headers, $body) {
		return [
			'headers' => $headers,
			'body' => $body
		];
	}

}