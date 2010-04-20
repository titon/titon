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
 * Require files to load constants and global functions.
 */
require FRAMEWORK .'Paths.php';
require FRAMEWORK .'Bootstrap.php';

/**
 * Require core internals that operate the application.
 */
require FRAMEWORK .'utility'. DS .'Inflector.php';
require FRAMEWORK .'utility'. DS .'Set.php';
require FRAMEWORK .'core'. DS .'Application.php';
require FRAMEWORK .'core'. DS .'Configuration.php';
require FRAMEWORK .'core'. DS .'Environment.php';
require FRAMEWORK .'core'. DS .'Prototype.php';
require FRAMEWORK .'core'. DS .'Registry.php';
require FRAMEWORK .'router'. DS .'Router.php';
require FRAMEWORK .'log'. DS .'Debugger.php';
require FRAMEWORK .'log'. DS .'Exception.php';
require FRAMEWORK .'system'. DS .'Dispatch.php';
require FRAMEWORK .'system'. DS .'Controller.php';
require FRAMEWORK .'system'. DS .'Event.php';

/**
 * Require custom config and settings from the application.
 */
require CONFIG .'Environments.php';
require CONFIG .'Setup.php';
require CONFIG .'Routes.php';
require CONFIG .'Bootstrap.php';

/**
 * Initialize the application objects.
 */
\titon\core\App::initialize();
