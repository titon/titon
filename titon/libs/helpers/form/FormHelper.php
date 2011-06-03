<?php
/**
 * A Form helper used for form and data creation. Creates forms based around a Model of data,
 * that will be pre-populated according to the data available in the $_POST (App::$data).
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\libs\helpers\html;

use \titon\core\App;
use \titon\core\Router;
use \titon\libs\helpers\HelperAbstract;
use \titon\utility\Inflector;
use \titon\utility\Set;

/**
 * Form Helper
 *
 * @package		Titon
 * @subpackage	Titon.Modules.Helpers
 */
class FormHelper extends HelperAbstract {

    /**
     * Forms generated for the current request. Logs the form, fields and labels data.
     *
     * @access protected
     * @var array
     */
    protected $_forms = array();
    
    /**
     * Fields that have failed validation and are invalid, grouped by model.
     * The index is the input name and the value is the error message.
     * 
     * @access protected
     * @var array
     */
    protected $_invalid = array();

    /**
     * The model currently being used to generate a form.
     *
     * @access protected
     * @var string
     */
    protected $_model = 'Form';

    /**
     * A list of all HTML and XHTML tags used within the current helper.
     * If an element has multiple variations, it is represented with an array.
     *
     * @access protected
     * @var string
     */
    protected $_tags = array(
        'input'         => array('<input%s>', '<input%s />'),
        'textarea'      => '<textarea%s>%s</textarea>',
        'label'         => '<label%s>%s</label>',
        'select'        => '<select%s>%s</select>',
        'option'        => '<option%s>%s</option>',
        'optgroup'      => '<optgroup%s>%s</optgroup>',
        'button'        => '<button%s>%s</button>',
        'legend'        => '<legend>%s</legend>',
        'form_open'     => '<form%s>',
        'form_close'    => '</form>',
        'fieldset_open' => '<fieldset>',
        'fieldset_close'=> '</fieldset>'
    );

    /**
	 * Configure the class with the current date settings, instead of calling them multiple times.
	 *
	 * @access public
     * @param object $Engine
	 * @return void
	 */
    public function initialize($Engine) {
        parent::initialize($Engine);

        $this->configure(array(
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
        ));
    }

