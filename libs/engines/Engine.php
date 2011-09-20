<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\engines;

/**
 * Interface for the engines library.
 * 
 * @package	titon.libs.engines
 */
interface Engine {

	/**
	 * The current output within the rendering process. The output changes depending on the current rendering stage.
	 *
	 * @access public
	 * @return void
	 */
	public function content();

	/**
	 * Opens and renders a partial view element within the current document.
	 *
	 * @access public
	 * @param string $path
	 * @param array $variables
	 * @return string
	 */
	public function open($path, array $variables);

	/**
	 * Triggered before a template is rendered by the engine.
	 *
	 * @access public
	 * @return void
	 */
	public function preRender();

	/**
	 * Triggered after a template is rendered by the engine.
	 *
	 * @access public
	 * @return void
	 */
	public function postRender();

	/**
	 * Primary method to render a single view template.
	 *
	 * @access public
	 * @param string $path
	 * @param array $variables
	 * @return void
	 */
	public function render($path, array $variables);

	/**
	 * Begins the staged rendering process. First stage, the system must render the template based on the module, 
	 * controller and action path. Second stage, wrap the first template in any wrappers. Third stage, 
	 * wrap the current template ouput with the layout. Return the final result.
	 *
	 * @access public
	 * @return string
	 */
	public function run();

}
