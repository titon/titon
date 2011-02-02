<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2009-2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace app;

use \titon\source\Titon;
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
		$this->attachObject('request', function() {
			return Titon::registry()->factory('titon\source\net\Request');
		});

		$this->attachObject('response', function() {
			return Titon::registry()->factory('titon\source\net\Response');
		});

		// Attach an engine if it doesn't exist
		$this->setEngine(Titon::registry()->factory('titon\library\engines\titon\Renderer'));

		// Attach helpers
		$this->addHelper('html', Titon::registry()->factory('titon\library\helpers\html\Html'));
	}

}