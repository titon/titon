<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\helpers\html;

use \titon\libs\helpers\HelperAbstract;

/**
 * The AssetHelper aids in the process of including stylesheets and external scripts.
 * 
 * @package	titon.libs.helpers.html
 */
class AssetHelper extends HelperAbstract {

	/**
	 * Constants to define the location for JavaScript.
	 */
	const JS_HEADER = 1;
	const JS_FOOTER = 2;

	/**
	 * A list of JavaScript files to include in the current page. Can be placed at the top or bottom of the page.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_scripts = array();

	/**
	 * A list of CSS stylesheets to include in the current page.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_stylesheets = array();

	/**
	 * Add a JavaScript file to the current page request. Can declare what order the scripts appear in, as well as the location.
	 *
	 * @access public
	 * @param string $script
	 * @param string $location
	 * @param int $order
	 * @return void
	 */
	public function addScript($script, $location = self::JS_FOOTER, $order = null) {
		if (substr($script, -3) != '.js') {
			$script .= '.js';
		}

		if (!$order || !is_numeric($order) || isset($this->_scripts[$location][$order])) {
			$order = count($this->_scripts);
		}

		$this->_scripts[$location][$order] = $script;
	}

	/**
	 * Add a CSS stylesheet to the current page request.
	 *
	 * @access public
	 * @param string $sheet
	 * @param string $media
	 * @param int $order
	 * @return void
	 */
	public function addStylesheet($sheet, $media = 'screen', $order = null) {
		if (substr($sheet, -4) != '.css') {
			$sheet .= '.css';
		}

		if (!$order || !is_numeric($order) || isset($this->_stylesheets[$order])) {
			$order = count($this->_stylesheets);
		}

		$this->_stylesheets[$order] = array(
			'path' => $sheet,
			'media' => $media
		);
	}

	/**
	 * Attach the HtmlHelper.
	 * 
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('html', function($self) {
			return Titon::registry()->factory('titon\libs\helpers\html\HtmlHelper');
		});
	}

	/**
	 * Return all the attached scripts. Uses the HTML helper to build the HTML tags.
	 *
	 * @access public
	 * @param string $location
	 * @return string
	 */
	public function scripts($location = self::JS_HEADER) {
		$output = null;

		if (!empty($this->_scripts[$location])) {
			foreach ($this->_scripts[$location] as $script) {
				$output .= $this->html->script($script);
			}
		}

		return $output;
	}

	/**
	 * Return all the attached stylesheets. Uses the HTML helper to build the HTML tags.
	 *
	 * @access public
	 * @return string
	 */
	public function stylesheets() {
		$output = null;

		if (!empty($this->_stylesheets)) {
			foreach ($this->_stylesheets as $sheet) {
				$output .= $this->html->link(array(
					'rel'   => 'stylesheet',
					'type'  => 'text/css',
					'media' => $sheet['media'],
					'href'  => $sheet['path']
				));
			}
		}

		return $output;
	}

}