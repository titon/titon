<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2009-2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app\config\environments;

use \titon\source\system\Dispatch;
use \titon\source\system\Event;

/**
 * Overwrite the default dispatcher with a custom dispatcher.
 * Can also restrict the dispatcher to a specific scope.
 */
Dispatch::setup(function($params) {
    return new \titon\source\components\dispatchers\front\Front($params);
});

/**
 * Can register listeners to be triggered at certain events.
 */
$optimizer = new \titon\source\components\listeners\optimizer\Optimizer();
$OptimizerListener = new \titon\source\components\events\listeners\OptimizerListener($optimizer);

Event::register($OptimizerListener);