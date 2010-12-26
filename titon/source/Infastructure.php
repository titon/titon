<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
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
require FRAMEWORK .'core'. DS .'Config.php';
require FRAMEWORK .'core'. DS .'Environment.php';
require FRAMEWORK .'core'. DS .'Loader.php';
require FRAMEWORK .'core'. DS .'Prototype.php';
require FRAMEWORK .'core'. DS .'Registry.php';
require FRAMEWORK .'core'. DS .'Router.php';
require FRAMEWORK .'log'. DS .'Debugger.php';
require FRAMEWORK .'log'. DS .'Exception.php';
require FRAMEWORK .'system'. DS .'Dispatch.php';
require FRAMEWORK .'system'. DS .'Controller.php';
require FRAMEWORK .'system'. DS .'Event.php';

/**
 * Initialize the application.
 */
$app = new \titon\source\core\Application();

/**
 * Require custom config and settings from the application.
 */
require CONFIG .'Environments.php';
require CONFIG .'Setup.php';
require CONFIG .'Routes.php';
