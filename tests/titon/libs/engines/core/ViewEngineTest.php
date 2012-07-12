<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\tests\TestCase;
use titon\libs\engines\core\ViewEngine;
use \Exception;

/**
 * Test class for titon\libs\engines\core\ViewEngine.
 */
class ViewEngineTest extends TestCase {

	/**
	 * Test that buildPath() generates correct file system paths, or throws exceptions.
	 */
	public function testBuildPath() {
		// views
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			]
		]);

		$this->assertEquals(APP_MODULES . 'pages/views/public/index/index.tpl', $engine->buildPath(ViewEngine::VIEW));

		$engine->config->set('template.action', 'add');
		$this->assertEquals(APP_MODULES . 'pages/views/public/index/add.tpl', $engine->buildPath(ViewEngine::VIEW));

		$engine->config->set('template.action', 'view');
		$engine->config->set('template.ext', 'xml');
		$this->assertEquals(APP_MODULES . 'pages/views/public/index/view.xml.tpl', $engine->buildPath(ViewEngine::VIEW));

		try {
			$engine->config->set('template.controller', 'invalidFile');
			$engine->buildPath(ViewEngine::VIEW); // file doesn't exist
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// layouts
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			]
		]);

		$this->assertEquals(APP_MODULES . 'pages/views/private/layouts/default.tpl', $engine->buildPath(ViewEngine::LAYOUT));

		$engine->config->layout = 'fallback';
		$this->assertEquals(APP_VIEWS . 'layouts/fallback.tpl', $engine->buildPath(ViewEngine::LAYOUT));

		try {
			$engine->config->layout = 'invalidFile';
			$engine->buildPath(ViewEngine::LAYOUT); // file doesn't exist
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		try {
			$engine->config->layout = null;
			$engine->buildPath(ViewEngine::LAYOUT); // file doesn't exist
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// errors
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'error',
				'ext' => null
			],
			'error' => true
		]);

		$this->assertEquals(APP_MODULES . 'pages/views/private/errors/error.tpl', $engine->buildPath(ViewEngine::ERROR));

		$engine->config->set('template.action', '404');
		$this->assertEquals(APP_MODULES . 'pages/views/private/errors/404.tpl', $engine->buildPath(ViewEngine::ERROR));

		try {
			$engine->config->set('template.action', 'invalidFile');
			$engine->buildPath(ViewEngine::ERROR); // file doesn't exist
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// wrappers
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			],
			'wrapper' => 'wrapper'
		]);

		$this->assertEquals(APP_MODULES . 'pages/views/private/wrappers/wrapper.tpl', $engine->buildPath(ViewEngine::WRAPPER));

		$engine->config->wrapper = 'fallback';
		$this->assertEquals(APP_VIEWS . 'wrappers/fallback.tpl', $engine->buildPath(ViewEngine::WRAPPER));

		try {
			$engine->config->wrapper = 'invalidFile';
			$engine->buildPath(ViewEngine::WRAPPER); // file doesn't exist
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		try {
			$engine->config->wrapper = null;
			$engine->buildPath(ViewEngine::WRAPPER); // file doesn't exist
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// includes
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			],
			'wrapper' => 'wrapper'
		]);

		$this->assertEquals(APP_MODULES . 'pages/views/private/includes/include.tpl', $engine->buildPath(ViewEngine::ELEMENT, 'include'));
		$this->assertEquals(APP_MODULES . 'pages/views/private/includes/nested/include.tpl', $engine->buildPath(ViewEngine::ELEMENT, 'nested/include'));
		$this->assertEquals(APP_MODULES . 'pages/views/private/includes/nested/include.tpl', $engine->buildPath(ViewEngine::ELEMENT, 'nested\include'));
		$this->assertEquals(APP_MODULES . 'pages/views/private/includes/nested/include.tpl', $engine->buildPath(ViewEngine::ELEMENT, 'nested/include.tpl'));

		try {
			$engine->buildPath(ViewEngine::ELEMENT, 'invalidFile');
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

		$engine = new ViewEngine();
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

}