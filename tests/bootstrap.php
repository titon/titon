<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

date_default_timezone_set('America/New_York');

/**
 * Define primary constants.
 */
define('TITON_APP', __DIR__ . '/app/');
define('TITON_VENDORS', dirname(__DIR__) . '/vendors/');
define('TITON_SOURCE', TITON_VENDORS . 'titon/');

/**
 * Include scripts.
 */
include_once 'PHPUnit/Autoload.php';
include_once TITON_SOURCE . 'bootstrap.php';

/**
 * Start class with fake environment.
 */
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_ADDR'] = '';