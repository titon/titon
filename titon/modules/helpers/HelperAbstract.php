<?php
/**
 * The Helper class acts as the base for all children helpers to extend.
 * Defines methods and properties for HTML tags, HTML attribute generation, and Doctype detection.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\helpers;

use \titon\core\App;
use \titon\core\Prototype;
use \titon\modules\helpers\HelperInterface;
use \titon\system\View;

/**
 * Helper Class
 *
 * @package		Titon
 * @subpackage	Titon.Modules
 * @abstract
 */
abstract class HelperAbstract extends Prototype implements HelperInterface {

    /**
     * A list of all HTML and XHTML tags used within the current helper.
     * If an element has multiple variations, it is represented with an array.
     *
     * @access protected
     * @var string
     */
    protected $_tags = array();

    /**
	 * Store the View object within the Helper, and set its default doctype.
	 *
	 * @access public
     * @param View $View
	 * @return void
	 */
    public function initialize(View $View) {
        if ($this->_initialized) {
            return;
        }
        
        $this->View = $View;
        $this->initialized();
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
     * Parses an array of attributes to the HTML equivalent.
     *
     * @access public
     * @param array $attributes
     * @param array $remove
     * @return string
     */
    public function attributes(array $attributes, array $remove = array()) {
        $parsed = null;
        $escape = true;

        if (isset($attributes['escape']) && is_bool($attributes['escape'])) {
            $escape = $attributes['escape'];
        }
        unset($attributes['escape']);

        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                if (in_array($key, $remove)) {
                    unset($attributes[$key]);
                    continue;
                }

                if ($escape === true) {
                    $value = htmlentities($value, ENT_COMPAT, App::charset());
                }

                $parsed .= ' '. strtolower($key) .'="'. $value .'"';
            }
        }

        return $parsed;
    }

    /**
     * Determines which doctype is currently being used.
     *
     * @access public
     * @param string $type
     * @return boolean
     */
    public function isDoctype($type = 'xhtml') {
        return (substr($this->View->getConfig('doctype'), 0, strlen($type)) == $type);
    }

    /**
     * Determines whether or not to return an HTML or XHTML tag.
     *
     * @access public
     * @return string
     */
    public function tag() {
        $args = func_get_args();
        $tagName = array_shift($args);

        if (is_array($this->_tags[$tagName])) {
            $tag = $this->_tags[$tagName][($this->isDoctype('xhtml') ? 1 : 0)];
        } else {
            $tag = $this->_tags[$tagName];
        }

        return vsprintf($tag, $args) ."\n";
    }
    
}