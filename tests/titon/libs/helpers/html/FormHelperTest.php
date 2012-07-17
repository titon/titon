<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\helpers\html;

use titon\tests\TestCase;
use titon\libs\helpers\html\FormHelper;

/**
 * Test class for titon\libs\helpers\html\FormHelper.
 */
class FormHelperTest extends TestCase {

	/**
	 * Setup live post data.
	 */
	protected function setUp() {
		$_POST['Test'] = [
			'checkbox' => 'no',
			'checkboxes' => 'red',
			'checkboxes_multi' => ['red', 'green'],
			'day' => 3,
			'hidden' => 'yes',
			'hour' => 4,
			'hour24' => 22,
			'meridiem' => 'am',
			'minute' => 19,
			'month' => 2
		];
	}

	/**
	 * Test that you can create a single checkbox using checkbox().
	 * Creating multiple checkboxes should be used for the most part.
	 */
	public function testCheckbox() {
		$helper = new FormHelper();
		$helper->open('Model');

		// regular
		$this->assertEquals(
			'<input id="model-checkbox" name="Model[checkbox]" type="checkbox" value="1">' . PHP_EOL .
			'<label for="model-checkbox">Title</label>' . PHP_EOL
		, $helper->checkbox('checkbox', 'Title'));

		// no label
		$this->assertEquals('<input id="model-checkbox" name="Model[checkbox]" type="checkbox" value="1">' . PHP_EOL, $helper->checkbox('checkbox', null));

		// tiered depth
		$this->assertEquals(
			'<input id="check-box" name="Model[check][box]" type="checkbox" value="1">' . PHP_EOL .
			'<label for="check-box">Title</label>' . PHP_EOL
		, $helper->checkbox('check.box', 'Title'));

		// attributes
		$this->assertEquals('<input class="class-name" id="custom-id" name="Model[checkbox]" type="checkbox" value="1">' . PHP_EOL, $helper->checkbox('checkbox', null, [
			'class' => 'class-name',
			'id' => 'custom-id'
		]));

		// value
		$this->assertEquals('<input id="model-checkbox" name="Model[checkbox]" type="checkbox" value="yes">' . PHP_EOL, $helper->checkbox('checkbox', null, [
			'value' => 'yes'
		]));

		// value with data
		$helper = new FormHelper();
		$helper->open('Test');

		$this->assertEquals('<input id="test-checkbox" name="Test[checkbox]" type="checkbox" value="no">' . PHP_EOL, $helper->checkbox('checkbox', null, [
			'value' => 'yes'
		]));
	}

	/**
	 * Test that creating multiple checkboxes work with checkboxes().
	 */
	public function testCheckboxes() {
		$helper = new FormHelper();
		$helper->open('Model');

		$options = array('red' => 'Red', 'blue' => 'Blue', 'green' => 'Green');

		// regular
		$this->assertEquals([
			'<input id="model-checkboxes-red" name="Model[checkboxes][]" type="checkbox" value="red">' . PHP_EOL .
			'<label for="model-checkboxes-red">Red</label>' . PHP_EOL
			,
			'<input id="model-checkboxes-blue" name="Model[checkboxes][]" type="checkbox" value="blue">' . PHP_EOL .
			'<label for="model-checkboxes-blue">Blue</label>' . PHP_EOL
			,
			'<input id="model-checkboxes-green" name="Model[checkboxes][]" type="checkbox" value="green">' . PHP_EOL .
			'<label for="model-checkboxes-green">Green</label>' . PHP_EOL
		], $helper->checkboxes('checkboxes', $options));

		// default
		$this->assertEquals([
			'<input id="model-checkboxes-red" name="Model[checkboxes][]" type="checkbox" value="red">' . PHP_EOL .
			'<label for="model-checkboxes-red">Red</label>' . PHP_EOL
			,
			'<input checked="checked" id="model-checkboxes-blue" name="Model[checkboxes][]" type="checkbox" value="blue">' . PHP_EOL .
			'<label for="model-checkboxes-blue">Blue</label>' . PHP_EOL
			,
			'<input id="model-checkboxes-green" name="Model[checkboxes][]" type="checkbox" value="green">' . PHP_EOL .
			'<label for="model-checkboxes-green">Green</label>' . PHP_EOL
		], $helper->checkboxes('checkboxes', $options, ['default' => 'blue']));

		// default with data
		$helper = new FormHelper();
		$helper->open('Test');

		$this->assertEquals([
			'<input checked="checked" id="test-checkboxes-red" name="Test[checkboxes][]" type="checkbox" value="red">' . PHP_EOL .
			'<label for="test-checkboxes-red">Red</label>' . PHP_EOL
			,
			'<input id="test-checkboxes-blue" name="Test[checkboxes][]" type="checkbox" value="blue">' . PHP_EOL .
			'<label for="test-checkboxes-blue">Blue</label>' . PHP_EOL
			,
			'<input id="test-checkboxes-green" name="Test[checkboxes][]" type="checkbox" value="green">' . PHP_EOL .
			'<label for="test-checkboxes-green">Green</label>' . PHP_EOL
		], $helper->checkboxes('checkboxes', $options, ['default' => 'blue']));

		// default with multiple data
		$helper = new FormHelper();
		$helper->open('Test');

		$this->assertEquals([
			'<input checked="checked" id="test-checkboxesmulti-red" name="Test[checkboxes_multi][]" type="checkbox" value="red">' . PHP_EOL .
			'<label for="test-checkboxesmulti-red">Red</label>' . PHP_EOL
			,
			'<input id="test-checkboxesmulti-blue" name="Test[checkboxes_multi][]" type="checkbox" value="blue">' . PHP_EOL .
			'<label for="test-checkboxesmulti-blue">Blue</label>' . PHP_EOL
			,
			'<input checked="checked" id="test-checkboxesmulti-green" name="Test[checkboxes_multi][]" type="checkbox" value="green">' . PHP_EOL .
			'<label for="test-checkboxesmulti-green">Green</label>' . PHP_EOL
		], $helper->checkboxes('checkboxes_multi', $options, ['default' => 'blue']));
	}

