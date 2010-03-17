<?php
/**
 * A View class that acts as a gateway between the client view and the system view.
 * Allows the client views to inheriet base functionality, as well as share functionality between other views.
 * If no View is found at the routed path, the AppView will be used in its place.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app;

use \titon\core\Registry;

/**
 * Application View Class
 *
 * @package		Titon
 * @subpackage	App
 */
class AppView extends \titon\system\View {
    
    /**
     * Construct the Engine and Helper object(s). Allows you to overwrite or remove for high customization.
     *
     * @access public
     * @return void
     */
    public function construct() {
        // Inherit the referenced Request and Response from the Controller
        $this->attachObject('Request', function() {
            return Registry::factory('titon.http.Request');
        });

        $this->attachObject('Response', function() {
            return Registry::factory('titon.http.Response');
        });

        // Attach an engine if it doesn't exist
        if (!$this->hasObject('Engine')) {
            $this->attachObject('Engine', function() {
                return Registry::factory('titon.modules.engines.titon.Renderer');
            });
        }

        // Attach helpers
        $this->attachObject('Html', function() {
            return Registry::factory('titon.modules.helpers.html.Html');
        });
    }

}