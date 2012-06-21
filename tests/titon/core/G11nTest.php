<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

include_once dirname(dirname(__DIR__)) . '/bootstrap.php';

use titon\core\G11n;
use titon\libs\translators\messages\MessageTranslator;

/**
 * Test class for titon\core\G11n.
 */
class G11nTest extends \PHPUnit_Framework_TestCase {

	protected $object;

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = titon\Titon::g11n();
		$this->object->setup('ex-va');
		$this->object->setup('ex-fm');
		$this->object->setup('ex-in');
		$this->object->setup('no'); // Needs 2 types of locales
		$this->object->fallbackAs('ex');
		$this->object->setTranslator(new MessageTranslator());
	}

	/**
	 * Test that formatting locale keys return the correct formats.
	 */
	public function testCanonicalize() {
		$this->assertEquals('en-us', $this->object->canonicalize('en-us', G11n::FORMAT_1));
		$this->assertEquals('en-US', $this->object->canonicalize('en-us', G11n::FORMAT_2));
		$this->assertEquals('en_US', $this->object->canonicalize('en-us', G11n::FORMAT_3));

		$this->assertEquals('en-us', $this->object->canonicalize('en-US', G11n::FORMAT_1));
		$this->assertEquals('en-US', $this->object->canonicalize('en-US', G11n::FORMAT_2));
		$this->assertEquals('en_US', $this->object->canonicalize('en-US', G11n::FORMAT_3));

		$this->assertEquals('en-us', $this->object->canonicalize('en_US', G11n::FORMAT_1));
		$this->assertEquals('en-US', $this->object->canonicalize('en_US', G11n::FORMAT_2));
		$this->assertEquals('en_US', $this->object->canonicalize('en_US', G11n::FORMAT_3));
	}

	/**
	 * Test that cascade returns a descending list of locale IDs.
	 */
	public function testCascade() {
		$httpAccepts = array(
			'ex-no,ex;q=0.5' => array('ex'),
			'ex-in,ex;q=0.5' => array('ex_IN', 'ex'),
			'ex-va,ex;q=0.5' => array('ex_VA', 'ex'),
			'ex-fm,ex;q=0.5' => array('ex_FM', 'ex'),
			'foobar' => array('ex') // Wont match and will use the fallback
		);

		foreach ($httpAccepts as $httpAccept => $localeId) {
			$_SERVER['HTTP_ACCEPT_LANGUAGE'] = $httpAccept;
			$this->object->initialize();

			$this->assertEquals($localeId, $this->object->cascade());
		}
	}

	/**
	 * Test that composing locale tags return the correctly formatted key.
	 */
	public function testCompose() {
		$this->assertEquals('en', $this->object->compose(array(
			'language' => 'en'
		)));

		$this->assertEquals('en_US', $this->object->compose(array(
			'language' => 'en',
			'region' => 'US'
		)));

		$this->assertEquals('en_Hans_US', $this->object->compose(array(
			'language' => 'en',
			'region' => 'US',
			'script' => 'Hans'
		)));

		$this->assertEquals('en_Hans_US_NEDIS_x_prv1', $this->object->compose(array(
			'language' => 'en',
			'region' => 'US',
			'script' => 'Hans',
			'variant0' => 'NEDIS',
			'private0' => 'prv1'
		)));
	}

	/**
	 * Test that the correct locale bundle is set while parsing the HTTP accept language header.
	 */
	public function testCurrent() {
		$httpAccepts = array(
			'ex-no,ex;q=0.5' => 'ex',
			'ex-in,ex;q=0.5' => 'ex_IN',
			'ex-va,ex;q=0.5' => 'ex_VA',
			'ex-fm,ex;q=0.5' => 'ex_FM',
			'foobar' => 'ex' // Wont match and will use the fallback
		);

		foreach ($httpAccepts as $httpAccept => $localeId) {
			$_SERVER['HTTP_ACCEPT_LANGUAGE'] = $httpAccept;
			$this->object->initialize();

			$current = $this->object->current();
			$config = $current->getLocale();

			$this->assertInstanceOf('titon\libs\bundles\locales\LocaleBundle', $current);
			$this->assertEquals($localeId, $config['id']);
		}
	}

	/**
	 * Test that decomposing a locale returns the correct array of tags.
	 */
	public function testDecompose() {
		$this->assertEquals(array(
			'language' => 'en'
		), $this->object->decompose('en'));

		$this->assertEquals(array(
			'language' => 'en',
			'region' => 'US'
		), $this->object->decompose('en_US'));

		$this->assertEquals(array(
			'language' => 'en',
			'region' => 'US',
			'script' => 'Hans'
		), $this->object->decompose('en_Hans_US'));

		$this->assertEquals(array(
			'language' => 'en',
			'script' => 'Hans',
			'region' => 'US',
			'variant0' => 'NEDIS',
			'private0' => 'prv1'
		), $this->object->decompose('en_Hans_US_nedis_x_prv1'));
	}

	/**
	 * Test that setting fallbacks work.
	 */
	public function testFallbackAs() {
		$this->object->fallbackAs('ex-va');
		$this->assertEquals('ex_VA', $this->object->getFallback()->getLocale('id'));

		$this->object->fallbackAs('ex-IN');
		$this->assertEquals('ex_IN', $this->object->getFallback()->getLocale('id'));

		$this->object->fallbackAs('ex_FM');
		$this->assertEquals('ex_FM', $this->object->getFallback()->getLocale('id'));

		try {
			$this->object->fallbackAs('fakeKey');
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that all locales are setup correctly and reference the correct bundle class.
	 */
	public function testGetLocales() {
		$bundles = $this->object->getLocales();

		$this->assertEquals(5, count($bundles));
		$this->assertEquals(array('ex-va', 'ex', 'ex-fm', 'ex-in', 'no'), array_keys($bundles));

		foreach ($bundles as $bundle) {
			$this->assertInstanceOf('titon\libs\bundles\locales\LocaleBundle', $bundle);
		}
	}

	/**
	 * Test that is matches a given locale key or locale id to the current bundle.
	 */
	public function testIs() {
		$httpAccepts = array(
			'ex-no,ex;q=0.5' => array('ex', 'ex'),
			'ex-in,ex;q=0.5' => array('ex_IN', 'ex-in'),
			'ex-va,ex;q=0.5' => array('ex_VA', 'ex-va'),
			'ex-fm,ex;q=0.5' => array('ex_FM', 'ex-fm'),
			'foobar' => array('ex', 'ex') // Wont match and will use the fallback
		);

		foreach ($httpAccepts as $httpAccept => $localeId) {
			$_SERVER['HTTP_ACCEPT_LANGUAGE'] = $httpAccept;
			$this->object->initialize();

			$this->assertTrue($this->object->is($localeId[0]));
			$this->assertTrue($this->object->is($localeId[1]));
		}
	}

	/**
	 * Test that setting a locale key/ID applies the correct bundle.
	 */
	public function testSet() {
		$this->object->set('ex');
		$this->assertEquals('ex', $this->object->current()->getLocale('id'));

		$this->object->set('ex_VA');
		$this->assertEquals('ex_VA', $this->object->current()->getLocale('id'));

		$this->object->set('ex-IN');
		$this->assertEquals('ex_IN', $this->object->current()->getLocale('id'));

		$this->object->set('ex_fm');
		$this->assertEquals('ex_FM', $this->object->current()->getLocale('id'));

		try {
			$this->object->set('fakeKey');
		} catch (\Exception $e) {
			$this->assertTrue(true);
		}
	}

}
