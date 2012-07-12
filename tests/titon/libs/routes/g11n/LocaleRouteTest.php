<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\tests\TestCase;
use titon\libs\routes\g11n\LocaleRoute;

/**
 * Test class for titon\libs\routes\g11n\LocaleRoute.
 */
class LocaleRouteTest extends TestCase {

	/**
	 * Test that compile() appends the correct locale pattern.
	 */
	public function testCompile() {
		$moduleControllerActionExt = new LocaleRoute('/{module}/{controller}/{action}.{ext}');
		$moduleControllerAction = new LocaleRoute('/{module}/{controller}/{action}');
		$moduleController = new LocaleRoute('/{module}/{controller}');
		$module = new LocaleRoute('/{module}');
		$root = new LocaleRoute('/', [], ['static' => true]);

		$this->assertEquals('/^\/([a-z]{2}(?:-[a-z]{2})?)\/([a-z\_\-\+]+)\/([a-z\_\-\+]+)\/([a-z\_\-\+]+)\.([a-z\_\-\+]+)(.*)?/i', $moduleControllerActionExt->compile());
		$this->assertEquals('/^\/([a-z]{2}(?:-[a-z]{2})?)\/([a-z\_\-\+]+)\/([a-z\_\-\+]+)\/([a-z\_\-\+]+)(.*)?/i', $moduleControllerAction->compile());
		$this->assertEquals('/^\/([a-z]{2}(?:-[a-z]{2})?)\/([a-z\_\-\+]+)\/([a-z\_\-\+]+)(.*)?/i', $moduleController->compile());
		$this->assertEquals('/^\/([a-z]{2}(?:-[a-z]{2})?)\/([a-z\_\-\+]+)(.*)?/i', $module->compile());
		$this->assertEquals('/^\/([a-z]{2}(?:-[a-z]{2})?)\/(.*)?/i', $root->compile());

		$multi = new LocaleRoute('{alpha}/[numeric]/(wildcard)/');

		$patterns = new LocaleRoute('<alnum>/<locale>', [], [
			'patterns' => [
				'alnum' => LocaleRoute::ALNUM,
				'locale' => '([a-z]{2}(?:-[a-z]{2})?)'
			]
		]);

		$withPattern = new LocaleRoute('/<locale>/{alpha}/(wildcard)/[numeric]/{alnum}', [], [
			'patterns' => [
				'alnum' => LocaleRoute::ALNUM,
				'locale' => '([a-z]{2}(?:-[a-z]{2})?)'
			]
		]);

		$withoutPattern = new LocaleRoute('/<locale>/{alpha}/(wildcard)/[numeric]/{alnum}', [], [
			'patterns' => [
				'alnum' => LocaleRoute::ALNUM
			]
		]);

		$this->assertEquals('/^\/([a-z]{2}(?:-[a-z]{2})?)\/([a-z\_\-\+]+)\/([0-9]+)\/(.*)(.*)?/i', $multi->compile());
		$this->assertEquals('/^\/([a-z]{2}(?:-[a-z]{2})?)\/([a-z0-9\_\-\+]+)\/([a-z]{2}(?:-[a-z]{2})?)(.*)?/i', $patterns->compile());
		$this->assertEquals('/^\/([a-z]{2}(?:-[a-z]{2})?)\/([a-z\_\-\+]+)\/(.*)\/([0-9]+)\/([a-z\_\-\+]+)(.*)?/i', $withPattern->compile());
		$this->assertEquals('/^\/([a-z]{2}(?:-[a-z]{2})?)\/([a-z\_\-\+]+)\/(.*)\/([0-9]+)\/([a-z\_\-\+]+)(.*)?/i', $withoutPattern->compile());
	}

	/**
	 * Test that isMatch() returns a valid response.
	 * Test that param() returns a single value, or all values.
	 * Test that url() returns the current URL.
	 */
	public function testIsMatchAndParamAndUrl() {
		$url = '/en-us/blog/2012/02/26';
		$route = new LocaleRoute('/blog/[year]/[month]/[day]', [
			'module' => 'blog',
			'controller' => 'api',
			'action' => 'archives',
			'custom' => 'value'
		]);

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'blog',
			'controller' => 'api',
			'action' => 'archives',
			'query' => [],
			'args' => [],
			'year' => 2012,
			'month' => 2,
			'day' => 26,
			'custom' => 'value',
			'locale' => 'en-us'
		], $route->param());

		$this->assertEquals('blog', $route->param('module'));
		$this->assertEquals('archives', $route->param('action'));
		$this->assertEquals(2012, $route->param('year'));
		$this->assertEquals(2, $route->param('month'));
		$this->assertEquals(26, $route->param('day'));
		$this->assertEquals('value', $route->param('custom'));
		$this->assertEquals(null, $route->param('foobar'));
		$this->assertEquals('en-us', $route->param('locale'));
		$this->assertEquals($url, $route->url());

		// module, controller, action
		$url = '/en/forum/topic/view/123';
		$route = new LocaleRoute('/{module}/{controller}/{action}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'forum',
			'controller' => 'topic',
			'action' => 'view',
			'query' => [],
			'args' => [123],
			'locale' => 'en'
		], $route->param());

		// invalid locale
		$url = '/foo-bar/forum/topic/view/123';
		$route = new LocaleRoute('/{module}/{controller}/{action}');

		$this->assertFalse($route->isMatch($url));

		// no locale
		$url = '/forum/topic/view/123';
		$route = new LocaleRoute('/{module}/{controller}/{action}');

		$this->assertFalse($route->isMatch($url));
	}

}