<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2009-2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app\config;

use \titon\source\Titon;

/**
 * Environments
 *
 * From here you can define all types of environmental configurations and host mappings.
 * The specific environment configuration file is loaded based on the current hostname.
 */

Titon::environment()
	->setup('development', array('localhost', '127.0.0.1'))
	->fallback('development');

/**
 * Configuration
 *
 * Define configuration values that should be global and persist across all environments.
 * Place environment specific config within the environment specific file.
 */

Titon::config()
	->set('app', array(
		'name' => 'Titon',
		'salt' => '66c63d989368170aff46040ab2353923',
		'encoding' => 'UTF-8'
	))
	->set('debug', array(
		'level' => 2,
		'email' => ''
	));