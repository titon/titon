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
define('TITON_CONSOLE', TITON_SOURCE . 'console/');
define('TITON_LIBS', TITON_SOURCE . 'libs/');
define('TITON_RESOURCES', TITON_SOURCE . 'resources/');

/**
 * App directory constants.
 */
define('APP_CONFIG', TITON_APP . 'config/');
define('APP_LIBS', TITON_APP . 'libs/');
define('APP_RESOURCES', TITON_APP . 'resources/');
define('APP_MODULES', TITON_APP . 'modules/');
define('APP_TEMP', TITON_APP . 'temp/');
define('APP_VIEWS', TITON_APP . 'views/');

/**
 * Library directory constants.
 */
define('LIB_ACTIONS', TITON_LIBS . 'actions/');
define('LIB_ADAPTERS', TITON_LIBS . 'adapters/');
define('LIB_BEHAVIORS', TITON_LIBS . 'behaviors/');
define('LIB_CONTROLLERS', TITON_LIBS . 'controllers/');
define('LIB_DISPATCHERS', TITON_LIBS . 'dispatchers/');
define('LIB_DRIVERS', TITON_LIBS . 'drivers/');
define('LIB_ENGINES', TITON_LIBS . 'engines/');
define('LIB_ENUMS', TITON_LIBS . 'enums/');
define('LIB_EXCEPTIONS', TITON_LIBS . 'exceptions/');
define('LIB_HELPERS', TITON_LIBS . 'helpers/');
define('LIB_LISTENERS', TITON_LIBS . 'listeners/');
define('LIB_MODELS', TITON_LIBS . 'models/');
define('LIB_PACKAGES', TITON_LIBS . 'packages/');
define('LIB_READERS', TITON_LIBS . 'readers/');
define('LIB_ROUTES', TITON_LIBS . 'routes/');
define('LIB_SHELLS', TITON_LIBS . 'shells/');
define('LIB_STORAGE', TITON_LIBS . 'storage/');
define('LIB_TRAITS', TITON_LIBS . 'traits/');
define('LIB_TRANSLATORS', TITON_LIBS . 'translators/');
define('LIB_TRANSPORTERS', TITON_LIBS . 'transporters/');

/**
 * Include the necessary classes to initialize the framework.
 */
include_once TITON_SOURCE . 'functions.php';
include_once TITON_SOURCE . 'Titon.php';
include_once TITON_SOURCE . 'core/Loader.php';

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
