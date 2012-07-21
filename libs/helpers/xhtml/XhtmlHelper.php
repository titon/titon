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
use titon\libs\helpers\html\HtmlHelper;
use titon\utility\String;

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
		'anchor'	=> '<a{attr}>{body}</a>',
		'link'		=> '<link{attr} />',
		'meta'		=> '<meta{attr} />',
		'script'	=> '<script{attr}>{body}</script>',
		'style'		=> '<style{attr}>{body}</style>',
		'image'		=> '<img{attr} />'
	];

	/**
	 * Return the XHTML doctype.
	 *
	 * @access public
	 * @param string $type
	 * @return string
	 */
	public function doctype($type = 'default') {
		$type = mb_strtolower($type);

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

	/**
	 * Escape a value.
	 *
	 * @access public
	 * @param string $value
	 * @param boolean|null $escape
	 * @return string
	 */
	public function escape($value, $escape = null) {
		if ($escape === null) {
			$escape = $this->config->escape;
		}

		if ($escape) {
			$value = String::escape($value, ENT_QUOTES | ENT_XHTML);
		}

		return $value;
	}

}