    /**
     * Create a single checkbox element, or multiple checkboxes if an 'options' array is passed.
     *
     * @access public
     * @param string $input
     * @param array $options
     * @param array $attributes
     *      - default: The checkbox to be selected by default
     *      - label: Enable or disable the label, or supply a new string to be used
     * @return string
     */
    public function checkbox($input, array $options = array(), array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input, 'type' => 'checkbox'), $attributes);
        $selected = null;
        $output = null;
        $label = true;

        // Value
        if (isset($attributes['value'])) {
            $selected = $attributes['value'];
        } else if (isset($attributes['default'])) {
            $selected = $attributes['default'];
        }

        // Show or hide label
        if (isset($attributes['label'])) {
            $label = $attributes['label'];
        } else {
            $label = Inflector::normalize($input);
        }

        // Create the checkboxes
        if (!is_array($options) || empty($options)) {
            $options = array(1 => $label);
        }

        foreach ($options as $value => $title) {
            $checkbox = $attributes;
            $checkbox['value'] = $value;

            if (count($options) > 1) {
                $append = Inflector::camelize($value);
                $checkbox['id'] = $checkbox['id'] . $append;
                $checkbox['name'] .= '[]';
                $labelInput = $input .'_'. $append;
            } else {
                $labelInput = $input;
            }

            if (!empty($selected)) {
                if ((is_array($selected) && in_array($value, $selected)) || ($value == $selected)) {
                    $checkbox['checked'] = 'checked';
                }
            }

            $output .= '<span class="form-checkbox">';
            $output .= $this->tag('input',
                $this->attributes($checkbox, array('label', 'options', 'default'))
            );

            if ($label && !empty($title)) {
                $output .= $this->label($labelInput, $title);
            }

            $output .= '</span>';
        }

        return $output;
    }

    /**
     * Close a form by outputting the form close tag. If the submit button text or legend is present, append those elements.
     *
     * @access public
     * @param string $submit
     * @return string
     */
    public function close($submit = false) {
        $output = null;

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
    public function date($input, array $attributes = array()) {
        $output  = $this->month($input .'.month',
            array_merge($attributes, array('name' => $input .'.month'))
        );
        
        $output .= $this->day($input .'.day',
            array_merge($attributes, array('name' => $input .'.day'))
        );

        $output .= $this->year($input .'.year',
            array_merge($attributes, array('name' => $input .'.year'))
        );

        return $output;
    }

    /**
     * Combine both date() and time() to output all datetime related dropdowns.
     *
     * @access public
     * @param string $input
     * @param array $attributes
     * @return string
     */
    public function dateTime($input, array $attributes = array()) {
        return $this->date($input, $attributes) .' - '. $this->time($input, $attributes);
    }

    /**
     * Create a select dropdown for calendar days, with a range of 1-31.
     *
     * @access public
     * @param string $input
     * @param array $attributes
     * @return string
     */
    public function day($input, array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input), $attributes);
        $selected = (isset($attributes['value']) ? $attributes['value'] : $this->_config['day']);
        $format = (isset($attributes['format']) ? $attributes['format'] : $this->_config['dayFormat']);
        $options = array();

        for ($i = 1; $i <= 31; ++$i) {
            $options[$i] = date($format, mktime(0, 0, 0, $this->_config['month'], $i, $this->_config['year']));
        }

        return $this->tag('select',
            $this->attributes($attributes, array('value', 'format')),
            $this->_options($options, $selected)
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
    public function file($input, array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input, 'type' => 'file'), $attributes);

        return $this->tag('input', $this->attributes($attributes));
    }

    /**
     * Create a hidden input field.
     *
     * @access public
     * @param string $input
     * @param array $attributes
     * @return string
     */
    public function hidden($input, array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input, 'type' => 'hidden'), $attributes);

        return $this->tag('input', $this->attributes($attributes));
    }

    /**
     * Create a select dropdown for hours, with a range of 0-23, or 1-12.
     *
     * @access public
     * @param string $input
     * @param array $attributes
     *      - military: Should the times be 0-23, instead of 1-12
     * @return string
     */
    public function hour($input, array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input), $attributes);
        $selected = (isset($attributes['value']) ? $attributes['value'] : null);
        $options = array();

        if (isset($attributes['military'])) {
            $start = 0;
            $end = 23;

            if ($selected == null) {
                $selected = $this->_config['hour24'];
            }
        } else {
            $start = 1;
            $end = 12;

            if ($selected == null) {
                $selected = $this->_config['hour'];
            }
        }

        for ($i = $start; $i <= $end; ++$i) {
            $options[sprintf('%02d', $i)] = sprintf('%02d', $i);
        }

        return $this->tag('select',
            $this->attributes($attributes, array('value', 'type', 'military')),
            $this->_options($options, $selected)
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
    public function image($input, array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input, 'type' => 'image'), $attributes);

        return $this->tag('input', $this->attributes($attributes));
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
    public function label($input, $title, array $attributes = array()) {
        $attributes = array_merge(array(
            'for'   => $this->_model . Inflector::camelize($input),
            'title' => $title
        ), $attributes);

        return $this->tag('label',
            $this->attributes($attributes),
            $title
        );
    }

    /**
     * Create a select dropdown for a time meridian.
     *
     * @access public
     * @param string $input
     * @param array $attributes
     * @return string
     */
    public function meridiem($input, array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input), $attributes);
        $selected = (isset($attributes['value']) ? $attributes['value'] : $this->_config['meridiem']);

        return $this->tag('select',
            $this->attributes($attributes, array('value', 'type')),
            $this->_options(array('am' => 'AM', 'pm' => 'PM'), $selected)
        );
    }

    /**
     * Create a select dropdown for minutes, with a range of 1-60.
     *
     * @access public
     * @param string $input
     * @param array $attributes
     * @return string
     */
    public function minute($input, array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input), $attributes);
        $selected = (isset($attributes['value']) ? $attributes['value'] : $this->_config['minute']);
        $options = array();

        for ($i = 1; $i <= 60; ++$i) {
            $options[sprintf('%02d', $i)] = sprintf('%02d', $i);
        }

        return $this->tag('select',
            $this->attributes($attributes, array('value', 'type')),
            $this->_options($options, $selected)
        );
    }

    /**
     * Create a select dropdown for calendar months, with a range of 1-12.
     *
     * @access public
     * @param string $input
     * @param array $attributes
     *      - format: Format the month names in the dropdown
     * @return string
     */
    public function month($input, array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input), $attributes);
        $selected = (isset($attributes['value']) ? $attributes['value'] : $this->_config['month']);
        $format = (isset($attributes['format']) ? $attributes['format'] : $this->_config['monthFormat']);
        $options = array();
        
        for ($i = 1; $i <= 12; ++$i) {
            $options[$i] = date($format, mktime(0, 0, 0, $i, $this->_config['day'], $this->_config['year']));
        }

        return $this->tag('select',
            $this->attributes($attributes, array('value', 'type', 'format')),
            $this->_options($options, $selected)
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
    public function open($model, array $attributes = array()) {
        if (!empty($model) && is_string($model)) {
            $model = Inflector::modelize($model);
            $this->_model = $model;
        }

        // Form type
        if (isset($attributes['type'])) {
			if ($attributes['type'] == 'file') {
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
        $attributes = array_merge(array(
            'method'    => 'post',
            'action'    => '',
            'id'        => $this->_model .'Form'
        ), $attributes);

        if (!empty($attributes['action'])) {
            $attributes['action'] = Router::build(Router::detect($attributes['action']));
        }

        $output = $this->tag('form_open', $this->attributes($attributes));

        // If legend, add fieldset
        if (isset($legend)) {
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
     *      - default: The radio to be selected by default
     *      - label: Enable or disable the label, or supply a new string to be used (single radio)
     * @return string
     */
    public function radio($input, array $options = array(), array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input, 'type' => 'radio'), $attributes);
        $selected = null;
        $output = null;
        $label = true;

        // Value
        if (isset($attributes['value'])) {
            $selected = $attributes['value'];
        } else if (isset($attributes['default'])) {
            $selected = $attributes['default'];
        }

        // Show or hide label
        if (isset($attributes['label'])) {
            $label = $attributes['label'];
        } else {
            $label = Inflector::normalize($input);
        }

        // Multiple radios
        foreach ($options as $value => $title) {
            $radio = $attributes;
            $radio['id'] = $radio['id'] . Inflector::camelize($value);
            $radio['value'] = $value;

            if ($selected == $value) {
                $radio['checked'] = 'checked';
            }

            $output .= '<span class="form-radio">';
            $output .= $this->tag('input',
                $this->attributes($radio, array('label', 'options', 'default'))
            );

            if ($label && !empty($title)) {
                $output .= $this->label($input .'_'. $value, $title);
            }

            $output .= '</span>';
        }

        return $output;
    }

    /**
     * Create a form reset button.
     *
     * @access public
     * @param string $title
     * @param array $attributes
     * @return string
     */
    public function reset($title, array $attributes = array()) {
        $attributes = array_merge(array(
            'id'    => $this->_model .'Reset',
            'type'  => 'reset'
        ), $attributes);

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
     * @return string
     */
    public function second($input, array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input), $attributes);
        $selected = (isset($attributes['value']) ? $attributes['value'] : $this->_config['second']);
        $options = array();

        for ($i = 1; $i <= 60; ++$i) {
            $options[sprintf('%02d', $i)] = sprintf('%02d', $i);
        }

        return $this->tag('select',
            $this->attributes($attributes, array('value', 'type')),
            $this->_options($options, $selected)
        );
    }

    /**
     * Create a select list with values based off an options array.
     *
     * @access public
     * @param string $input
     * @param array $options
     * @param array $attributes
     *      - default: The option to be selected by default
     *      - empty: Display an empty option at the top of the list
     * @return string
     */
    public function select($input, array $options = array(), array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input), $attributes);
        $selected = null;

        if (isset($attributes['value'])) {
            $selected = $attributes['value'];
        } else if (isset($attributes['default'])) {
            $selected = $attributes['default'];
        }

        if (isset($attributes['empty'])) {
            $options = array_merge(array('emptyOption' => $attributes['empty']), $options);
        }

        return $this->tag('select',
            $this->attributes($attributes, array('value', 'default', 'empty')),
            $this->_options($options, $selected)
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
    public function submit($title, array $attributes = array()) {
        $attributes = array_merge(array(
            'id'    => $this->_model .'Submit',
            'type'  => 'submit'
        ), $attributes);

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
    public function text($input, array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input, 'type' => 'text'), $attributes);

        return $this->tag('input', $this->attributes($attributes));
    }

    /**
     * Create a textarea field and determine the correct value content.
     *
     * @access public
     * @param string $input
     * @param array $attributes
     * @return string
     */
    public function textarea($input, array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input, 'cols' => 25, 'rows' => 5), $attributes);
        $value = $attributes['value'];

        return $this->tag('textarea',
            $this->attributes($attributes, array('value', 'type')),
            $value
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
    public function time($input, array $attributes = array()) {
        $output  = $this->hour($input .'.hour',
            array_merge($attributes, array('name' => $input .'.hour'))
        ).' : ';

        $output .= $this->minute($input .'.minute',
            array_merge($attributes, array('name' => $input .'.minute'))
        ).' : ';

        $output .= $this->second($input .'.second',
            array_merge($attributes, array('name' => $input .'.second'))
        );

        $output .= $this->meridiem($input .'.meridiem',
            array_merge($attributes, array('name' => $input .'.meridiem'))
        );

        return $output;
    }

    /**
     * Check to see if a value exists in the POST/GET data, if so escape and return.
     *
     * @access public
     * @param string $model
     * @param string $field
     * @return string
     */
    public function value($model, $field) {
        $data =& App::$data;
        $value = Set::extract($data, $model .'.'. $field);

        if (!empty($value)) {
            return (is_array($value) ? $value : htmlentities($value, ENT_COMPAT, App::charset()));
        } else {
            $data = Set::insert($data, $model .'.'. $field, null);
            return null;
        }
    }

    /**
     * Create a select dropdown for calendar years, with a user defined range.
     *
     * @access public
     * @param string $input
     * @param array $attributes
     *      - start: The year to start the range
     *      - end: The year to end the range
     *      - reverse: Should the years be in reverse order
     *      - format: How the year should be formatted
     * @return string
     */
    public function year($input, array $attributes = array()) {
        $attributes = $this->_prepareInput(array('name' => $input), $attributes);
        $selected   = (isset($attributes['value']) ? $attributes['value'] : $this->_config['year']);
        $format     = (isset($attributes['format']) ? $attributes['format'] : $this->_config['yearFormat']);
        $reverse    = (isset($attributes['reverse']) ? true : false);
        $start  = (isset($attributes['start']) ? $attributes['start'] : $this->_config['year']);
        $end    = (isset($attributes['end']) ? $attributes['end'] : ($this->_config['year'] + 10));
        $options = array();

        if ($reverse == false) {
            for ($i = $start; $i <= $end; ++$i) {
                $options[$i] = date($format, mktime(0, 0, 0, $this->_config['month'], $this->_config['day'], $i));
            }
        } else {
            for ($i = $end; $i >= $start; --$i) {
                $options[$i] = date($format, mktime(0, 0, 0, $this->_config['month'], $this->_config['day'], $i));
            }
        }
        
        return $this->tag('select',
            $this->attributes($attributes, array('value', 'type', 'format', 'reverse', 'start', 'end')),
            $this->_options($options, $selected)
        );
    }

    /**
     * Create a list of options for a select dropdown. Can create an optgroup if a multi-dimensional array is used.
     *
     * @access protected
     * @param array $options
     * @param mixed $selected
     * @param bool $grouped
     * @return string
     */
    protected function _options(array $options = array(), $selected = null, $grouped = false) {
        $output = null;
        $empty = null;

        if (!empty($options)) {
            foreach ($options as $value => $option) {
                if ($value == 'emptyValue') {
                    $value = '';
                    if (!is_string($option)) {
                        $option = '';
                    }
                }

                // Optgroup
                if (is_array($option) && $grouped === false) {
                    $output .= $this->tag('optgroup',
                        $this->attributes(array('label' => $value)),
                        $this->_options($option, $selected, true)
                    );

                // Option
                } else {
                    $attributes = array('value' => $value);

                    if (!empty($selected)) {
                        if ((is_array($selected) && in_array($value, $selected)) || ($value == $selected)) {
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
    protected function _prepareInput(array $defaults = array(), array $attributes = array()) {
        $attributes = array_merge($defaults, $attributes);
        $input = $attributes['name'];

        if ($this->_model != 'Form') {
            $attributes['name'] = $this->_model .'.'. $attributes['name'];
        }

        $nameParts = explode('.', $attributes['name']);
        $name = array_shift($nameParts);

        if (!empty($nameParts)) {
            foreach ($nameParts as $part) {
                $name .= '['. $part .']';
            }
        }

        $attributes['name'] = $name;

        foreach (array('disabled', 'readonly', 'multiple') as $attr) {
            if (isset($attributes[$attr])) {
                if (($attributes[$attr] === true) || ($attributes[$attr] == $attr)) {
                    $attributes[$attr] = $attr;
                }
                unset($attributes[$attr]);
            }
        }

        $attributes = array_merge(array(
            'id'    => $this->_model . Inflector::camelize(str_replace('.', ' ', $input)),
            'value' => $this->value($this->_model, $input)
        ), $attributes);

        return $attributes;
    }
    
}