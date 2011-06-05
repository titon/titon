<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\system;

use \titon\base\Prototype;
use \titon\system\Controller;
use \titon\system\SystemException;

/**
 * The Action is a sub-routine of the Controller parent and is packaged as a stand-alone object instead of a method.
 * An Action object gives you the flexibility of re-using actions and specific logic across multiple
 * Controllers, encapsulating additional methods within the Action process, and defining its own attachments.
 *
 * @package	titon.system
 */
class Action extends Prototype {

	/**
	 * Controller object.
	 *
	 * @see Controller
	 * @access protected
	 * @var object
	 */
	protected $controller;

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
	 * The primary method that is executed for an Action.
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		throw new SystemException('You must define the run() method within your Action.');
	}

}