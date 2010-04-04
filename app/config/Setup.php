<?php
/**
 * Setup and initialize any core components or modules.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app\config;

use \titon\core\Environment;
use \titon\system\Dispatch;
use \titon\system\Hook;

/**
 * Overwrite the default dispatcher with a custom dispatcher.
 * Can also restrict the dispatcher to a specific scope.
 */

switch (Environment::detect()) {
	case 'production':

		Dispatch::setup(function($params) {
			return new \titon\modules\dispatchers\front\Front($params);
		});
		
	break;
}


// Testing the Optimizer hook
$optimizer = new \titon\modules\hooks\optimizer\Optimizer();
$optimizerCommand = new \titon\modules\hooks\optimizer\OptimizerCommand($optimizer);
Hook::register($optimizerCommand);