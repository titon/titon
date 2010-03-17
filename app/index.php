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
 * Convenience function for the directory, path and namespace separators.
 */
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('NS', '\\');

/**
 * Define the root parent folder.
 */
define('ROOT', __DIR__ . DS);
define('SUBROOT', dirname(ROOT) . DS);

define('APP', ROOT);
define('MODULES', SUBROOT .'modules'. DS);
define('FRAMEWORK', SUBROOT .'titon'. DS);
define('VENDORS', SUBROOT .'vendors'. DS);

/**
 * Load the core Titon files and initialize dispatcher;
 * Throw fatal error if libraries could not be found.
 */
if (!file_exists(FRAMEWORK)) {
	trigger_error('Titon: Application failed to load the core libraries. Please check your paths and configuration.', E_USER_ERROR);
}

include_once FRAMEWORK .'Infastructure.php';

/**
 * Set the include paths for all important files
 */
set_include_path(
	implode(PS, array(get_include_path(), ROOT, SUBROOT, FRAMEWORK, MODULES, VENDORS))
);

/**
 * Dispatch the request to the proper controller
 */
\titon\system\Dispatch::initialize();