	public function testClose() {
		$helper = new FormHelper();
		$helper->open('Model');

		$this->assertEquals('</form>' . PHP_EOL, $helper->close());
		$this->assertEquals(
			'<button id="model-submit" type="submit">Submit</button>' . PHP_EOL .
			'</form>' . PHP_EOL
		, $helper->close('Submit'));

		// legend
		$helper = new FormHelper();
		$helper->open('Model', ['legend' => 'Legend']);

		$this->assertEquals('</fieldset>' . PHP_EOL . '</form>' . PHP_EOL, $helper->close());
		$this->assertEquals(
			'<button id="model-submit" type="submit">Submit</button>' . PHP_EOL .
			'</fieldset>' . PHP_EOL .
			'</form>' . PHP_EOL
		, $helper->close('Submit'));
	}

	/**
	 * Test that you can create a select dropdown of days with day().
	 */
	public function testDay() {
		$helper = new FormHelper();
		$helper->open('Model');

		$this->assertEquals(
			'<select id="model-day" name="Model[day]">' . PHP_EOL .
			'<option value="1">1</option>' . PHP_EOL .
			'<option value="2">2</option>' . PHP_EOL .
			'<option value="3">3</option>' . PHP_EOL .
			'<option value="4">4</option>' . PHP_EOL .
			'<option value="5">5</option>' . PHP_EOL .
			'<option value="6">6</option>' . PHP_EOL .
			'<option value="7">7</option>' . PHP_EOL .
			'<option value="8">8</option>' . PHP_EOL .
			'<option value="9">9</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'<option value="13">13</option>' . PHP_EOL .
			'<option value="14">14</option>' . PHP_EOL .
			'<option value="15">15</option>' . PHP_EOL .
			'<option value="16">16</option>' . PHP_EOL .
			'<option value="17">17</option>' . PHP_EOL .
			'<option value="18">18</option>' . PHP_EOL .
			'<option value="19">19</option>' . PHP_EOL .
			'<option value="20">20</option>' . PHP_EOL .
			'<option value="21">21</option>' . PHP_EOL .
			'<option value="22">22</option>' . PHP_EOL .
			'<option value="23">23</option>' . PHP_EOL .
			'<option value="24">24</option>' . PHP_EOL .
			'<option value="25">25</option>' . PHP_EOL .
			'<option value="26">26</option>' . PHP_EOL .
			'<option value="27">27</option>' . PHP_EOL .
			'<option value="28">28</option>' . PHP_EOL .
			'<option value="29">29</option>' . PHP_EOL .
			'<option value="30">30</option>' . PHP_EOL .
			'<option value="31">31</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->day('day', ['defaultDay' => '']));

		// format
		$this->assertEquals(
			'<select id="model-day" name="Model[day]">' . PHP_EOL .
			'<option value="1">01</option>' . PHP_EOL .
			'<option value="2">02</option>' . PHP_EOL .
			'<option value="3">03</option>' . PHP_EOL .
			'<option value="4">04</option>' . PHP_EOL .
			'<option value="5">05</option>' . PHP_EOL .
			'<option value="6">06</option>' . PHP_EOL .
			'<option value="7">07</option>' . PHP_EOL .
			'<option value="8">08</option>' . PHP_EOL .
			'<option value="9">09</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'<option value="13">13</option>' . PHP_EOL .
			'<option value="14">14</option>' . PHP_EOL .
			'<option value="15">15</option>' . PHP_EOL .
			'<option value="16">16</option>' . PHP_EOL .
			'<option value="17">17</option>' . PHP_EOL .
			'<option value="18">18</option>' . PHP_EOL .
			'<option value="19">19</option>' . PHP_EOL .
			'<option value="20">20</option>' . PHP_EOL .
			'<option value="21">21</option>' . PHP_EOL .
			'<option value="22">22</option>' . PHP_EOL .
			'<option value="23">23</option>' . PHP_EOL .
			'<option value="24">24</option>' . PHP_EOL .
			'<option value="25">25</option>' . PHP_EOL .
			'<option value="26">26</option>' . PHP_EOL .
			'<option value="27">27</option>' . PHP_EOL .
			'<option value="28">28</option>' . PHP_EOL .
			'<option value="29">29</option>' . PHP_EOL .
			'<option value="30">30</option>' . PHP_EOL .
			'<option value="31">31</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->day('day', ['defaultDay' => '', 'dayFormat' => 'd']));

		// default
		$this->assertEquals(
			'<select id="model-day" name="Model[day]">' . PHP_EOL .
			'<option value="1">1</option>' . PHP_EOL .
			'<option value="2">2</option>' . PHP_EOL .
			'<option value="3">3</option>' . PHP_EOL .
			'<option value="4">4</option>' . PHP_EOL .
			'<option value="5">5</option>' . PHP_EOL .
			'<option value="6">6</option>' . PHP_EOL .
			'<option value="7">7</option>' . PHP_EOL .
			'<option value="8">8</option>' . PHP_EOL .
			'<option value="9">9</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'<option selected="selected" value="13">13</option>' . PHP_EOL .
			'<option value="14">14</option>' . PHP_EOL .
			'<option value="15">15</option>' . PHP_EOL .
			'<option value="16">16</option>' . PHP_EOL .
			'<option value="17">17</option>' . PHP_EOL .
			'<option value="18">18</option>' . PHP_EOL .
			'<option value="19">19</option>' . PHP_EOL .
			'<option value="20">20</option>' . PHP_EOL .
			'<option value="21">21</option>' . PHP_EOL .
			'<option value="22">22</option>' . PHP_EOL .
			'<option value="23">23</option>' . PHP_EOL .
			'<option value="24">24</option>' . PHP_EOL .
			'<option value="25">25</option>' . PHP_EOL .
			'<option value="26">26</option>' . PHP_EOL .
			'<option value="27">27</option>' . PHP_EOL .
			'<option value="28">28</option>' . PHP_EOL .
			'<option value="29">29</option>' . PHP_EOL .
			'<option value="30">30</option>' . PHP_EOL .
			'<option value="31">31</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->day('day', ['defaultDay' => 13]));

		// with data
		$helper = new FormHelper();
		$helper->open('Test');

		$this->assertEquals(
			'<select id="test-day" name="Test[day]">' . PHP_EOL .
			'<option value="1">1</option>' . PHP_EOL .
			'<option value="2">2</option>' . PHP_EOL .
			'<option selected="selected" value="3">3</option>' . PHP_EOL .
			'<option value="4">4</option>' . PHP_EOL .
			'<option value="5">5</option>' . PHP_EOL .
			'<option value="6">6</option>' . PHP_EOL .
			'<option value="7">7</option>' . PHP_EOL .
			'<option value="8">8</option>' . PHP_EOL .
			'<option value="9">9</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'<option value="13">13</option>' . PHP_EOL .
			'<option value="14">14</option>' . PHP_EOL .
			'<option value="15">15</option>' . PHP_EOL .
			'<option value="16">16</option>' . PHP_EOL .
			'<option value="17">17</option>' . PHP_EOL .
			'<option value="18">18</option>' . PHP_EOL .
			'<option value="19">19</option>' . PHP_EOL .
			'<option value="20">20</option>' . PHP_EOL .
			'<option value="21">21</option>' . PHP_EOL .
			'<option value="22">22</option>' . PHP_EOL .
			'<option value="23">23</option>' . PHP_EOL .
			'<option value="24">24</option>' . PHP_EOL .
			'<option value="25">25</option>' . PHP_EOL .
			'<option value="26">26</option>' . PHP_EOL .
			'<option value="27">27</option>' . PHP_EOL .
			'<option value="28">28</option>' . PHP_EOL .
			'<option value="29">29</option>' . PHP_EOL .
			'<option value="30">30</option>' . PHP_EOL .
			'<option value="31">31</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->day('day'));
	}

	/**
	 * Test that you can create file inputs with file().
	 */
	public function testFile() {
		$helper = new FormHelper();
		$helper->open('Model');

		$this->assertEquals('<input id="model-file" name="Model[file]" type="file">' . PHP_EOL, $helper->file('file'));
	}

	/**
	 * Test that you can create hidden inputs with hidden().
	 */
	public function testHidden() {
		$helper = new FormHelper();
		$helper->open('Model');

		$this->assertEquals('<input id="model-hidden" name="Model[hidden]" type="hidden" value="">' . PHP_EOL, $helper->hidden('hidden'));
		$this->assertEquals('<input id="model-hidden" name="Model[hidden]" type="hidden" value="foobar">' . PHP_EOL, $helper->hidden('hidden', ['value' => 'foobar']));

		$helper = new FormHelper();
		$helper->open('Test');

		$this->assertEquals('<input id="test-hidden" name="Test[hidden]" type="hidden" value="yes">' . PHP_EOL, $helper->hidden('hidden'));
	}

	/**
	 * Test that you can create a select dropdown of hours with hour().
	 */
	public function testHour() {
		$helper = new FormHelper();
		$helper->open('Model');

		$this->assertEquals(
			'<select id="model-hour" name="Model[hour]">' . PHP_EOL .
			'<option value="01">01</option>' . PHP_EOL .
			'<option value="02">02</option>' . PHP_EOL .
			'<option value="03">03</option>' . PHP_EOL .
			'<option value="04">04</option>' . PHP_EOL .
			'<option value="05">05</option>' . PHP_EOL .
			'<option value="06">06</option>' . PHP_EOL .
			'<option value="07">07</option>' . PHP_EOL .
			'<option value="08">08</option>' . PHP_EOL .
			'<option value="09">09</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->hour('hour', ['defaultHour' => '']));

		$this->assertEquals(
			'<select id="model-hour" name="Model[hour]" onchange="update(this);">' . PHP_EOL .
			'<option value="01">01</option>' . PHP_EOL .
			'<option value="02">02</option>' . PHP_EOL .
			'<option value="03">03</option>' . PHP_EOL .
			'<option value="04">04</option>' . PHP_EOL .
			'<option value="05">05</option>' . PHP_EOL .
			'<option value="06">06</option>' . PHP_EOL .
			'<option value="07">07</option>' . PHP_EOL .
			'<option selected="selected" value="08">08</option>' . PHP_EOL .
			'<option value="09">09</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->hour('hour', ['defaultHour' => 8, 'onchange' => 'update(this);']));

		// 24 hour
		$this->assertEquals(
			'<select id="model-hour" name="Model[hour]">' . PHP_EOL .
			'<option value="00">00</option>' . PHP_EOL .
			'<option value="01">01</option>' . PHP_EOL .
			'<option value="02">02</option>' . PHP_EOL .
			'<option value="03">03</option>' . PHP_EOL .
			'<option value="04">04</option>' . PHP_EOL .
			'<option value="05">05</option>' . PHP_EOL .
			'<option value="06">06</option>' . PHP_EOL .
			'<option value="07">07</option>' . PHP_EOL .
			'<option value="08">08</option>' . PHP_EOL .
			'<option value="09">09</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'<option value="13">13</option>' . PHP_EOL .
			'<option value="14">14</option>' . PHP_EOL .
			'<option value="15">15</option>' . PHP_EOL .
			'<option value="16">16</option>' . PHP_EOL .
			'<option value="17">17</option>' . PHP_EOL .
			'<option value="18">18</option>' . PHP_EOL .
			'<option value="19">19</option>' . PHP_EOL .
			'<option value="20">20</option>' . PHP_EOL .
			'<option value="21">21</option>' . PHP_EOL .
			'<option value="22">22</option>' . PHP_EOL .
			'<option value="23">23</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->hour('hour', ['defaultHour' => '', '24hour' => true]));

		$this->assertEquals(
			'<select id="model-hour" name="Model[hour]" onchange="update(this);">' . PHP_EOL .
			'<option value="00">00</option>' . PHP_EOL .
			'<option value="01">01</option>' . PHP_EOL .
			'<option value="02">02</option>' . PHP_EOL .
			'<option value="03">03</option>' . PHP_EOL .
			'<option value="04">04</option>' . PHP_EOL .
			'<option value="05">05</option>' . PHP_EOL .
			'<option value="06">06</option>' . PHP_EOL .
			'<option value="07">07</option>' . PHP_EOL .
			'<option value="08">08</option>' . PHP_EOL .
			'<option value="09">09</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'<option value="13">13</option>' . PHP_EOL .
			'<option value="14">14</option>' . PHP_EOL .
			'<option value="15">15</option>' . PHP_EOL .
			'<option value="16">16</option>' . PHP_EOL .
			'<option value="17">17</option>' . PHP_EOL .
			'<option selected="selected" value="18">18</option>' . PHP_EOL .
			'<option value="19">19</option>' . PHP_EOL .
			'<option value="20">20</option>' . PHP_EOL .
			'<option value="21">21</option>' . PHP_EOL .
			'<option value="22">22</option>' . PHP_EOL .
			'<option value="23">23</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->hour('hour', ['defaultHour' => 18, 'onchange' => 'update(this);', '24hour' => true]));

		// with data
		$helper = new FormHelper();
		$helper->open('Test');

		$this->assertEquals(
			'<select id="test-hour" name="Test[hour]">' . PHP_EOL .
			'<option value="01">01</option>' . PHP_EOL .
			'<option value="02">02</option>' . PHP_EOL .
			'<option value="03">03</option>' . PHP_EOL .
			'<option selected="selected" value="04">04</option>' . PHP_EOL .
			'<option value="05">05</option>' . PHP_EOL .
			'<option value="06">06</option>' . PHP_EOL .
			'<option value="07">07</option>' . PHP_EOL .
			'<option value="08">08</option>' . PHP_EOL .
			'<option value="09">09</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->hour('hour'));

		$this->assertEquals(
			'<select id="test-hour24" name="Test[hour24]">' . PHP_EOL .
			'<option value="00">00</option>' . PHP_EOL .
			'<option value="01">01</option>' . PHP_EOL .
			'<option value="02">02</option>' . PHP_EOL .
			'<option value="03">03</option>' . PHP_EOL .
			'<option value="04">04</option>' . PHP_EOL .
			'<option value="05">05</option>' . PHP_EOL .
			'<option value="06">06</option>' . PHP_EOL .
			'<option value="07">07</option>' . PHP_EOL .
			'<option value="08">08</option>' . PHP_EOL .
			'<option value="09">09</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'<option value="13">13</option>' . PHP_EOL .
			'<option value="14">14</option>' . PHP_EOL .
			'<option value="15">15</option>' . PHP_EOL .
			'<option value="16">16</option>' . PHP_EOL .
			'<option value="17">17</option>' . PHP_EOL .
			'<option value="18">18</option>' . PHP_EOL .
			'<option value="19">19</option>' . PHP_EOL .
			'<option value="20">20</option>' . PHP_EOL .
			'<option value="21">21</option>' . PHP_EOL .
			'<option selected="selected" value="22">22</option>' . PHP_EOL .
			'<option value="23">23</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->hour('hour24', ['24hour' => true]));
	}

	/**
	 * Test that you can create file inputs with image().
	 */
	public function testImage() {
		$helper = new FormHelper();
		$helper->open('Model');

		$this->assertEquals('<input id="model-image" name="Model[image]" src="image.png" type="image">' . PHP_EOL, $helper->image('image', ['src' => 'image.png']));
	}

	/**
	 * Test that you can create input labels with label.
	 */
	public function testLabel() {
		$helper = new FormHelper();
		$helper->open('Model');

		$this->assertEquals('<label for="model-label">Title</label>' . PHP_EOL, $helper->label('label', 'Title'));

		// escaping
		$this->assertEquals('<label for="model-label">Title &quot;with&quot; quotes</label>' . PHP_EOL, $helper->label('label', 'Title "with" quotes'));

		// escaping
		$this->assertEquals('<label class="class-name" for="label-nested-input">Title</label>' . PHP_EOL, $helper->label('label.nested.input', 'Title', ['class' => 'class-name']));

		// nested input
		$this->assertEquals('<label for="model-label"><input id="model-label" name="Model[label]" type="checkbox" value="1">' . PHP_EOL . '</label>' . PHP_EOL, $helper->label('label', $helper->checkbox('label', null), ['escape' => false]));
	}

	/**
	 * Test that you can create a select dropdown of am/pm with meridiem().
	 */
	public function testMeridiem() {
		$helper = new FormHelper();
		$helper->open('Model');

		$this->assertEquals(
			'<select id="model-meridiem" name="Model[meridiem]">' . PHP_EOL .
			'<option value="am">AM</option>' . PHP_EOL .
			'<option value="pm">PM</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->meridiem('meridiem', ['defaultMeridiem' => '']));

		$this->assertEquals(
			'<select id="model-meridiem" name="Model[meridiem]">' . PHP_EOL .
			'<option value="am">AM</option>' . PHP_EOL .
			'<option selected="selected" value="pm">PM</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->meridiem('meridiem', ['defaultMeridiem' => 'pm']));

		// with data
		$helper = new FormHelper();
		$helper->open('Test');

		$this->assertEquals(
			'<select id="test-meridiem" name="Test[meridiem]">' . PHP_EOL .
			'<option selected="selected" value="am">AM</option>' . PHP_EOL .
			'<option value="pm">PM</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->meridiem('meridiem'));
	}

	/**
	 * Test that you can create a select dropdown of minutes with minute().
	 */
	public function testMinute() {
		$helper = new FormHelper();
		$helper->open('Model');

		$this->assertEquals(
			'<select id="model-minute" name="Model[minute]">' . PHP_EOL .
			'<option value="01">01</option>' . PHP_EOL .
			'<option value="02">02</option>' . PHP_EOL .
			'<option value="03">03</option>' . PHP_EOL .
			'<option value="04">04</option>' . PHP_EOL .
			'<option value="05">05</option>' . PHP_EOL .
			'<option value="06">06</option>' . PHP_EOL .
			'<option value="07">07</option>' . PHP_EOL .
			'<option value="08">08</option>' . PHP_EOL .
			'<option value="09">09</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'<option value="13">13</option>' . PHP_EOL .
			'<option value="14">14</option>' . PHP_EOL .
			'<option value="15">15</option>' . PHP_EOL .
			'<option value="16">16</option>' . PHP_EOL .
			'<option value="17">17</option>' . PHP_EOL .
			'<option value="18">18</option>' . PHP_EOL .
			'<option value="19">19</option>' . PHP_EOL .
			'<option value="20">20</option>' . PHP_EOL .
			'<option value="21">21</option>' . PHP_EOL .
			'<option value="22">22</option>' . PHP_EOL .
			'<option value="23">23</option>' . PHP_EOL .
			'<option value="24">24</option>' . PHP_EOL .
			'<option value="25">25</option>' . PHP_EOL .
			'<option value="26">26</option>' . PHP_EOL .
			'<option value="27">27</option>' . PHP_EOL .
			'<option value="28">28</option>' . PHP_EOL .
			'<option value="29">29</option>' . PHP_EOL .
			'<option value="30">30</option>' . PHP_EOL .
			'<option value="31">31</option>' . PHP_EOL .
			'<option value="32">32</option>' . PHP_EOL .
			'<option value="33">33</option>' . PHP_EOL .
			'<option value="34">34</option>' . PHP_EOL .
			'<option value="35">35</option>' . PHP_EOL .
			'<option value="36">36</option>' . PHP_EOL .
			'<option value="37">37</option>' . PHP_EOL .
			'<option value="38">38</option>' . PHP_EOL .
			'<option value="39">39</option>' . PHP_EOL .
			'<option value="40">40</option>' . PHP_EOL .
			'<option value="41">41</option>' . PHP_EOL .
			'<option value="42">42</option>' . PHP_EOL .
			'<option value="43">43</option>' . PHP_EOL .
			'<option value="44">44</option>' . PHP_EOL .
			'<option value="45">45</option>' . PHP_EOL .
			'<option value="46">46</option>' . PHP_EOL .
			'<option value="47">47</option>' . PHP_EOL .
			'<option value="48">48</option>' . PHP_EOL .
			'<option value="49">49</option>' . PHP_EOL .
			'<option value="50">50</option>' . PHP_EOL .
			'<option value="51">51</option>' . PHP_EOL .
			'<option value="52">52</option>' . PHP_EOL .
			'<option value="53">53</option>' . PHP_EOL .
			'<option value="54">54</option>' . PHP_EOL .
			'<option value="55">55</option>' . PHP_EOL .
			'<option value="56">56</option>' . PHP_EOL .
			'<option value="57">57</option>' . PHP_EOL .
			'<option value="58">58</option>' . PHP_EOL .
			'<option value="59">59</option>' . PHP_EOL .
			'<option value="60">60</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->minute('minute', ['defaultMinute' => '']));

		// default
		$this->assertEquals(
			'<select id="model-minute" name="Model[minute]">' . PHP_EOL .
			'<option value="01">01</option>' . PHP_EOL .
			'<option value="02">02</option>' . PHP_EOL .
			'<option value="03">03</option>' . PHP_EOL .
			'<option value="04">04</option>' . PHP_EOL .
			'<option value="05">05</option>' . PHP_EOL .
			'<option value="06">06</option>' . PHP_EOL .
			'<option value="07">07</option>' . PHP_EOL .
			'<option value="08">08</option>' . PHP_EOL .
			'<option value="09">09</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'<option value="13">13</option>' . PHP_EOL .
			'<option value="14">14</option>' . PHP_EOL .
			'<option value="15">15</option>' . PHP_EOL .
			'<option value="16">16</option>' . PHP_EOL .
			'<option value="17">17</option>' . PHP_EOL .
			'<option value="18">18</option>' . PHP_EOL .
			'<option value="19">19</option>' . PHP_EOL .
			'<option value="20">20</option>' . PHP_EOL .
			'<option value="21">21</option>' . PHP_EOL .
			'<option value="22">22</option>' . PHP_EOL .
			'<option value="23">23</option>' . PHP_EOL .
			'<option value="24">24</option>' . PHP_EOL .
			'<option value="25">25</option>' . PHP_EOL .
			'<option value="26">26</option>' . PHP_EOL .
			'<option value="27">27</option>' . PHP_EOL .
			'<option value="28">28</option>' . PHP_EOL .
			'<option value="29">29</option>' . PHP_EOL .
			'<option value="30">30</option>' . PHP_EOL .
			'<option value="31">31</option>' . PHP_EOL .
			'<option value="32">32</option>' . PHP_EOL .
			'<option value="33">33</option>' . PHP_EOL .
			'<option value="34">34</option>' . PHP_EOL .
			'<option value="35">35</option>' . PHP_EOL .
			'<option value="36">36</option>' . PHP_EOL .
			'<option value="37">37</option>' . PHP_EOL .
			'<option selected="selected" value="38">38</option>' . PHP_EOL .
			'<option value="39">39</option>' . PHP_EOL .
			'<option value="40">40</option>' . PHP_EOL .
			'<option value="41">41</option>' . PHP_EOL .
			'<option value="42">42</option>' . PHP_EOL .
			'<option value="43">43</option>' . PHP_EOL .
			'<option value="44">44</option>' . PHP_EOL .
			'<option value="45">45</option>' . PHP_EOL .
			'<option value="46">46</option>' . PHP_EOL .
			'<option value="47">47</option>' . PHP_EOL .
			'<option value="48">48</option>' . PHP_EOL .
			'<option value="49">49</option>' . PHP_EOL .
			'<option value="50">50</option>' . PHP_EOL .
			'<option value="51">51</option>' . PHP_EOL .
			'<option value="52">52</option>' . PHP_EOL .
			'<option value="53">53</option>' . PHP_EOL .
			'<option value="54">54</option>' . PHP_EOL .
			'<option value="55">55</option>' . PHP_EOL .
			'<option value="56">56</option>' . PHP_EOL .
			'<option value="57">57</option>' . PHP_EOL .
			'<option value="58">58</option>' . PHP_EOL .
			'<option value="59">59</option>' . PHP_EOL .
			'<option value="60">60</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->minute('minute', ['defaultMinute' => 38]));

		// with data
		$helper = new FormHelper();
		$helper->open('Test');

		$this->assertEquals(
			'<select id="test-minute" name="Test[minute]">' . PHP_EOL .
			'<option value="01">01</option>' . PHP_EOL .
			'<option value="02">02</option>' . PHP_EOL .
			'<option value="03">03</option>' . PHP_EOL .
			'<option value="04">04</option>' . PHP_EOL .
			'<option value="05">05</option>' . PHP_EOL .
			'<option value="06">06</option>' . PHP_EOL .
			'<option value="07">07</option>' . PHP_EOL .
			'<option value="08">08</option>' . PHP_EOL .
			'<option value="09">09</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'<option value="13">13</option>' . PHP_EOL .
			'<option value="14">14</option>' . PHP_EOL .
			'<option value="15">15</option>' . PHP_EOL .
			'<option value="16">16</option>' . PHP_EOL .
			'<option value="17">17</option>' . PHP_EOL .
			'<option value="18">18</option>' . PHP_EOL .
			'<option selected="selected" value="19">19</option>' . PHP_EOL .
			'<option value="20">20</option>' . PHP_EOL .
			'<option value="21">21</option>' . PHP_EOL .
			'<option value="22">22</option>' . PHP_EOL .
			'<option value="23">23</option>' . PHP_EOL .
			'<option value="24">24</option>' . PHP_EOL .
			'<option value="25">25</option>' . PHP_EOL .
			'<option value="26">26</option>' . PHP_EOL .
			'<option value="27">27</option>' . PHP_EOL .
			'<option value="28">28</option>' . PHP_EOL .
			'<option value="29">29</option>' . PHP_EOL .
			'<option value="30">30</option>' . PHP_EOL .
			'<option value="31">31</option>' . PHP_EOL .
			'<option value="32">32</option>' . PHP_EOL .
			'<option value="33">33</option>' . PHP_EOL .
			'<option value="34">34</option>' . PHP_EOL .
			'<option value="35">35</option>' . PHP_EOL .
			'<option value="36">36</option>' . PHP_EOL .
			'<option value="37">37</option>' . PHP_EOL .
			'<option value="38">38</option>' . PHP_EOL .
			'<option value="39">39</option>' . PHP_EOL .
			'<option value="40">40</option>' . PHP_EOL .
			'<option value="41">41</option>' . PHP_EOL .
			'<option value="42">42</option>' . PHP_EOL .
			'<option value="43">43</option>' . PHP_EOL .
			'<option value="44">44</option>' . PHP_EOL .
			'<option value="45">45</option>' . PHP_EOL .
			'<option value="46">46</option>' . PHP_EOL .
			'<option value="47">47</option>' . PHP_EOL .
			'<option value="48">48</option>' . PHP_EOL .
			'<option value="49">49</option>' . PHP_EOL .
			'<option value="50">50</option>' . PHP_EOL .
			'<option value="51">51</option>' . PHP_EOL .
			'<option value="52">52</option>' . PHP_EOL .
			'<option value="53">53</option>' . PHP_EOL .
			'<option value="54">54</option>' . PHP_EOL .
			'<option value="55">55</option>' . PHP_EOL .
			'<option value="56">56</option>' . PHP_EOL .
			'<option value="57">57</option>' . PHP_EOL .
			'<option value="58">58</option>' . PHP_EOL .
			'<option value="59">59</option>' . PHP_EOL .
			'<option value="60">60</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->minute('minute'));
	}

	/**
	 * Test that you can create a select dropdown of months with month().
	 */
	public function testMonth() {
		$helper = new FormHelper();
		$helper->open('Model');

		$this->assertEquals(
			'<select id="model-month" name="Model[month]">' . PHP_EOL .
			'<option value="1">January</option>' . PHP_EOL .
			'<option value="2">February</option>' . PHP_EOL .
			'<option value="3">March</option>' . PHP_EOL .
			'<option value="4">April</option>' . PHP_EOL .
			'<option value="5">May</option>' . PHP_EOL .
			'<option value="6">June</option>' . PHP_EOL .
			'<option value="7">July</option>' . PHP_EOL .
			'<option value="8">August</option>' . PHP_EOL .
			'<option value="9">September</option>' . PHP_EOL .
			'<option value="10">October</option>' . PHP_EOL .
			'<option value="11">November</option>' . PHP_EOL .
			'<option value="12">December</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->month('month', ['defaultMonth' => '']));

		// format + default
		$this->assertEquals(
			'<select id="model-month" name="Model[month]">' . PHP_EOL .
			'<option value="1">Jan</option>' . PHP_EOL .
			'<option value="2">Feb</option>' . PHP_EOL .
			'<option value="3">Mar</option>' . PHP_EOL .
			'<option value="4">Apr</option>' . PHP_EOL .
			'<option value="5">May</option>' . PHP_EOL .
			'<option value="6">Jun</option>' . PHP_EOL .
			'<option value="7">Jul</option>' . PHP_EOL .
			'<option selected="selected" value="8">Aug</option>' . PHP_EOL .
			'<option value="9">Sep</option>' . PHP_EOL .
			'<option value="10">Oct</option>' . PHP_EOL .
			'<option value="11">Nov</option>' . PHP_EOL .
			'<option value="12">Dec</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->month('month', ['defaultMonth' => 8, 'monthFormat' => 'M']));

		// with data
		$helper = new FormHelper();
		$helper->open('Test');

		$this->assertEquals(
			'<select id="test-month" name="Test[month]">' . PHP_EOL .
			'<option value="1">01</option>' . PHP_EOL .
			'<option selected="selected" value="2">02</option>' . PHP_EOL .
			'<option value="3">03</option>' . PHP_EOL .
			'<option value="4">04</option>' . PHP_EOL .
			'<option value="5">05</option>' . PHP_EOL .
			'<option value="6">06</option>' . PHP_EOL .
			'<option value="7">07</option>' . PHP_EOL .
			'<option value="8">08</option>' . PHP_EOL .
			'<option value="9">09</option>' . PHP_EOL .
			'<option value="10">10</option>' . PHP_EOL .
			'<option value="11">11</option>' . PHP_EOL .
			'<option value="12">12</option>' . PHP_EOL .
			'</select>' . PHP_EOL
		, $helper->month('month', ['monthFormat' => 'm']));
	}

	/**
	 * Test that you can create opening form tags with open().
	 */
	public function testOpen() {
		$helper = new FormHelper();

		$this->assertEquals('<form action="" enctype="application/x-www-form-urlencoded" id="model-form" method="post">' . PHP_EOL, $helper->open('Model'));

		// file
		$this->assertEquals('<form action="" enctype="multipart/form-data" id="model-form" method="post">' . PHP_EOL, $helper->open('Model', ['type' => 'file']));

		// action
		$this->assertEquals('<form action="/pages/search" enctype="application/x-www-form-urlencoded" id="model-form" method="get">' . PHP_EOL, $helper->open('model', ['method' => 'get', 'action' => ['controller' => 'search']]));

		// no model
		$this->assertEquals('<form action="" enctype="application/x-www-form-urlencoded" id="form" method="post">' . PHP_EOL, $helper->open(null));

		// legend
		$this->assertEquals(
			'<form action="" enctype="application/x-www-form-urlencoded" id="model-form" method="post">' . PHP_EOL .
			'<fieldset>' . PHP_EOL .
			'<legend>Legend</legend>' . PHP_EOL
		, $helper->open('Model', ['legend' => 'Legend']));
	}

}