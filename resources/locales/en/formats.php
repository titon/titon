<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

return [
	'phone' => '(###) ###-####',
	'ssn' => '###-##-####',

	// Datetime
	'date' => 'm/d/Y',
	'time' => 'h:ma',
	'datetime' => 'm/d/Y h:ma',

	// Numbers
	'number' => [
		'thousands' => ',',
		'decimals' => '.',
		'places' => 2
	],

	// Currency
	'currency' => [
		'code' => 'USD #',
		'dollar' => '$#',
		'cents' => '#&cent;',
		'negative' => '(#)',
		'use' => 'dollar'
	],

	// Localization
	'pluralForms' => 2,
	'pluralRule' => function ($n) {
		return $n != 1 ? 1 : 0;
	}
];