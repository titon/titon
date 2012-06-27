<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\helpers\xhtml;

use titon\libs\helpers\html\HtmlHelper;

/**
 * The HtmlHelper is primarily used for dynamic HTML tag creation within templates.
 *
 * @package	titon.libs.helpers.xhtml
 */
class XhtmlHelper extends HtmlHelper {

	/**
	 * Mapping of XHTML tags for this helper.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_tags = [
		'anchor'	=> '<a%s>%s</a>',
		'link'		=> '<link%s />',
		'meta'		=> '<meta%s />',
		'script'	=> '<script%s>%s</script>',
		'style'		=> '<style%s>%s</style>',
		'image'		=> '<img%s />'
	];

	/**
	 * Return the XHTML doctype.
	 *
	 * @access public
	 * @param string $type
	 * @return string
	 */
	public function docType($type = 'default') {
		$type = strtolower($type);

		if ($type === 'transitional') {
			$type = 'trans';
		}

		$docTypes = [
			'strict'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
			'trans'		=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
			'frameset'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
			'default'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'
		];

		if (!isset($docTypes[$type])) {
			$type = 'default';
		}

		return $docTypes[$type] . "\n";
	}

}