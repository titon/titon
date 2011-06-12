<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */
 
namespace titon\libs\controllers;

use \titon\libs\actions\Action;
use \titon\libs\views\View;

/**
 * Interface for the controllers library.
 *
 * @package	titon.libs.controllers
 */
interface Controller {

	/**
	 * Trigger a custom Action class.
	 *
	 * @access public
	 * @param Action $Action
	 * @return void
	 */
	public function action(Action $action);

	/**
	 * Dispatch the request to the correct controller action. Checks to see if the action exists and is not protected.
	 *
	 * @access public
	 * @param string $action
	 * @param array $args
	 * @return mixed
	 */
	public function dispatch($action, array $args);

	/**
	 * Functionality to throw up an error page (like a 404). The error template is derived from the $action passed.
	 *
	 * @access public
	 * @param string $action
	 * @param array $args
	 * @return void
	 */
	public function error($action, array $args);

	/**
	 * Forward the current request to a new action, instead of doing an additional HTTP request.
	 *
	 * @access public
	 * @param string $action
	 * @param array $args
	 * @return mixed
	 */
	public function forward($action, array $args);

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
	 * Configure the Controller and store the View object.
	 *
	 * @access public
	 * @param View $view
	 * @return void
	 */
	public function setView(View $view);

}
