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
use titon\utility\Inflector;
use titon\utility\Hash;

/**
 * The Formhelper is used for HTML form creation. Data is passed to the associated input fields
 * if a value is present with the Request object ($_POST, $_GET and $_FILES).
 *
 * @package	titon.libs.helpers.html
 */
class FormHelper extends HelperAbstract {

	/**
	 * Forms generated for the current request.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_forms = [];

	/**
	 * Fields that have failed validatio.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_invalid = [];

	/**
	 * The model currently being used to generate a form.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_model = 'Form';

	/**
	 * A list of all HTML tags used within the current helper.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tags = [
		'input'			=> '<input%s>',
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

	/**
	 * Parses an array of attributes to the HTML equivalent.
	 *
	 * @access public
	 * @param array $attributes
	 * @param array $remove
	 * @return string
	 */
	public function attributes(array $attributes, array $remove = []) {
		$remove = $remove + [
			'defaultDay', 'dayFormat', 'defaultHour', 'military', 'defaultMeridiem', 'defaultSecond',
			'defaultMinute', 'defaultMonth', 'monthFormat', 'options', 'default',
			'defaultYear', 'yearFormat', 'reverseYear', 'startYear', 'endYear'
		];

		return parent::attributes($attributes, $remove);
	}

	/**
	 * Create a single checkbox element.
	 *
	 * @access public
	 * @param string $input
	 * @param mixed $label
	 * @param array $attributes
	 * @return string
	 */
	public function checkbox($input, $label, array $attributes = []) {
		$value = isset($attributes['value']) ? $attributes['value'] : 1;
		unset($attributes['default']);

		$attributes = $this->_prepare(['name' => $input, 'type' => 'checkbox', 'value' => $value], $attributes);
		$selected = $this->_selected($attributes);

		if ($selected !== null) {
			if (is_array($selected) && in_array($value, $selected) || $value === $selected) {
				$attributes['checked'] = 'checked';
			}
		}

		// Prepare for multiple checkboxes
		if (isset($attributes['multiple']) && $attributes['multiple']) {
			$append = Inflector::camelCase($value);

			$input .= ' ' . $append;
			$attributes['id'] .= $append;
			$attributes['name'] .= '[]';

			unset($attributes['multiple']);
		}

		// Reset the value
		if (is_array($attributes['value']) || !$attributes['value']) {
			$attributes['value'] = $value;
		}

		$output = $this->tag('input', $this->attributes($attributes));

		if ($label) {
			$output .= $this->label($input, $label);
		}

		return $output;
	}

	/**
	 * Create multiple checkboxes.
	 *
	 * @access public
	 * @param string $input
	 * @param array $options
	 * @param array $attributes
	 *		default: The checkbox to be selected by default
	 * @return string
	 */
	public function checkboxes($input, array $options, array $attributes = []) {
		$checkboxes = [];

		foreach ($options as $value => $option) {
			$attributes['value'] = $value;
			$attributes['multiple'] = true;

			$checkboxes[] = $this->checkbox($input, $option, $attributes);
		}

		return $checkboxes;
	}

	/**
	 * Close a form by outputting the form close tag. If the submit button text or legend is present, append those elements.
	 *
	 * @access public
	 * @param mixed $submit
	 * @return string
	 */
	public function close($submit = false) {
		$output = '';

		if (!empty($submit)) {
			$output .= $this->submit($submit);
		}

		if (isset($this->_forms[$this->_model]['legend'])) {
			$output .= $this->tag('fieldset_close');
		}

		$output .= $this->tag('form_close');

		return $output;
	}

	/**
	 * Create a select dropdown for a calendar date: month, day, year.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 * @return string
	 */
	public function date($input, array $attributes = []) {
		$year = $this->year($input . '.year',
			['name' => $input . '.year'] + $attributes
		);

		$month = $this->month($input . '.month',
			['name' => $input . '.month'] + $attributes
		);

		$day = $this->day($input . '.day',
			['name' => $input . '.day'] + $attributes
		);

		return $month . ' ' . $day . ' ' . $year;
	}

