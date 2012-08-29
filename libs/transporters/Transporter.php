<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\transporters;

/**
 * Interface for the transporters library.
 *
 * @package	titon.libs.transporters
 */
interface Transporter {

	public function send(array $headers, $body);

}