<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\actions;

use \titon\base\Prototype;
use \titon\libs\actions\Action;
use \titon\libs\actions\ActionException;
use \titon\libs\controllers\Controller;

/**
 * The Action is a sub-routine of the Controller parent and is packaged as a stand-alone object instead of a method.
 * An Action object gives you the flexibility of re-using actions and specific logic across multiple
 * Controllers, encapsulating additional methods within the Action process, and defining its own attachments.
 *
 * @package	titon.libs.actions
 * @uses	titon\libs\actions\ActionException
 * @abstract
 */
abstract class ActionAbstract extends Prototype implements Action {

	/**
	 * Controller object.
	 *
	 * @access public
	 * @var Controller
	 */
	public $controller;

	/**
	 * Store the parent Controller.
	 *
	 * @access public
	 * @param Controller $controller
	 * @return void
	 */
	public function setController(Controller $controller) {
		$this->controller = $controller;
	}

	/**
	 * Method that is executed to trigger the actions logic.
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		throw new ActionException(spritnf('You must define the run() method within your %s Action.', get_class($this)));
	}

}