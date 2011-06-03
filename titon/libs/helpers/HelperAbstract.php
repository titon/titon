<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\helpers;

use \titon\Titon;
use \titon\base\Prototype;
use \titon\libs\helpers\HelperInterface;
use \titon\system\View;

/**
 * The Helper class acts as the base for all children helpers to extend.
 * Defines methods and properties for HTML tags and attribute generation.
 *
 * @package	titon.source.library.helpers
 * @uses	titon\Titon
 * @abstract
 */
abstract class HelperAbstract extends Prototype implements HelperInterface {

	/**
	 * View class.
	 *
	 * @see View
	 * @access protected
	 * @var object
	 */
	protected $view;

	/**
	 * A list of all HTML tags used within the current helper.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tags = array();

	/**
	 * Parses an array of attributes to the HTML equivalent.
	 *
	 * @access public
	 * @param array $attributes
	 * @param array $remove
	 * @return string
	 */
	public function attributes(array $attributes, array $remove = array()) {
		$parsed = '';
		$escape = true;

		if (isset($attributes['escape']) && is_bool($attributes['escape'])) {
			$escape = $attributes['escape'];
			unset($attributes['escape']);
		}

		if (!empty($attributes)) {
			foreach ($attributes as $key => $value) {
				if (in_array($key, $remove)) {
					unset($attributes[$key]);
					continue;
				}

				if ($escape === true) {
					$value = htmlentities($value, ENT_COMPAT, Titon::app()->charset());
				}

				$parsed .= ' '. strtolower($key) .'="'. $value .'"';
			}
		}

		return $parsed;
	}

	/**
	 * Store the View object within the helper.
	 *
	 * @access public
	 * @param View $view
	 * @return void
	 */
	public function initialize(View $view) {
		$this->view = $view;
	}

	/**
	 * Triggered before a template is rendered by the engine.
	 *
	 * @access public
	 * @param View $view
	 * @return void
	 */
	public function preRender(View $view) {
		$this->view = $view;
	}

	/**
	 * Triggered after a template is rendered by the engine.
	 *
	 * @access public
	 * @param View $view
	 * @return void
	 */
	public function postRender(View $view) {
		$this->view = $view;
	}

	/**
	 * Generates an HTML tag if it exists.
	 *
	 * @access public
	 * @return string
	 */
	public function tag() {
		$args = func_get_args();
		$tagName = array_shift($args);
		$tag = $this->_tags[$tagName];

		return vsprintf($tag, $args) ."\n";
	}

}