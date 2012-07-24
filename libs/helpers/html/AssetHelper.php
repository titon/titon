<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\helpers\html;

use titon\Titon;
use titon\libs\helpers\HelperAbstract;

/**
 * The AssetHelper aids in the process of including external stylesheets and scripts.
 *
 * @package	titon.libs.helpers.html
 */
class AssetHelper extends HelperAbstract {

	/**
	 * Default locations.
	 */
	const HEADER = 'header';
	const FOOTER = 'footer';

	/**
	 * A list of JavaScript files to include in the current page.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_scripts = [];

	/**
	 * A list of CSS stylesheets to include in the current page.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_stylesheets = [];

	/**
	 * Add a JavaScript file to the current page request.
	 *
	 * @access public
	 * @param string $script
	 * @param string $location
	 * @param int $order
	 * @param int $env
	 * @return titon\libs\helpers\html\AssetHelper
	 */
	public function addScript($script, $location = self::FOOTER, $order = null, $env = null) {
		if (mb_substr($script, -3) !== '.js') {
			$script .= '.js';
		}

		if (!isset($this->_scripts[$location])) {
			$this->_scripts[$location] = [];
		}

		if (!is_numeric($order)) {
			$order = count($this->_scripts[$location]);
		}

		while (isset($this->_scripts[$location][$order])) {
			$order++;
		}

		$this->_scripts[$location][$order] = [
			'path' => $script,
			'env' => $env
		];

		return $this;
	}

	/**
	 * Add a CSS stylesheet to the current page request.
	 *
	 * @access public
	 * @param string $sheet
	 * @param string $media
	 * @param int $order
	 * @param int $env
	 * @return titon\libs\helpers\html\AssetHelper
	 */
	public function addStylesheet($sheet, $media = 'screen', $order = null, $env = null) {
		if (mb_substr($sheet, -4) !== '.css') {
			$sheet .= '.css';
		}

		if (!is_numeric($order)) {
			$order = count($this->_stylesheets);
		}

		while (isset($this->_stylesheets[$order])) {
			$order++;
		}

		$this->_stylesheets[$order] = [
			'path' => $sheet,
			'media' => $media,
			'env' => $env
		];

		return $this;
	}

	/**
	 * Attach the HtmlHelper.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('html', function() {
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
	public function scripts($location = self::FOOTER) {
		$output = null;

		if (!empty($this->_scripts[$location])) {
			$env = Titon::env()->current('type');

			$scripts = $this->_scripts[$location];
			ksort($scripts);

			foreach ($scripts as $script) {
				if ($script['env'] === null || $script['env'] === $env) {
					$output .= $this->html->script($script['path']);
				}
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
			$env = Titon::env()->current('type');

			$stylesheets = $this->_stylesheets;
			ksort($stylesheets);

			foreach ($stylesheets as $sheet) {
				if ($sheet['env'] === null || $sheet['env'] === $env) {
					$output .= $this->html->link($sheet['path'], ['media' => $sheet['media']]);
				}
			}
		}

		return $output;
	}

}