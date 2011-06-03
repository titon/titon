<?php
/**
 * The Engine acts as a base for all child Engines to inherit. The view engine acts as the renderer of data
 * (set by the Controller) to markup (the View templates), using a templating system. 
 * The order of process is as follows:
 *
 *  - The engine inherits the configuration and variables that were set in the Controller
 *  - The engine applies the configuration and loads any defined helpers and classes
 *  - Once loaded, it renders all views used within the current request
 *  - Will trigger any callbacks and shutdown
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\engines;

use \titon\base\Prototype;
use \titon\core\Config;
use \titon\log\Exception;
use \titon\libs\engines\EngineInterface;
use \titon\core\Router;
use \titon\system\View;

/**
 * Engine
 *
 * @package		Titon
 * @subpackage	Titon.Modules
 * @abstract
 */
abstract class EngineAbstract extends Prototype implements EngineInterface {

    /**
     * View object.
     *
     * @access public
     * @var View
     */
    public $View;

    /**
     * The rendered content used within the wrapper or the layout.
     *
     * @access protected
     * @var string
     */
    protected $_content = null;

    /**
     * The name of the layout tpl to load for the base HTML.
     *
     * @access protected
     * @var string
     */
    protected $_layout = 'default';

    /**
     * Has the views been completely rendered.
     *
     * @access protected
     * @var boolean
     */
    protected $_rendered = false;

    /**
     * The name of the wrapper tpl to use for the wrapping of the HTML content, within the layout.
     *
     * @access protected
     * @var string
     */
    protected $_wrapper = null;

    /**
     * Has a wrapper been applied to the layout.
     *
     * @access protected
     * @var boolean
     */
    protected $_wrapped = false;

    /**
     * Return the inner content for the layout or for the wrapper, depending if certain files exist.
     *
     * @access public
     * @return void
     */
	public function content() {
        return $this->_content;
    }

    /**
     * Output the flash message into the view. Uses the flash.tpl element.
     *
     * @access public
     * @param array $params
     * @return string|null
     */
    public function flash(array $params = array()) {
        if ($this->View->hasObject('Session')) {
            $message = $this->View->Session->get('App.flash');
            $this->View->Session->set('App.flash', '');
        } else {
            $message = Set::extract($_SESSION, 'App.flash');
            $_SESSION = Set::remove($_SESSION, 'App.flash');
        }

        if (!empty($message)) {
            $params = array('message' => $message) + $params;

            return $this->open('flash', $params);
        }
    }

    /**
     * Store the View object and define the layout and wrapper settings.
     *
     * @access public
     * @param View $View
     * @return void
     */
    public function initialize(View $View) {
        $this->View = $View;
        $this->_layout = $this->View->getConfig('layout');
        $this->_wrapper = $this->View->getConfig('wrapper');
    }

    /**
     * Opens and renders a partial view element within the current document.
     * Can be called within other view templates.
     *
     * @access public
     * @param string $path
     * @param array $variables
     * @return string
     */
    public function open($path, array $variables = array()) {
        throw new Exception('You must define the open() method within your engine.');
    }

    /**
	 * Triggered before a template is rendered by the engine.
	 *
	 * @access public
     * @param View $View
	 * @return void
	 */
    public function preRender(View $View) {
    }

    /**
	 * Triggered after a template is rendered by the engine.
	 *
	 * @access public
     * @param View $View
	 * @return void
	 */
    public function postRender(View $View) {
    }

    /**
     * Renders the inner content templates, applies a wrapper if it exists and renders the layout.
     * The inner content will be rendered if the content() method exists in the tpl.
     * Finally, it will output the correct HTTP headers depending on the "type" property in the config.
     *
     * @access public
     * @return string
     */
	public function run() {
        throw new Exception('You must define the run() method within your Engine.');
    }

}
