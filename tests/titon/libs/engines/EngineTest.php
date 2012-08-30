<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\engines;

use titon\tests\TestCase;
use titon\tests\fixtures\EngineFixture;
use titon\libs\helpers\html\HtmlHelper;
use \Exception;

/**
 * Test class for titon\libs\engines\Engine.
 */
class EngineTest extends TestCase {

	/**
	 * Test that addHelper() loads helpers, and throws exceptions when not a helper.
	 * Test that getHelper() returns the aliased helper names.
	 */
	public function testAddHelperAndGetHelper() {
		$engine = new EngineFixture();

		// typical helper
		$engine->addHelper('html', function() {
			return new HtmlHelper();
		});

		$this->assertInstanceOf('titon\libs\helpers\Helper', $engine->html);

		// not a helper
		$engine->addHelper('test', function() {
			return new EngineFixture();
		});

		try {
			$this->assertInstanceOf('titon\libs\helpers\Helper', $engine->test);
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		$this->assertEquals(['html', 'test'], $engine->getHelpers());
	}

	/**
	 * Test that buildPath() generates correct file system paths, or throws exceptions.
	 */
	public function testBuildPath() {
		// views
		$engine = new EngineFixture([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			]
		]);

		$this->assertEquals(APP_MODULES . 'pages/views/public/index/index.tpl', $engine->buildPath(EngineFixture::VIEW));

		$engine->config->set('template.action', 'add');
		$this->assertEquals(APP_MODULES . 'pages/views/public/index/add.tpl', $engine->buildPath(EngineFixture::VIEW));

		$engine->config->set('template.action', 'view');
		$engine->config->set('template.ext', 'xml');
		$this->assertEquals(APP_MODULES . 'pages/views/public/index/view.xml.tpl', $engine->buildPath(EngineFixture::VIEW));

		try {
			$engine->config->set('template.controller', 'invalidFile');
			$engine->buildPath(EngineFixture::VIEW); // file doesn't exist
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// layouts
		$engine = new EngineFixture([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			]
		]);

		$this->assertEquals(APP_MODULES . 'pages/views/private/layouts/default.tpl', $engine->buildPath(EngineFixture::LAYOUT));

		$engine->config->layout = 'fallback';
		$this->assertEquals(APP_VIEWS . 'layouts/fallback.tpl', $engine->buildPath(EngineFixture::LAYOUT));

		try {
			$engine->config->layout = 'invalidFile';
			$engine->buildPath(EngineFixture::LAYOUT); // file doesn't exist
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		try {
			$engine->config->layout = null;
			$engine->buildPath(EngineFixture::LAYOUT); // file doesn't exist
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// errors
		$engine = new EngineFixture([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'error',
				'ext' => null
			],
			'folder' => 'errors'
		]);

		$this->assertEquals(APP_MODULES . 'pages/views/private/errors/error.tpl', $engine->buildPath(EngineFixture::VIEW));

		$engine->override('errors', 404);
		$this->assertEquals(APP_MODULES . 'pages/views/private/errors/404.tpl', $engine->buildPath(EngineFixture::VIEW));

		try {
			$engine->config->set('template.action', 'invalidFile');
			$engine->buildPath(EngineFixture::VIEW); // file doesn't exist
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// wrappers
		$engine = new EngineFixture([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			],
			'wrapper' => 'wrapper'
		]);

		$this->assertEquals(APP_MODULES . 'pages/views/private/wrappers/wrapper.tpl', $engine->buildPath(EngineFixture::WRAPPER));

		$engine->config->wrapper = 'fallback';
		$this->assertEquals(APP_VIEWS . 'wrappers/fallback.tpl', $engine->buildPath(EngineFixture::WRAPPER));

		try {
			$engine->config->wrapper = 'invalidFile';
			$engine->buildPath(EngineFixture::WRAPPER); // file doesn't exist
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		try {
			$engine->config->wrapper = null;
			$engine->buildPath(EngineFixture::WRAPPER); // file doesn't exist
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// includes
		$engine = new EngineFixture([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			],
			'wrapper' => 'wrapper'
		]);

		$this->assertEquals(APP_MODULES . 'pages/views/private/includes/include.tpl', $engine->buildPath(EngineFixture::ELEMENT, 'include'));
		$this->assertEquals(APP_MODULES . 'pages/views/private/includes/nested/include.tpl', $engine->buildPath(EngineFixture::ELEMENT, 'nested/include'));
		$this->assertEquals(APP_MODULES . 'pages/views/private/includes/nested/include.tpl', $engine->buildPath(EngineFixture::ELEMENT, 'nested\include'));
		$this->assertEquals(APP_MODULES . 'pages/views/private/includes/nested/include.tpl', $engine->buildPath(EngineFixture::ELEMENT, 'nested/include.tpl'));

		try {
			$engine->buildPath(EngineFixture::ELEMENT, 'invalidFile');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that get() and set() handle data correctly. Variable names are inflected to be usable in the page.
	 */
	public function testGetAndSet() {
		$data = [
			'integer' => 123,
			'boolean' => true,
			'string' => 'abc',
			'null' => null,
			'array' => [
				'foo' => 'bar',
				123
			],
			'invalid key' => 'value',
			'123 numeric' => 456,
			'invalid #$*)#)_#@ chars' => false
		];

		$engine = new EngineFixture();
		$engine->set($data);

		$this->assertEquals([
			'integer' => 123,
			'boolean' => true,
			'string' => 'abc',
			'null' => null,
			'array' => [
				'foo' => 'bar',
				123
			],
			'invalidkey' => 'value',
			'_123numeric' => 456,
			'invalid_chars' => false
		], $engine->get());

		$engine->set('array', []);
		$this->assertEquals([], $engine->get('array'));

		$engine->set('123456789', true);
		$this->assertEquals(true, $engine->get('_123456789'));

		$this->assertEquals(null, $engine->get('fakeKey'));
	}

	/**
	 * Test that setup() overwrites configuration depending on the type passed.
	 */
	public function testSetup() {
		$engine = new EngineFixture([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			]
		]);

		// toggle rendering
		$engine->setup(false);
		$this->assertFalse($engine->config->render);

		$engine->setup(null);
		$this->assertFalse($engine->config->render);

		$engine->setup(true);
		$this->assertTrue($engine->config->render);

		// change action
		$this->assertEquals('index', $engine->config->template['action']);

		$engine->setup('foo');
		$this->assertEquals('foo', $engine->config->template['action']);

		$engine->setup('bar');
		$this->assertEquals('bar', $engine->config->template['action']);

		// change action via array
		$engine->setup([
			'template' => 'index'
		]);
		$this->assertEquals('index', $engine->config->template['action']);

		// change wrapper or layout
		$engine->setup([
			'layout' => 'fallback',
			'wrapper' => 'wrapper'
		]);
		$this->assertEquals('fallback', $engine->config->layout);
		$this->assertEquals('wrapper', $engine->config->wrapper);

		// change template
		$engine->setup([
			'template' => [
				'module' => 'users',
				'controller' => 'dashboard'
			]
		]);
		$this->assertEquals([
			'module' => 'users',
			'controller' => 'dashboard',
			'action' => 'index',
			'ext' => null
		], $engine->config->template);
	}

}