	/**
	 * Combine both date() and time() to output all datetime related dropdowns.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 * @return string
	 */
	public function dateTime($input, array $attributes = []) {
		return $this->date($input, $attributes) . ' - ' . $this->time($input, $attributes);
	}

	/**
	 * Create a select dropdown for calendar days, with a range of 1-31.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 *		dayFormat: How the day should be formatted
	 *		defaultDay: The default selected day
	 * @return string
	 */
	public function day($input, array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input], $attributes);
		$format = isset($attributes['dayFormat']) ? $attributes['dayFormat'] : $this->config->dayFormat;
		$options = [];

		for ($i = 1; $i <= 31; ++$i) {
			$options[$i] = date($format, mktime(0, 0, 0, $this->config->month, $i, $this->config->year));
		}

		return $this->tag('select',
			$this->attributes($attributes, ['value']),
			$this->_options($options, $this->_selected($attributes, 'defaultDay', $this->config->day))
		);
	}

	/**
	 * Create a file upload and browse input field.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 * @return string
	 */
	public function file($input, array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input, 'type' => 'file'], $attributes);

		return $this->tag('input',
			$this->attributes($attributes)
		);
	}

	/**
	 * Create a hidden input field.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 * @return string
	 */
	public function hidden($input, array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input, 'type' => 'hidden'], $attributes);

		return $this->tag('input',
			$this->attributes($attributes)
		);
	}

	/**
	 * Create a select dropdown for hours, with a range of 0-23, or 1-12.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 *		military: Should the times be 0-23 or 1-12
	 *		defaultHour: The default selected hour
	 * @return string
	 */
	public function hour($input, array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input], $attributes);
		$selected = null;
		$options = [];

		if (isset($attributes['military']) && $attributes['military']) {
			$start = 0;
			$end = 23;

			if ($selected === null) {
				$selected = $this->config->hour24;
			}
		} else {
			$start = 1;
			$end = 12;

			if ($selected === null) {
				$selected = $this->config->hour;
			}
		}

		for ($i = $start; $i <= $end; ++$i) {
			$options[sprintf('%02d', $i)] = sprintf('%02d', $i);
		}

		return $this->tag('select',
			$this->attributes($attributes, ['value']),
			$this->_options($options, $this->_selected($attributes, 'defaultHour', $selected))
		);
	}

	/**
	 * Create a image submit input field.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 * @return string
	 */
	public function image($input, array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input, 'type' => 'image'], $attributes);

		return $this->tag('input',
			$this->attributes($attributes)
		);
	}

	/**
	 * Configure the class with the current date settings, instead of calling them multiple times.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('request', function() {
			return Titon::registry()->factory('titon\net\Request');
		});

		$this->config->set(array_diff_key([
			'day' => date('j'),
			'dayFormat' => 'j',
			'month' => date('n'),
			'monthFormat' => 'F',
			'year' => date('Y'),
			'yearFormat' => 'Y',
			'hour' => date('h'),
			'hour24' => date('H'),
			'minute' => date('i'),
			'second' => date('s'),
			'meridiem' => date('a')
		], $this->config->get()));
	}

	/**
	 * Create a label form for an input field.
	 *
	 * @access public
	 * @param string $input
	 * @param string $title
	 * @param array $attributes
	 * @return string
	 */
	public function label($input, $title, array $attributes = []) {
		$attributes = $attributes + [
			'for' => $this->_model . Inflector::camelCase($input),
		];

		return $this->tag('label',
			$this->attributes($attributes),
			$title
		);
	}

	/**
	 * Create a select dropdown for a time meridiem.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 *		defaultMeridiem: The default selected meridiem
	 * @return string
	 */
	public function meridiem($input, array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input], $attributes);
		$options = ['am' => 'AM', 'pm' => 'PM'];

		return $this->tag('select',
			$this->attributes($attributes, ['value']),
			$this->_options($options, $this->_selected($attributes, 'defaultMeridiem', $this->config->meridiem))
		);
	}

	/**
	 * Create a select dropdown for minutes, with a range of 1-60.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 *		defaultMinute: The default selected minute
	 * @return string
	 */
	public function minute($input, array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input], $attributes);
		$options = [];

		for ($i = 1; $i <= 60; ++$i) {
			$options[sprintf('%02d', $i)] = sprintf('%02d', $i);
		}

		return $this->tag('select',
			$this->attributes($attributes, ['value']),
			$this->_options($options, $this->_selected($attributes, 'defaultMinute', $this->config->minute))
		);
	}

	/**
	 * Create a select dropdown for calendar months, with a range of 1-12.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 *		monthFormat: Format the month names in the dropdown
	 *		defaultMonth: The default selected month
	 * @return string
	 */
	public function month($input, array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input], $attributes);
		$format = isset($attributes['monthFormat']) ? $attributes['monthFormat'] : $this->config->monthFormat;
		$options = [];

		for ($i = 1; $i <= 12; ++$i) {
			$options[$i] = date($format, mktime(0, 0, 0, $i, $this->config->day, $this->config->year));
		}

		return $this->tag('select',
			$this->attributes($attributes, ['value']),
			$this->_options($options, $this->_selected($attributes, 'defaultMonth', $this->config->month))
		);
	}

	/**
	 * Open a form by outputting the form open tag.
	 *
	 * @access public
	 * @param string $model
	 * @param array $attributes
	 * @return string
	 */
	public function open($model, array $attributes = []) {
		if (!empty($model) && is_string($model)) {
			$model = Inflector::modelize($model);
			$this->_model = $model;
		}

		// Form type
		if (isset($attributes['type'])) {
			if ($attributes['type'] === 'file') {
				$attributes['enctype'] = 'multipart/form-data';
			}
			unset($attributes['type']);
		}

		// Fieldset legend
		$legend = null;

		if (isset($attributes['legend'])) {
			$legend = $attributes['legend'];
			unset($attributes['legend']);
		}

		// Attributes
		$attributes = $attributes + [
			'method' => 'post',
			'action' => '',
			'id' => $this->_model . 'Form'
		];

		if (isset($attributes['action'])) {
			$attributes['action'] = Titon::router()->detect($attributes['action']);
		}

		$output = $this->tag('form_open', $this->attributes($attributes));

		// If legend, add fieldset
		if ($legend !== null) {
			$attributes['legend'] = $legend;

			$output .= $this->tag('fieldset_open');
			$output .= $this->tag('legend', $legend);
		}

		// Save its state
		$this->_forms[$this->_model] = $attributes;

		return $output;
	}

	/**
	 * Create a single checkbox element, or multiple checkboxes if an 'options' array is passed.
	 *
	 * @access public
	 * @param string $input
	 * @param array $options
	 * @param array $attributes
	 *		default: The radio to be selected by default
	 *		label: Enable or disable the labels
	 * @return string
	 */
	public function radio($input, array $options = [], array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input, 'type' => 'radio'], $attributes);
		$selected = $this->_selected($attributes);
		$showLabel = true;
		$radios = [];

		if (isset($attributes['label'])) {
			$showLabel = (bool) $attributes['label'];
		}

		foreach ($options as $value => $option) {
			$radio = $attributes;
			$radio['id'] = $radio['id'] . Inflector::camelCase($value);
			$radio['value'] = $value;

			if ($selected === $value) {
				$radio['checked'] = 'checked';
			}

			$output = $this->tag('input', $this->attributes($radio));

			if ($showLabel && $option !== '') {
				$output .= $this->label($input . ' ' . $value, $option);
			}

			$radios[] = $output;
		}

		return $radios;
	}

	/**
	 * Create a form reset button.
	 *
	 * @access public
	 * @param string $title
	 * @param array $attributes
	 * @return string
	 */
	public function reset($title, array $attributes = []) {
		$attributes = $attributes + [
			'id' => $this->_model . 'Reset',
			'type' => 'reset'
		];

		return $this->tag('button',
			$this->attributes($attributes),
			$title
		);
	}

	/**
	 * Create a select dropdown for seconds, with a range of 1-60.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 *		defaultSecond: The default selected second
	 * @return string
	 */
	public function second($input, array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input], $attributes);
		$options = [];

		for ($i = 1; $i <= 60; ++$i) {
			$options[sprintf('%02d', $i)] = sprintf('%02d', $i);
		}

		return $this->tag('select',
			$this->attributes($attributes, ['value']),
			$this->_options($options, $this->_selected($attributes, 'defaultSecond', $this->config->second))
		);
	}

	/**
	 * Create a select list with values based off an options array.
	 *
	 * @access public
	 * @param string $input
	 * @param array $options
	 * @param array $attributes
	 *		default: The option to be selected by default
	 *		empty: Display an empty option at the top of the list
	 * @return string
	 */
	public function select($input, array $options = [], array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input], $attributes);

		if (isset($attributes['empty'])) {
			$options = array_merge(['emptyOption' => $attributes['empty']], $options);
			unset($attributes['empty']);
		}

		return $this->tag('select',
			$this->attributes($attributes, ['value', 'empty']),
			$this->_options($options, $this->_selected($attributes))
		);
	}

	/**
	 * Create a form submit button.
	 *
	 * @access public
	 * @param string $title
	 * @param array $attributes
	 * @return string
	 */
	public function submit($title, array $attributes = []) {
		$attributes = $attributes + [
			'id' => $this->_model . 'Submit',
			'type' => 'submit'
		];

		return $this->tag('button',
			$this->attributes($attributes),
			$title
		);
	}

	/**
	 * Create a basic input text field.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 * @return string
	 */
	public function text($input, array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input, 'type' => 'text'], $attributes);

		return $this->tag('input',
			$this->attributes($attributes)
		);
	}

	/**
	 * Create a textarea field and determine the correct value content.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 * @return string
	 */
	public function textarea($input, array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input, 'cols' => 25, 'rows' => 5], $attributes);

		return $this->tag('textarea',
			$this->attributes($attributes, ['value']),
			$attributes['value']
		);
	}

	/**
	 * Create multiple select dropdowns for hours, minutes, seconds and the meridiem.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 * @return string
	 */
	public function time($input, array $attributes = []) {
		$hour = $this->hour($input . '.hour',
			['name' => $input . '.hour'] + $attributes
		);

		$minute = $this->minute($input . '.minute',
			['name' => $input . '.minute'] + $attributes
		);

		$second = $this->second($input . '.second',
			['name' => $input . '.second'] + $attributes
		);

		$meridiem = $this->meridiem($input . '.meridiem',
			['name' => $input . '.meridiem'] + $attributes
		);

		return $hour . ':' . $minute . ':' . $second . $meridiem;
	}

	/**
	 * Check to see if a value exists in the request data, if so return.
	 *
	 * @access public
	 * @param string $model
	 * @param string $field
	 * @return string
	 */
	public function value($model, $field) {
		return Hash::extract($this->request->data, $model . '.' . $field);
	}

	/**
	 * Create a select dropdown for calendar years, with a user defined range.
	 *
	 * @access public
	 * @param string $input
	 * @param array $attributes
	 *		startYear: The year to start the range
	 *		endYear: The year to end the range
	 *		reverseYear: Should the years be in reverse order
	 *		yearFormat: How the year should be formatted
	 *		defaultYear: The default selected year
	 * @return string
	 */
	public function year($input, array $attributes = []) {
		$attributes = $this->_prepare(['name' => $input], $attributes);
		$options = [];
		$config = $this->config->get();

		$reverse = isset($attributes['reverseYear']) ? $attributes['reverseYear'] : false;
		$format	= isset($attributes['yearFormat']) ? $attributes['yearFormat'] : $config['yearFormat'];
		$start = isset($attributes['startYear']) ? $attributes['startYear'] : $config['year'];
		$end = isset($attributes['endYear']) ? $attributes['endYear'] : ($config['year'] + 10);

		if (!$reverse) {
			for ($i = $start; $i <= $end; ++$i) {
				$options[$i] = date($format, mktime(0, 0, 0, $config['month'], $config['day'], $i));
			}
		} else {
			for ($i = $end; $i >= $start; --$i) {
				$options[$i] = date($format, mktime(0, 0, 0, $config['month'], $config['day'], $i));
			}
		}

		return $this->tag('select',
			$this->attributes($attributes, ['value']),
			$this->_options($options, $this->_selected($attributes, 'defaultYear', $config['year']))
		);
	}

	/**
	 * Create a list of options for a select dropdown. Can create an optgroup if a multi-dimensional array is used.
	 *
	 * @access protected
	 * @param array $options
	 * @param mixed $selected
	 * @return string
	 */
	protected function _options(array $options = [], $selected = null) {
		$output = '';

		if (!empty($options)) {
			foreach ($options as $value => $option) {
				if ($value === 'emptyValue') {
					$value = '';
				}

				// Optgroup
				if (is_array($option)) {
					$output .= $this->tag('optgroup',
					$this->attributes(['label' => $value]),
						$this->_options($option, $selected)
					);

				// Option
				} else {
					$attributes = ['value' => $value];

					if ($selected !== null) {
						if (is_array($selected) && in_array($value, $selected) || $value === $selected) {
							$attributes['selected'] = 'selected';
						}
					}

					$output .= $this->tag('option',
						$this->attributes($attributes),
						$option
					);
				}
			}
		}

		return $output;
	}

	/**
	 * Parse all the default and required attributes that are used within the input field.
	 *
	 * @access protected
	 * @param array $defaults
	 * @param array $attributes
	 * @return array
	 */
	protected function _prepare(array $defaults = [], array $attributes = []) {
		$attributes = $attributes + $defaults;
		$input = $attributes['name'];

		if ($this->_model !== 'Form') {
			$attributes['name'] = $this->_model . '.' . $attributes['name'];
		}

		$parts = explode('.', $attributes['name']);
		$name = array_shift($parts);
		$id = $this->_model;

		if (!empty($parts)) {
			foreach ($parts as $part) {
				$id .= Inflector::camelCase($part);
				$name .= '[' . $part . ']';
			}
		}

		foreach (['disabled', 'readonly', 'multiple'] as $attr) {
			if (isset($attributes[$attr])) {
				if ($attributes[$attr] === true || $attributes[$attr] === $attr) {
					$attributes[$attr] = $attr;
				} else {
					unset($attributes[$attr]);
				}
			}
		}

		$attributes = $attributes + ['id' => $id];
		$attributes['name'] = $name;
		$attributes['value'] = $this->value($this->_model, $input);

		return $attributes;
	}

	/**
	 * Return the currently selected value, or return a default value.
	 *
	 * @access protected
	 * @param array $attributes
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	protected function _selected(array $attributes, $key = 'default', $default = null) {
		$keys = ['value'];
		$selected = null;

		if ($key) {
			$keys[] = $key;
		}

		foreach ($keys as $key) {
			if (isset($attributes[$key])) {
				// Doesn't exist in post data
				if ($attributes[$key] === false || $attributes[$key] === null) {
					continue;

				// Does exist
				} else if ($attributes[$key]) {
					$selected = $attributes[$key];
					break;
				}
			}
		}

		if ($selected === null && $default !== null) {
			$selected = $default;
		}

		return $selected;
	}

}