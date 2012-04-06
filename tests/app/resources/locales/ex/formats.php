<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

return array(
	'date' => 'ex',
	'time' => 'ex',
	'datetime' => 'ex',
	'pluralForms' => 2,
	'pluralRule' => function ($n) {
		return $n != 1 ? 1 : 0;
	}
);