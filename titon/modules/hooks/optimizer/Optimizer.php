<?php

namespace titon\modules\hooks\optimizer;

use \titon\modules\hooks\HookAbstract;
use \titon\system\Controller;
use \titon\system\View;

class Optimizer extends HookAbstract {

    public function preProcess(Controller $Controller) {
		debug($Controller);
        //ini_set('zlib.output_compression', true);
    }

}