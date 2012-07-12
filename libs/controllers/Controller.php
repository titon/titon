<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\controllers;

use titon\libs\actions\Action;
use \Closure;

/**
 * Interface for the controllers library.
 *
 * @package	titon.libs.controllers
 */
interface Controller {

	/**
	 * Dispatch the request to the correct controller action. Checks to see if the action exists and is not protected.
	 *
	 * @access public
	 * @param string $action
	 * @param array $args
	 * @return mixed
	 */
	public function dispatchAction($action = null, array $args = []);

	/**
	 * Forward the current request to a new action, instead of doing an additional HTTP request.
	 *
	 * @access public
	 * @param string $action
	 * @param array $args
	 * @return mixed
	 */
	public function forwardAction($action, array $args = []);

	/**
	 * Trigger a custom Action class.
	 *
	 * @access public
	 * @param titon\libs\actions\Action $action
	 * @return void
	 */
	public function runAction(Action $action);

	/**
	 * Functionality to throw up an error page (like a 404). The error template is derived from the $action passed.
	 *
	 * @access public
	 * @param string $action
	 * @param array $args
	 * @return void
	 */
	public function throwError($action, array $args = []);

	/**
	 * Triggered before the Controller processes the requested Action.
	 *
	 * @access public
	 * @return void
	 */
	public function preProcess();

	/**
	 * Triggered after the Action processes, but before the View renders.
	 *
	 * @access public
	 * @return void
	 */
	public function postProcess();

	/**
	 * Setup the rendering engine to use.
	 *
	 * @access public
	 * @param Closure $engine
	 * @return void
	 */
	public function setEngine(Closure $engine);

}
