<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\listeners;

use \titon\libs\controllers\Controller;
use \titon\libs\engines\Engine;

/**
 * Interface for the event listeners library.
 *
 * @package	titon.libs.listeners
 */
interface Listener {

	/**
	 * Executed after kernel startup.
	 *
	 * @access public
	 * @return void
	 */
	public function startup();

	/**
	 * Executed before kernel shutdown.
	 *
	 * @access public
	 * @return void
	 */
	public function shutdown();

	/**
	 * Executed at the beginning of the dispatch cycle.
	 *
	 * @access public
	 * @return void
	 */
	public function preDispatch();

	/**
	 * Executed at the very end of the dispatch cycle.
	 *
	 * @access public
	 * @return void
	 */
	public function postDispatch();

	/**
	 * Executed before the action gets processed.
	 *
	 * @access public
	 * @param \titon\libs\controllers\Controller $controller
	 * @return void
	 */
	public function preProcess(Controller $controller);

	/**
	 * Executed after the action gets processed.
	 *
	 * @access public
	 * @param \titon\libs\controllers\Controller $controller
	 * @return void
	 */
	public function postProcess(Controller $controller);

	/**
	 * Executed before the template gets rendered.
	 *
	 * @access public
	 * @param \titon\libs\engines\Engine $engine
	 * @return void
	 */
	public function preRender(Engine $engine);

	/**
	 * Executed after the template gets rendered.
	 *
	 * @access public
	 * @param \titon\libs\engines\Engine $engine
	 * @return void
	 */
	public function postRender(Engine $engine);
    
}