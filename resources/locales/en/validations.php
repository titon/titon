<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

return [
	'currency' => '/\$[0-9\.\s,]/i',
	'phone' => '/(?:\+?1\s?)?(?:\([0-9]{3}\)\s?)?[0-9]{3}-[0-9]{4}/i',
	'postalCode' => '/[0-9]{5}(?:-[0-9]{4})?/i',
	'ssn' => '/[0-9]{3}\-[0-9]{2}-[0-9]{4}/i'
];