<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2009-2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

/**
 * Compare the PHP version so that the application is running in 5.3!
 */
if (version_compare(PHP_VERSION, '5.3.0') == -1) {
	trigger_error(sprintf('Titon: Application requires PHP 5.3.x to run correctly, please upgrade your environment. You are using %s.', PHP_VERSION), E_USER_ERROR);
}

/**
 * Convenience constants for the directory, path and namespace separators.
 */
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('NS', '\\');

/**
 * Define the public html root (ROOT) and the directory containing the titon/vendors folder (SUBROOT).
 */
define('ROOT', __DIR__ . DS);
define('SUBROOT', dirname(ROOT) . DS);

/**
 * Define the paths for the titon source, components and vendors directories.
 */
define('FRAMEWORK', SUBROOT .'titon'. DS .'source'. DS);
define('COMPONENTS', SUBROOT .'titon'. DS .'components'. DS);
define('VENDORS', SUBROOT .'vendors'. DS);

/**
 * Load the core Titon files and initialize dispatcher; throw fatal error if libraries could not be found.
 */
if (!file_exists(FRAMEWORK)) {
	trigger_error('Titon: Application failed to load the core libraries. Please check your paths and configuration.', E_USER_ERROR);
}

include_once FRAMEWORK .'Infastructure.php';

/**
 * Set the include paths for all important files.
 */
set_include_path(
	implode(PS, array(get_include_path(), ROOT, SUBROOT, FRAMEWORK, COMPONENTS, VENDORS))
);

/**
 * Dispatch the request.
 */
\titon\source\system\Dispatch::initialize();
