<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\actions;

use titon\base\Base;
use titon\libs\actions\Action;
use titon\libs\actions\ActionException;
use titon\libs\controllers\Controller;
use titon\libs\traits\Attachable;

/**
 * The Action is a sub-routine of the Controller parent and is packaged as a stand-alone object instead of a method.
 * An Action object gives you the flexibility of re-using actions and specific logic across multiple
 * Controllers, encapsulating additional methods within the Action process, and defining its own attachments.
 *
 * @package	titon.libs.actions
 * @abstract
 */
abstract class ActionAbstract extends Base implements Action {
	use Attachable;

	/**
	 * Controller object.
	 *
	 * @access protected
	 * @var \titon\libs\controllers\Controller
	 */
	protected $_controller;

	/**
	 * Method that is executed to trigger the actions logic.
	 *
	 * @access public
	 * @return void
	 * @throws \titon\libs\actions\ActionException
	 */
	public function run() {
		throw new ActionException(sprintf('You must define the run() method within your %s Action.', get_class($this)));
	}

	/**
	 * Store the parent Controller.
	 *
	 * @access public
	 * @param \titon\libs\controllers\Controller $controller
	 * @return \titon\libs\actions\Action
	 */
	public function setController(Controller $controller) {
		$this->_controller = $controller;

		return $this;
	}

}