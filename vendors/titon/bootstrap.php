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
define('CONSOLE', TITON . 'console' . DS);
define('LIBRARY', TITON . 'libs' . DS);
define('VENDORS', TITON . 'vendors' . DS);

/**
 * App directory constants.
 */
define('APP_CONFIG', APP . 'config' . DS);
define('APP_LIBRARY', APP . 'libs' . DS);
define('APP_MODULES', APP . 'modules' . DS);
define('APP_TEMP', APP . 'temp' . DS);
define('APP_VIEWS', APP . 'views' . DS);

/**
 * Library directory constants.
 */
define('LIB_ACTIONS', LIBRARY . 'actions' . DS);
define('LIB_ADAPTERS', LIBRARY . 'adapters' . DS);
define('LIB_BEHAVIORS', LIBRARY . 'behaviors' . DS);
define('LIB_CONTROLLERS', LIBRARY . 'controllers' . DS);
define('LIB_DISPATCHERS', LIBRARY . 'dispatchers' . DS);
define('LIB_DRIVERS', LIBRARY . 'drivers' . DS);
define('LIB_ENGINES', LIBRARY . 'engines' . DS);
define('LIB_ENUMS', LIBRARY . 'enums' . DS);
define('LIB_EXCEPTIONS', LIBRARY . 'exceptions' . DS);
define('LIB_HELPERS', LIBRARY . 'helpers' . DS);
define('LIB_LISTENERS', LIBRARY . 'listeners' . DS);
define('LIB_MODELS', LIBRARY . 'models' . DS);
define('LIB_PACKAGES', LIBRARY . 'packages' . DS);
define('LIB_READERS', LIBRARY . 'readers' . DS);
define('LIB_ROUTES', LIBRARY . 'routes' . DS);
define('LIB_SHELLS', LIBRARY . 'shells' . DS);
define('LIB_STORAGE', LIBRARY . 'storage' . DS);
define('LIB_TRAITS', LIBRARY . 'traits' . DS);
define('LIB_TRANSLATERS', LIBRARY . 'translaters' . DS);
define('LIB_TRANSPORTERS', LIBRARY . 'transporters' . DS);

/**
 * Include the necessary classes to initialize the framework.
 */
include_once TITON . 'functions.php';
include_once TITON . 'Titon.php';
include_once TITON . 'core' . DS . 'Loader.php';

/**
 * Initialize Titon.
 */
\titon\Titon::initialize();

/**
 * Include custom configuration and settings from the application.
 */
include_once APP_CONFIG . 'setup.php';
