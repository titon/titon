<?php

namespace app\config\environments;

use \titon\system\Dispatch;
use \titon\system\Event;

/**
 * Overwrite the default dispatcher with a custom dispatcher.
 * Can also restrict the dispatcher to a specific scope.
 */
Dispatch::setup(function($params) {
    return new \titon\modules\dispatchers\front\Front($params);
});

/**
 * Can register listeners to be triggered at certain events.
 */
$optimizer = new \titon\modules\events\optimizer\Optimizer();
$OptimizerListener = new \titon\modules\events\optimizer\OptimizerListener($optimizer);

Event::register($OptimizerListener);
