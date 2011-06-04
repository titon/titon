<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

/**
 * Define primary constants.
 */
define('DS', '/');
define('PS', PATH_SEPARATOR);
define('NS', '\\');
define('APP', dirname(__DIR__) . DS .'app'. DS);
define('ROOT', dirname(__DIR__) . DS);
define('TITON', ROOT .'titon'. DS);

/**
 * Include include_onced scripts.
 */
include_once TITON .'bootstrap.php';
include_once TITON .'functions.php';
include_once TITON .'Titon.php';
include_once TITON .'core'. DS .'Loader.php';

/**
 * Start class with fake environment.
 */
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REQUEST_URI'] = '/';

\titon\Titon::initialize();
\titon\Titon::startup();
