<?php
/**
 * Defined constants for all the pathing required in the application. All paths end in a trailing slash.
 *
 * @copyright		Copyright 2009, Titon (A PHP Micro Framework)
 * @link			http://titonphp.com
 * @license			http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

/**
 * App directory constants.
 */
define('APP_CONFIG', ROOT .'config'. DS);
define('APP_LIBRARY', ROOT .'library'. DS);
define('APP_MODULES', ROOT .'modules'. DS);
define('APP_TEMP', ROOT .'TEMP'. DS);

/*define('ACTIONS', APP .'actions'. DS);
define('CONFIG', APP .'config'. DS);
define('CONTROLLERS', APP .'controllers'. DS);
define('MODELS', APP .'models'. DS);
define('TEMP', APP .'temp'. DS);
define('TEMPLATES', APP .'templates'. DS);
define('VIEWS', APP .'views'. DS);*/

/**
 * Webroot directory constants.
 *
define('WEBROOT', ROOT .'web'. DS);
define('CSS', WEBROOT .'css'. DS);
define('CSSR', 'css/');
define('JS', WEBROOT .'js'. DS);
define('JSR', 'js/');
define('IMG', WEBROOT .'images'. DS);
define('IMGR', 'images/');

/**
 * Modules directory constants.
 
define('ADAPTERS', MODULES .'adapters'. DS);
define('DISPATCHERS', MODULES .'dispatchers'. DS);
define('DRIVERS', MODULES .'drivers'. DS);
define('ENGINES', MODULES .'engines'. DS);
define('EVENTS', MODULES .'events'. DS);
define('HELPERS', MODULES .'helpers'. DS);

/**
 * Vendors directory constants.
 
define('COMPONENTS', VENDORS .'components'. DS);
define('SHELLS', VENDORS .'shells'. DS);*/