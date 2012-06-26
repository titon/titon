<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\helpers\xhtml;

use titon\libs\helpers\html\FormHelper as HtmlFormHelper;

/**
 * The Formhelper is used for HTML form creation. Data is passed to the associated input fields
 * if a value is present with the Request object ($_POST, $_GET and $_FILES).
 *
 * @package	titon.libs.helpers.xhtml
 */
class FormHelper extends HtmlFormHelper {

	/**
	 * A list of all XHTML tags used within the current helper.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tags = [
		'input'			=> '<input%s />',
		'textarea'		=> '<textarea%s>%s</textarea>',
		'label'			=> '<label%s>%s</label>',
		'select'		=> '<select%s>%s</select>',
		'option'		=> '<option%s>%s</option>',
		'optgroup'		=> '<optgroup%s>%s</optgroup>',
		'button'		=> '<button%s>%s</button>',
		'legend'		=> '<legend>%s</legend>',
		'form_open'		=> '<form%s>',
		'form_close'	=> '</form>',
		'fieldset_open'	=> '<fieldset>',
		'fieldset_close'=> '</fieldset>'
	];

}