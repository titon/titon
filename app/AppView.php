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
 * The application view acts as the gateway between the controller logic and the templates.
 * Any attachment of view helpers or engines should be done within this class.
 *
 * @package		Titon
 * @subpackage	App
 */
class AppView extends \titon\source\system\View {

	/**
	 * Construct the Engine and Helper object(s). Allows you to overwrite or remove for high customization.
	 *
	 * @access public
	 * @return void
	 */
	public function construct() {
		// Inherit the referenced Request and Response from the Controller
		$this->attachObject('Request', function() {
			return Registry::factory('titon.source.http.Request');
		});

		$this->attachObject('Response', function() {
			return Registry::factory('titon.source.http.Response');
		});

		// Attach an engine if it doesn't exist
		$this->attachObject('Engine', function() {
			return Registry::factory('titon.source.components.engines.titon.Renderer');
		});

		// Attach helpers
		$this->attachObject('Html', function() {
			return Registry::factory('titon.source.components.helpers.html.Html');
		});
	}

}