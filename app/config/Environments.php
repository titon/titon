<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2009-2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app\config;

use \titon\source\core\Environment;

/**
 * Setup all your environment configurations within this file. An environment is passed an array of configurtion options
 * which power certain aspects of the framework. These options can be retrieved or overwritten using the Config class.
 *
 * Setup your development environment by applying the core settings.
 * To setup a production environment, copy the code below and create a production version by switching the hosts.
 */
Environment::setup('development', array(
	'Hosts'				=> array('localhost', '127.0.0.1'),
	'App.name'			=> 'Titon',
	'App.salt'			=> '',
	'App.encoding'		=> 'UTF-8',
	'Debug.level'		=> 2,
	'Debug.email'		=> '',
	'Cache.enabled'		=> false,
	'Cache.expires'		=> '+1 hour',
	'Locale.current'	=> 'en_US',
	'Locale.default'	=> 'en_US',
	'Locale.timezone'	=> 'America/Los_Angeles',
));


/**
 * Set the default environment to use if an environment is found without a matching host.
 */
Environment::setDefault('development');