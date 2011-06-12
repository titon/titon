<?php
/**
 * A required interface for all custom view engines to implement.
 * Defines all the default methods that will be used in the rendering process.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\libs\engines;

use \titon\libs\views\View;

/**
 * Interface for the engines library.
 * 
 * @package	titon.libs.engines
 */
interface Engine {

    /**
     * Output the inner content templates, as well as applying a wrapper if it exists.
     *
     * @access public
     * @return void
     */
	public function content();

    /**
	 * Triggered upon the engine class instantiation, following __construct().
	 *
	 * @access public
     * @param View $View
	 * @return void
	 */
    public function initialize(View $View);

    /**
     * Output the flash message into the view. Uses the flash.tpl element.
     *
     * @access public
     * @param array $params
     * @return string|null
     */
    public function flash(array $params = array());

    /**
     * Opens and renders a partial view element within the current document.
     * Can be called within other view templates.
     *
     * @access public
     * @param string $path
     * @param array $variables
     * @return string
     */
    public function open($path, array $variables = array());

    /**
	 * Triggered before a template is rendered by the engine.
	 *
	 * @access public
     * @param View $View
	 * @return void
	 */
    public function preRender(View $View);

    /**
	 * Triggered after a template is rendered by the engine.
	 *
	 * @access public
     * @param View $View
	 * @return void
	 */
    public function postRender(View $View);

    /**
     * Renders the layout by extracting variables into the template and returning the output.
     * The inner content will be rendered if the content() method exists in the tpl.
     * Finally, it will output the correct HTTP headers depending on the "type" property in the config.
     *
     * @access public
     * @return string
     */
	public function run();
	
}
