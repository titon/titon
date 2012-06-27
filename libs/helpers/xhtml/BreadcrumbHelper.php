<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\helpers\xhtml;

use titon\Titon;
use titon\libs\helpers\html\BreadcrumbHelper as HtmlBreadcrumbHelper;

/**
 * The BreadcrumbHelper is primarily used for adding and generating breadcrumb lists.
 *
 * @package	titon.libs.helpers.xhtml
 */
class BreadcrumbHelper extends HtmlBreadcrumbHelper {

	/**
	 * Attach the XhtmlHelper.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('html', function() {
			return Titon::registry()->factory('titon\libs\helpers\xhtml\XhtmlHelper');
		});
	}

}