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
use titon\libs\transporters\TransporterException;

/**
 * @todo
 *
 * @package	titon.libs.transporters
 * @abstract
 */
abstract class TransporterAbstract extends Base implements Transporter {

	public function formatHeaders($headers, $eol = "\n") {
		$string = '';

		foreach ($headers as $header => $value) {
			$string .= $header .': ' . $value . $eol;
		}

		return trim($string);
	}

}