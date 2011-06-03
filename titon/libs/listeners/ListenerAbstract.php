<?php
/**
 * The Event Listener loads the external object as well as defining skeleton callbacks.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\libs\listeners;

use \titon\base\Base;
use \titon\libs\events\ListenerInterface;
use \titon\system\Controller;
use \titon\system\View;

/**
 * Event Listener Abstract
 *
 * @package		Titon
 * @subpackage	Titon.Modules.Events
 */
abstract class ListenerAbstract extends Base implements ListenerInterface {

	/**
	 * Executed at the beginning of the dispatch cycle.
	 *
	 * @access public
	 * @return void
	 */
    public function preDispatch() {
	}

	/**
	 * Executed at the very end of the dispatch cycle.
	 *
	 * @access public
	 * @return void
	 */
	public function postDispatch() {
	}

	/**
	 * Executed before the action gets processed.
	 *
	 * @access public
	 * @param Controller $Controller
	 * @return void
	 */
	public function preProcess(Controller $Controller) {
	}

	/**
	 * Executed after the action gets processed.
	 *
	 * @access public
	 * @param Controller $Controller
	 * @return void
	 */
	public function postProcess(Controller $Controller) {
	}

	/**
	 * Executed before the template gets rendered.
	 *
	 * @access public
	 * @param View $View
	 * @return void
	 */
	public function preRender(View $View) {
	}

	/**
	 * Executed after the template gets rendered.
	 *
	 * @access public
	 * @param View $View
	 * @return void
	 */
	public function postRender(View $View) {
	}

}