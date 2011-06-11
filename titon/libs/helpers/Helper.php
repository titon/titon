<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\helpers;

use \titon\system\View;

/**
 * A Helper is a class that helps in the rendering and presentation of the view templates. 
 * This interface defines the basic callback methods.
 *
 * @package	titon.libs.helpers;
 */
interface Helper {

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