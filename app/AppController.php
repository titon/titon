<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2009-2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app;

use \titon\source\core\Registry;

/**
 * A Controller class that acts as a gateway between the client controller and the system controller.
 * Allows the client controllers to inheriet base functionality, as well as share functionality between other controllers.
 *
 * @package		Titon
 * @subpackage	App
 */
class AppController extends \titon\source\system\Controller {

	/**
	 * Construct the Request and Response objects.
	 * Allows you to overwrite or remove for high customization.
	 *
	 * @access public
	 * @return void
	 */
	public function construct() {
		$this->attachObject('request', function() {
			return Registry::factory('titon.source.net.Request');
		});

		$this->attachObject('response', function() {
			return Registry::factory('titon.source.net.Response');
		});
	}

}
