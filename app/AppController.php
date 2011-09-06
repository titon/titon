<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2009-2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app;

use \titon\Titon;
use \titon\libs\controllers\ControllerAbstract;

/**
 * A Controller class that acts as a gateway between the client controller and the system controller.
 * Allows the client controllers to inheriet base functionality, as well as share functionality between other controllers.
 *
 * @package	app
 * @uses	titon\Titon
 */
class AppController extends ControllerAbstract {

	/**
	 * Define global helpers and objects.
	 * 
	 * @access public
	 * @return type 
	 */
	public function initialize() {
		parent::initialize();
		
		$this->view->addHelper('html', function($self) {
			return Titon::registry()->factory('titon\libs\helpers\html\HtmlHelper');
		});
		
		$this->view->addHelper('asset', function($self) {
			return Titon::registry()->factory('titon\libs\helpers\html\AssetHelper');
		});
	}
	
}
