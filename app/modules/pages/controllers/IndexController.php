<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2009-2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app\modules\pages\controllers;

use \app\AppController;

/**
 * By default the framework will determine which controller is the "index" controller of a module
 * by matching the module name with the controller name: pages module -> index controller.
 */
class IndexController extends AppController {

	/**
	 * The index() action is called automatically as the index page of a controller.
	 */
	public function index() {
		$this->view->set('pageTitle', 'Titon: Controller');
	}

}