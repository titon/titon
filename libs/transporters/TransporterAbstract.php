<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\transporters;

use titon\Titon;
use titon\base\Base;
use titon\io\Email;
use titon\libs\transporters\TransporterException;

/**
 * Provides convenience methods for Transporters.
 *
 * @package	titon.libs.transporters
 * @abstract
 */
abstract class TransporterAbstract extends Base implements Transporter {

	/**
	 * Format an array of headers into a newline separate string.
	 *
	 * @access public
	 * @param array $headers
	 * @param string $eol
	 * @return string
	 */
	public function formatHeaders($headers, $eol = "\r\n") {
		$out = [];

		if ($headers) {
			foreach ($headers as $header => $value) {
				$out[] = wordwrap(trim($header) . ': ' . trim($value), Email::CHAR_LIMIT_SHOULD, " ");
			}
		}

		return implode($eol, $out);
	}

}