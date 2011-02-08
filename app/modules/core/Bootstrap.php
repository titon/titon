<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2009-2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app\modules\core;

use \titon\source\Titon;

/**
 * Each module contains its own specific bootstrap file. This bootstrap is automatically included within the dispatch cycle
 * if the current request directs to this module. The bootstrap can be used to quickly configure the module and its controllers,
 * models, and whatever logic it may have. You may also place custom global functions here specific to this module.
 */

Titon::app()
	->addModule('core', array('index'))
	->setDefaultModule('core');
