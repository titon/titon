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

/**
 * Overwrite the default dispatcher with a custom dispatcher.
 * Can also restrict the dispatcher to a specific scope.
 */

switch (Environment::detect()) {
	case 'production':

		Dispatch::setup(array(
			'container' => '*',
			'controller' => '*'
		), function($params) {
			return new \titon\modules\dispatchers\front\Front($params);
		});
		
	break;
}