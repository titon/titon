<?php
/**
 * A Controller class that acts as a gateway between the client controller and the system controller.
 * Allows the client controllers to inheriet base functionality, as well as share functionality between other controllers.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app;

use \titon\core\Registry;

/**
 * Application Controller Class
 *
 * @package		Titon
 * @subpackage	App
 */
class AppController extends \titon\system\Controller {

    /**
     * Construct the Request and Response objects. Allows you to overwrite or remove for high customization.
     *
     * @access public
     * @return void
     */
    public function construct() {
        $this->attachObject('Request', function() {
            return Registry::factory('titon.http.Request');
        });

        $this->attachObject('Response', function() {
            return Registry::factory('titon.http.Response');
        });
    }

}
