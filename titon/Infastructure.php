<?php
/**
 * Includes all required core files for the framework, and loads all the application core settings files.
 * Once included, initializes required objects and defines basic setup.
 *
 * @copyright		Copyright 2009, Titon (A PHP Micro Framework)
 * @link			http://titonphp.com
 * @license			http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

/**
 * Compare the PHP version so that the application is running in 5.3!
 */
if (version_compare(PHP_VERSION, '5.3.0') == -1) {
	trigger_error('Titon: Application requires PHP 5.3.x to run correctly, please upgrade your environment. You are using '. PHP_VERSION .'.', E_USER_ERROR);
}

/**
 * Require files to load constants and global functions.
 */
require_once FRAMEWORK .'Paths.php';
require_once FRAMEWORK .'Bootstrap.php';

/**
 * Require core internals that operate the application.
 */
require_once FRAMEWORK .'utility'. DS .'Inflector.php';
require_once FRAMEWORK .'utility'. DS .'Set.php';
require_once FRAMEWORK .'core'. DS .'Application.php';
require_once FRAMEWORK .'core'. DS .'Configuration.php';
require_once FRAMEWORK .'core'. DS .'Environment.php';
require_once FRAMEWORK .'core'. DS .'Prototype.php';
require_once FRAMEWORK .'core'. DS .'Registry.php';
require_once FRAMEWORK .'router'. DS .'Router.php';
require_once FRAMEWORK .'log'. DS .'Debugger.php';
require_once FRAMEWORK .'log'. DS .'Error.php';
require_once FRAMEWORK .'log'. DS .'Exception.php';
require_once FRAMEWORK .'system'. DS .'Dispatch.php';
require_once FRAMEWORK .'system'. DS .'Controller.php';
require_once FRAMEWORK .'system'. DS .'Hook.php';

/**
 * Require custom config and settings from the application.
 */
require_once CONFIG .'Environments.php';
require_once CONFIG .'Setup.php';
require_once CONFIG .'Routes.php';
require_once CONFIG .'Bootstrap.php';

/**
 * Initialize the application objects.
 */
\titon\core\App::initialize();
