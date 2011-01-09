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

declare(encoding='UTF-8');

/**
 * Convenience constants for the directory, path and namespace separators.
 */
define('DS', '/');
define('PS', PATH_SEPARATOR);
define('NS', '\\');

/**
 * Define the folders that contain the app and titon files.
 */
define('APP', __DIR__ . DS);
define('ROOT', dirname(ROOT) . DS);
define('TITON', ROOT .'titon'. DS);

/**
 * Load the core Titon files and initialize dispatcher; throw fatal error if libraries could not be found.
 */
if (!is_dir(TITON .'source')) {
	trigger_error('Titon: Application failed to load the core libraries. Please check your paths and configuration.', E_USER_ERROR);
}

include_once TITON .'source'. DS .'Infrastructure.php';

/**
 * Set the include paths.
 */
$app->loader->includePaths(array(
	APP, ROOT, TITON, SOURCE, LIBRARY, VENDORS
));

/**
 * Dispatch the request.
 */
$app->dispatcher->run();