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
define('TITON_CONSOLE', TITON_SOURCE . 'console' . DS);
define('TITON_LIBS', TITON_SOURCE . 'libs' . DS);
define('TITON_RESOURCES', TITON_SOURCE . 'resources' . DS);

/**
 * App directory constants.
 */
define('APP_CONFIG', TITON_APP . 'config' . DS);
define('APP_LIBS', TITON_APP . 'libs' . DS);
define('APP_MODULES', TITON_APP . 'modules' . DS);
define('APP_TEMP', TITON_APP . 'temp' . DS);
define('APP_VIEWS', TITON_APP . 'views' . DS);

/**
 * Resource directory constants.
 */
define('RES_LOCALES', TITON_RESOURCES . 'locales' . DS);

/**
 * Library directory constants.
 */
define('LIB_ACTIONS', TITON_LIBS . 'actions' . DS);
define('LIB_ADAPTERS', TITON_LIBS . 'adapters' . DS);
define('LIB_BEHAVIORS', TITON_LIBS . 'behaviors' . DS);
define('LIB_CONTROLLERS', TITON_LIBS . 'controllers' . DS);
define('LIB_DISPATCHERS', TITON_LIBS . 'dispatchers' . DS);
define('LIB_DRIVERS', TITON_LIBS . 'drivers' . DS);
define('LIB_ENGINES', TITON_LIBS . 'engines' . DS);
define('LIB_ENUMS', TITON_LIBS . 'enums' . DS);
define('LIB_EXCEPTIONS', TITON_LIBS . 'exceptions' . DS);
define('LIB_HELPERS', TITON_LIBS . 'helpers' . DS);
define('LIB_LISTENERS', TITON_LIBS . 'listeners' . DS);
define('LIB_MODELS', TITON_LIBS . 'models' . DS);
define('LIB_PACKAGES', TITON_LIBS . 'packages' . DS);
define('LIB_READERS', TITON_LIBS . 'readers' . DS);
define('LIB_ROUTES', TITON_LIBS . 'routes' . DS);
define('LIB_SHELLS', TITON_LIBS . 'shells' . DS);
define('LIB_STORAGE', TITON_LIBS . 'storage' . DS);
define('LIB_TRAITS', TITON_LIBS . 'traits' . DS);
define('LIB_TRANSLATORS', TITON_LIBS . 'translators' . DS);
define('LIB_TRANSPORTERS', TITON_LIBS . 'transporters' . DS);

/**
 * Include the necessary classes to initialize the framework.
 */
include_once TITON_SOURCE . 'functions.php';
include_once TITON_SOURCE . 'Titon.php';
include_once TITON_SOURCE . 'core' . DS . 'Loader.php';

/**
 * Initialize Titon.
 */
\titon\Titon::initialize();

/**
 * Include custom configuration and settings from the application.
 */
if (file_exists(APP_CONFIG . 'setup.php')) {
	include_once APP_CONFIG . 'setup.php';
}
