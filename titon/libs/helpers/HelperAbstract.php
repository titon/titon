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
use \titon\libs\engines\Engine;
use \titon\libs\helpers\Helper;

/**
 * The Helper class acts as the base for all children helpers to extend.
 * Defines methods and properties for HTML tags and attribute generation.
 *
 * @package	titon.libs.helpers
 * @uses	titon\Titon
 * @abstract
 */
abstract class HelperAbstract extends Prototype implements Helper {

	/**
	 * Mapping of HTML tags.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tags = array();

	/**
	 * Engine object.
	 * 
	 * @access protected
	 * @var Engine
	 */
	protected $_engine;

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

		if (isset($attributes['escape'])) {
			$escape = (bool) $attributes['escape'];
			unset($attributes['escape']);
		}

		if (!empty($attributes)) {
			foreach ($attributes as $key => $value) {
				if (in_array($key, $remove)) {
					unset($attributes[$key]);
					continue;
				}

				if ($escape) {
					$value = htmlentities($value, ENT_COMPAT, Titon::config()->charset());
				}

				$parsed .= ' '. strtolower($key) .'="'. $value .'"';
			}
		}

		return $parsed;
	}

	/**
	 * Triggered before a template is rendered by the engine.
	 *
	 * @access public
	 * @param Engine $engine
	 * @return void
	 */
	public function preRender(Engine $engine) {
		$this->_engine = $engine;
	}

	/**
	 * Triggered after a template is rendered by the engine.
	 *
	 * @access public
	 * @param Engine $engine
	 * @return void
	 */
	public function postRender(Engine $engine) {
		$this->_engine = $engine;
	}

	/**
	 * Generates an HTML tag if it exists.
	 *
	 * @access public
	 * @return string
	 */
	public function tag() {
		$args = func_get_args();
		$tag = $this->_tags[array_shift($args)];

		return vsprintf($tag, $args) ."\n";
	}

}