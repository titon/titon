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
 * The BreadcrumbHelper is primarily used for adding and generating breadcrumb lists.
 *
 * @package	titon.libs.helpers.html
 * @uses	titon\Titon
 */
class BreadcrumbHelper extends HelperAbstract {

	/**
	 * A list of all breadcrumbs in the trail, with the title, url and attributes.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_breadcrumbs = array();

	/**
	 * Add a link to the breadcrumbs.
	 *
	 * @access public
	 * @param string $title
	 * @param string|array $url
	 * @param array $attributes
	 * @return BreadcrumbHelper
	 */
	public function add($title, $url, array $attributes = array()) {
		$this->_breadcrumbs[] = array($title, $url, $attributes);

		return $this;
	}

	/**
	 * Return an array of breadcrumbs formatted as anchor links.
	 *
	 * @access public
	 * @return array
	 */
	public function generate() {
		$trail = array();

		if (!empty($this->_breadcrumbs)) {
			foreach ($this->_breadcrumbs as $crumb) {
				$trail[] = $this->html->anchor($crumb[0], $crumb[1], $crumb[2]);
			}
		}

		return $trail;
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

}