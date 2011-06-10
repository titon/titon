<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\listeners;

use \titon\base\Base;
use \titon\libs\listeners\Listener;
use \titon\system\Controller;
use \titon\system\View;

/**
 * Provides no functionality except the definition of skeleton methods.
 *
 * @package	titon.libs.listeners
 */
abstract class ListenerAbstract extends Base implements Listener {

	/**
	 * Executed at the beginning of the dispatch cycle.
	 *
	 * @access public
	 * @return void
	 */
	public function preDispatch() {
		return;
	}

	/**
	 * Executed at the very end of the dispatch cycle.
	 *
	 * @access public
	 * @return void
	 */
	public function postDispatch() {
		return;
	}

	/**
	 * Executed before the action gets processed.
	 *
	 * @access public
	 * @param Controller $Controller
	 * @return void
	 */
	public function preProcess(Controller $Controller) {
		return;
	}

	/**
	 * Executed after the action gets processed.
	 *
	 * @access public
	 * @param Controller $Controller
	 * @return void
	 */
	public function postProcess(Controller $Controller) {
		return;
	}

	/**
	 * Executed before the template gets rendered.
	 *
	 * @access public
	 * @param View $View
	 * @return void
	 */
	public function preRender(View $View) {
		return;
	}

	/**
	 * Executed after the template gets rendered.
	 *
	 * @access public
	 * @param View $View
	 * @return void
	 */
	public function postRender(View $View) {
		return;
	}

}