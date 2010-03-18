<?php
/**
 * Initializes the whole application.
 *
 * Base file that is requested first. The file then sets the base paths, loads and initializes the core Titon files,
 * then dispatches the current request to the correct application route.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

/**
 * Compare the PHP version so that the application is running in 5.3!
 */
if (version_compare(PHP_VERSION, '5.3.0') == -1) {
	trigger_error('Titon: Application requires PHP 5.3.x to run correctly, please upgrade your environment. You are using '. PHP_VERSION .'.', E_USER_ERROR);
}

/**
 * Convenience function for the directory, path and namespace separators.
 */
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('NS', '\\');

/**
 * Define the public html ROOT and path beneath (SUBROOT).
 */
define('ROOT', __DIR__ . DS);
define('SUBROOT', dirname(ROOT) . DS);

define('APP', ROOT .'app'. DS);
define('MODULES', ROOT .'modules'. DS);
define('VENDORS', ROOT .'vendors'. DS);
define('FRAMEWORK', ROOT .'titon'. DS);

/**
 * Load the core Titon files and initialize dispatcher;
 * Throw fatal error if libraries could not be found.
 */
if (!file_exists(FRAMEWORK)) {
	trigger_error('Titon: Application failed to load the core libraries. Please check your paths and configuration.', E_USER_ERROR);
}

include_once FRAMEWORK .'Infastructure.php';

/**
 * Set the include paths for all important files.
 */
set_include_path(
	implode(PS, array(get_include_path(), ROOT, SUBROOT, APP, FRAMEWORK, MODULES, VENDORS))
);

/**
 * Dispatch the request.
 */
\titon\system\Dispatch::initialize();
