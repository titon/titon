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
define('DS', '/');
define('PS', PATH_SEPARATOR);
define('NS', '\\');

/**
 * Define the public html root (ROOT) and the directory containing the titon/vendors folder (SUBROOT).
 */
define('ROOT', __DIR__ . DS);
define('SUBROOT', dirname(ROOT) . DS);

/**
 * Define the paths for the titon source, libraries and vendors directories.
 */
define('TITON', SUBROOT .'titon'. DS);
define('FRAMEWORK', TITON .'source'. DS);
define('LIBRARY', TITON .'library'. DS);
define('VENDORS', SUBROOT .'vendors'. DS);

/**
 * Load the core Titon files and initialize dispatcher; throw fatal error if libraries could not be found.
 */
if (!is_file(FRAMEWORK)) {
	trigger_error('Titon: Application failed to load the core libraries. Please check your paths and configuration.', E_USER_ERROR);
}

include_once FRAMEWORK .'Infastructure.php';

/**
 * Set the include paths for all important files.
 */
$app->loader->includePaths(array(
	ROOT, SUBROOT, TITON, FRAMEWORK, LIBRARY, VENDORS
));

/**
 * Dispatch the request.
 */
$app->dispatcher->run();