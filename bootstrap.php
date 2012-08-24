<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

/**
 * Titon constants.
 */
define('TITON_CONSOLE', TITON . 'console/');
define('TITON_LIBS', TITON . 'libs/');
define('TITON_RESOURCES', TITON . 'resources/');

/**
 * App directory constants.
 */
define('APP_CONFIG', TITON_APP . 'config/');
define('APP_LIBS', TITON_APP . 'libs/');
define('APP_RESOURCES', TITON_APP . 'resources/');
define('APP_MODULES', TITON_APP . 'modules/');
define('APP_TEMP', TITON_APP . 'temp/');
define('APP_LOGS', APP_TEMP . 'logs/');
define('APP_VIEWS', TITON_APP . 'views/');

/**
 * Include the necessary classes to initialize the framework.
 */
include_once TITON . 'functions.php';
include_once TITON . 'Titon.php';
include_once TITON . 'core/Loader.php';

/**
 * Initialize Titon.
 */
\titon\Titon::initialize();

/**
 * Include custom configuration and settings from the application.
 */
$configs = array('bootstrap', 'loader', 'config', 'env', 'registry', 'g11n', 'router', 'event', 'dispatch', 'cache');

foreach ($configs as $config) {
	if (file_exists(APP_CONFIG . $config . '.php')) {
		include_once APP_CONFIG . $config . '.php';
	}
}

unset($configs);
