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
require TITON .'paths.php';
require TITON .'bootstrap.php';
require TITON .'Titon.php';

/**
 * Require core internals that operate the application.
 */
require SOURCE .'utility'. DS .'Inflector.php';
require SOURCE .'utility'. DS .'Set.php';
require SOURCE .'log'. DS .'Debugger.php';
require SOURCE .'log'. DS .'Exception.php';
require SOURCE .'core'. DS .'Application.php';
require SOURCE .'core'. DS .'Config.php';
require SOURCE .'core'. DS .'Dispatch.php';
require SOURCE .'core'. DS .'Environment.php';
require SOURCE .'core'. DS .'Event.php';
require SOURCE .'core'. DS .'Loader.php';
require SOURCE .'core'. DS .'Registry.php';
require SOURCE .'core'. DS .'Router.php';
require SOURCE .'system'. DS .'Object.php';
require SOURCE .'system'. DS .'Prototype.php';

/**
 * Initialize Titon.
 */
\titon\source\Titon::initialize();

/**
 * Require custom config and settings from the application.
 */
require APP_CONFIG .'setup.php';
require APP_CONFIG .'routes.php';
