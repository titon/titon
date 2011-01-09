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
require SOURCE .'Paths.php';
require SOURCE .'Bootstrap.php';

/**
 * Require core internals that operate the application.
 */
require SOURCE .'utility'. DS .'Inflector.php';
require SOURCE .'utility'. DS .'Set.php';
require SOURCE .'core'. DS .'Application.php';
require SOURCE .'core'. DS .'Config.php';
require SOURCE .'core'. DS .'Environment.php';
require SOURCE .'core'. DS .'Loader.php';
require SOURCE .'core'. DS .'Prototype.php';
require SOURCE .'core'. DS .'Registry.php';
require SOURCE .'core'. DS .'Router.php';
require SOURCE .'log'. DS .'Debugger.php';
require SOURCE .'log'. DS .'Exception.php';
require SOURCE .'system'. DS .'Dispatch.php';
require SOURCE .'system'. DS .'Controller.php';
require SOURCE .'system'. DS .'Event.php';

/**
 * Initialize the application.
 */
$app = new \titon\source\core\Application();

/**
 * Require custom config and settings from the application.
 */
require CONFIG .'Setup.php';
require CONFIG .'Routes.php';
