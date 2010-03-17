<?php

namespace titon\modules\hooks\optimizer;

use \titon\modules\hooks\HookInterface;

class Optimizer implements HookInterface {

    public function preDispatch() {
        //ini_set('zlib.output_compression', true);
    }

}