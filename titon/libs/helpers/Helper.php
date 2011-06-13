<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\helpers;

use \titon\libs\engines\Engine;

/**
 * Interface for the helpers library.
 *
 * @package	titon.libs.helpers;
 */
interface Helper {

	/**
	 * Triggered before a template is rendered by the engine.
	 *
	 * @access public
	 * @param Engine $engine
	 * @return void
	 */
	public function preRender(Engine $engine);

	/**
	 * Triggered after a template is rendered by the engine.
	 *
	 * @access public
	 * @param Engine $engine
	 * @return void
	 */
	public function postRender(Engine $engine);

}