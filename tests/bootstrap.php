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
include_once TITON .'source'. DS .'Titon.php';
include_once TITON .'source'. DS .'Paths.php';
include_once TITON .'source'. DS .'Bootstrap.php';
include_once TITON .'source'. DS .'Titon.php';
include_once SOURCE .'utility'. DS .'Inflector.php';
include_once SOURCE .'utility'. DS .'Set.php';
include_once SOURCE .'log'. DS .'Debugger.php';
include_once SOURCE .'log'. DS .'Exception.php';
include_once SOURCE .'core'. DS .'Application.php';
include_once SOURCE .'core'. DS .'Config.php';
include_once SOURCE .'core'. DS .'Dispatch.php';
include_once SOURCE .'core'. DS .'Environment.php';
include_once SOURCE .'core'. DS .'Event.php';
include_once SOURCE .'core'. DS .'Loader.php';
include_once SOURCE .'core'. DS .'Registry.php';
include_once SOURCE .'core'. DS .'Router.php';
include_once SOURCE .'system'. DS .'Object.php';
include_once SOURCE .'system'. DS .'Prototype.php';

/**
 * Start class with fake environment.
 */
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REQUEST_URI'] = '/';

\titon\source\Titon::initialize();
\titon\source\Titon::startup();
