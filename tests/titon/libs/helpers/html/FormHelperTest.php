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
			'day' => 3
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
			'<input id="model-check-box" name="Model[check][box]" type="checkbox" value="1">' . PHP_EOL .
			'<label for="model-check-box">Title</label>' . PHP_EOL
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
	}

}