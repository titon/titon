<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\library\helpers;

use \titon\source\system\View;

/**
 * A required interface for all Helpers to implement.
 * Defines the callbacks and the arguments that are available to the Helper.
 *
 * @package	titon.source.library.helpers;
 */
interface HelperInterface {

	/**
	 * Triggered upon the view class instantiation, following __construct().
	 *
	 * @access public
	 * @param View $view
	 * @return void
	 */
	public function initialize(View $view);

	/**
	 * Triggered before a template is rendered by the engine.
	 *
	 * @access public
	 * @param View $view
	 * @return void
	 */
	public function preRender(View $view);

	/**
	 * Triggered after a template is rendered by the engine.
	 *
	 * @access public
	 * @param View $view
	 * @return void
	 */
	public function postRender(View $view);

}