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
use \titon\source\core\routes\Route;

Titon::router()
	->map('news', new Route('/news', array('module' => 'core', 'controller' => 'index', 'action' => 'index')));