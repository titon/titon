<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\helpers\xhtml;

use \titon\libs\helpers\html\AssetHelper as HtmlAssetHelper;

/**
 * The AssetHelper aids in the process of including external stylesheets and scripts.
 * 
 * @package	titon.libs.helpers.xhtml
 */
class AssetHelper extends HtmlAssetHelper {

	/**
	 * Attach the XhtmlHelper.
	 * 
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('html', function($self) {
			return Titon::registry()->factory('titon\libs\helpers\xhtml\XhtmlHelper');
		});
	}

}