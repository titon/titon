<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2009-2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app\modules\core\controllers;

/**
 * By default the framework will determine which controller is the "index" controller of a module
 * by matching the module name with the controller name: core module -> core controller.
 */
class CoreController extends \app\AppController {

	/**
	 * The index() action is called automatically as the index page of a controller.
	 */
	public function index() {
		$this->View->set('pageTitle', 'Titon: Controller');
	}

}