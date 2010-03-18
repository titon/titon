<?php
/**
 * The default controller used for the index page and for static content.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app\controllers;

class Pages extends \app\AppController {

	/**
	 * The index() action is called as the index page of a controller.
	 */
	public function index() {
        $this->View->set('pageTitle', 'Example Controller');
        //$this->View->render(false);

		//$this->Response->contentBody('Disabled view rendering until fixed.')->respond();
	}

}