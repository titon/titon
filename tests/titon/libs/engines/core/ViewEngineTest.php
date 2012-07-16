<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\engines\core;

use titon\tests\TestCase;
use titon\libs\engines\core\ViewEngine;
use \Exception;

/**
 * Test class for titon\libs\engines\core\ViewEngine.
 */
class ViewEngineTest extends TestCase {

	/**
	 * Test that open() renders includes and it's variables.
	 */
	public function testOpen() {
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			]
		]);

		$engine->set('name', 'Titon');

		$this->assertEquals('include.tpl', $engine->open('include'));
		$this->assertEquals('include.tpl', $engine->open('include.tpl'));

		$this->assertEquals('nested/include.tpl', $engine->open('nested/include'));
		$this->assertEquals('nested/include.tpl', $engine->open('nested/include.tpl'));

		$data = [
			'filename' => 'variables.tpl',
			'type' => 'include'
		];

		$this->assertEquals('Titon - include - variables.tpl', $engine->open('variables', $data));
		$this->assertEquals('Titon - include - variables.tpl', $engine->open('variables.tpl', $data));
	}

	/**
	 * Test that run() renders the layout, wrapper, view and includes in the correct sequence.
	 */
	public function testRun() {
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			]
		]);

		$this->assertEquals('<layout>index.tpl</layout>', $engine->run());

		// with wrapper
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'add',
				'ext' => null
			],
			'wrapper' => 'wrapper'
		]);

		$this->assertEquals('<layout><wrapper>add.tpl</wrapper></layout>', $engine->run());

		// with fallback layout
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'edit',
				'ext' => null
			],
			'layout' => 'fallback'
		]);

		$this->assertEquals('<fallbackLayout>edit.tpl</fallbackLayout>', $engine->run());

		// with fallback wrapper
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'view',
				'ext' => null
			],
			'wrapper' => 'fallback'
		]);

		$this->assertEquals('<layout><fallbackWrapper>view.tpl</fallbackWrapper></layout>', $engine->run());

		// with include
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'test-include',
				'ext' => null
			]
		]);

		$this->assertEquals('<layout>test-include.tpl nested/include.tpl</layout>', $engine->run());

		// with ext and no layout
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'view',
				'ext' => 'xml'
			],
			'layout' => null
		]);

		$this->assertEquals('view.xml.tpl', $engine->run());

		// with ext and blank layout
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'view',
				'ext' => 'xml'
			],
			'layout' => 'blank'
		]);

		$this->assertEquals('view.xml.tpl', $engine->run());

		// with error
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			],
			'error' => 'error',
			'layout' => 'error'
		]);

		$engine->set('message', 'Error message!');

		$this->assertEquals('<error>Error message!</error>', $engine->run());

		// with http error
		$engine = new ViewEngine([
			'template' => [
				'module' => 'pages',
				'controller' => 'index',
				'action' => 'index',
				'ext' => null
			],
			'error' => '404',
			'layout' => 'error'
		]);

		$this->assertEquals('<error>404.tpl</error>', $engine->run());
	}

}