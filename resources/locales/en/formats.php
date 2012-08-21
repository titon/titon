<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

return [
	'ssn' => '###-##-####',

	// Phone
	'phone' => [
		7 => '###-####',
		10 => '(###) ###-####',
		11 => '# (###) ###-####'
	],

	// Datetime
	'date' => '%m/%d/%Y',
	'time' => '%I:%M%P',
	'datetime' => '%m/%d/%Y %I:%M%P',

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