<?php

$dir = glob(__DIR__ . '/*', GLOB_ONLYDIR);

echo '<pre>'. print_r(array_map('basename', $dir), true) .'</pre>';