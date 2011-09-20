<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\listeners;

use \titon\base\Prototype;
use \titon\libs\controllers\Controller;
use \titon\libs\engines\Engine;
use \titon\libs\listeners\Listener;

/**
 * Provides no functionality except the definition of skeleton methods.
 *
 * @package	titon.libs.listeners
 * @abstract
 */
abstract class ListenerAbstract extends Prototype implements Listener {

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
	 * @param Controller $controller
	 * @return void
	 */
	public function preProcess(Controller $controller) {
		return;
	}

	/**
	 * Executed after the action gets processed.
	 *
	 * @access public
	 * @param Controller $controller
	 * @return void
	 */
	public function postProcess(Controller $controller) {
		return;
	}

	/**
	 * Executed before the template gets rendered.
	 *
	 * @access public
	 * @param Engine $engine
	 * @return void
	 */
	public function preRender(Engine $engine) {
		return;
	}

	/**
	 * Executed after the template gets rendered.
	 *
	 * @access public
	 * @param Engine $engine
	 * @return void
	 */
	public function postRender(Engine $engine) {
		return;
	}

}