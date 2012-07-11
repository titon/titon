<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\tests\TestCase;
use titon\libs\routes\core\DefaultRoute;

/**
 * Test class for titon\libs\routes\core\DefaultRoute.
 */
class DefaultRouteTest extends TestCase {

	/**
	 * Test that compile() generates the correct regex pattern.
	 */
	public function testCompile() {
		$moduleControllerActionExt = new DefaultRoute('/{module}/{controller}/{action}.{ext}');
		$moduleControllerAction = new DefaultRoute('/{module}/{controller}/{action}');
		$moduleController = new DefaultRoute('/{module}/{controller}');
		$module = new DefaultRoute('/{module}');
		$root = new DefaultRoute('/', [], ['static' => true]);

		$this->assertEquals('/^\/([a-z\_\-\+]+)\/([a-z\_\-\+]+)\/([a-z\_\-\+]+)\.([a-z\_\-\+]+)(.*)?/i', $moduleControllerActionExt->compile());
		$this->assertEquals('/^\/([a-z\_\-\+]+)\/([a-z\_\-\+]+)\/([a-z\_\-\+]+)(.*)?/i', $moduleControllerAction->compile());
		$this->assertEquals('/^\/([a-z\_\-\+]+)\/([a-z\_\-\+]+)(.*)?/i', $moduleController->compile());
		$this->assertEquals('/^\/([a-z\_\-\+]+)(.*)?/i', $module->compile());
		$this->assertEquals('/^\/(.*)?/i', $root->compile());

		$multi = new DefaultRoute('{alpha}/[numeric]/(wildcard)/');

		$patterns = new DefaultRoute('<alnum>/<locale>', [], [
			'patterns' => [
				'alnum' => DefaultRoute::ALNUM,
				'locale' => '([a-z]{2}(?:-[a-z]{2})?)'
			]
		]);

		$allTypes = new DefaultRoute('/<locale>/{alpha}/(wildcard)/[numeric]/{alnum}', [], [
			'patterns' => [
				'alnum' => DefaultRoute::ALNUM,
				'locale' => '([a-z]{2}(?:-[a-z]{2})?)'
			]
		]);

		$this->assertEquals('/^\/([a-z\_\-\+]+)\/([0-9]+)\/(.*)(.*)?/i', $multi->compile());
		$this->assertEquals('/^\/([a-z0-9\_\-\+]+)\/([a-z]{2}(?:-[a-z]{2})?)(.*)?/i', $patterns->compile());
		$this->assertEquals('/^\/([a-z]{2}(?:-[a-z]{2})?)\/([a-z\_\-\+]+)\/(.*)\/([0-9]+)\/([a-z\_\-\+]+)(.*)?/i', $allTypes->compile());
	}

	/**
	 * Test that isMatch() works for /module/controller/action paths, but also accounts for extensions.
	 */
	public function testIsMatchModuleControllerActionExt() {
		$url = '/forum/topic/stats.html';
		$route = new DefaultRoute('/{module}/{controller}/{action}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => 'html',
			'module' => 'forum',
			'controller' => 'topic',
			'action' => 'stats',
			'query' => [],
			'args' => []
		], $route->param());

		// single argument
		$url = '/forum/topic/view/123.xml';
		$route = new DefaultRoute('/{module}/{controller}/{action}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => 'xml',
			'module' => 'forum',
			'controller' => 'topic',
			'action' => 'view',
			'query' => [],
			'args' => [123]
		], $route->param());

		// multi arguments
		$url = '/forum/topic/view/123/abc.json';
		$route = new DefaultRoute('/{module}/{controller}/{action}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => 'json',
			'module' => 'forum',
			'controller' => 'topic',
			'action' => 'view',
			'query' => [],
			'args' => [123, 'abc']
		], $route->param());

		// invalid module
		$url = '/foobar/dashboard/edit.xhtml';
		$route = new DefaultRoute('/{module}/{controller}/{action}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => 'xhtml',
			'module' => 'foobar',
			'controller' => 'dashboard',
			'action' => 'edit',
			'query' => [],
			'args' => []
		], $route->param());

		// invalid controller
		$url = '/forum/foobar/view/123.php';
		$route = new DefaultRoute('/{module}/{controller}/{action}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => 'php',
			'module' => 'forum',
			'controller' => 'foobar',
			'action' => 'view',
			'query' => [],
			'args' => [123]
		], $route->param());
	}

	/**
	 * Test that isMatch() works for /module/controller/action paths.
	 */
	public function testIsMatchModuleControllerAction() {
		$url = '/forum/topic/stats';
		$route = new DefaultRoute('/{module}/{controller}/{action}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'forum',
			'controller' => 'topic',
			'action' => 'stats',
			'query' => [],
			'args' => []
		], $route->param());

		// single argument
		$url = '/forum/topic/view/123';
		$route = new DefaultRoute('/{module}/{controller}/{action}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'forum',
			'controller' => 'topic',
			'action' => 'view',
			'query' => [],
			'args' => [123]
		], $route->param());

		// multi arguments
		$url = '/forum/topic/view/123/abc';
		$route = new DefaultRoute('/{module}/{controller}/{action}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'forum',
			'controller' => 'topic',
			'action' => 'view',
			'query' => [],
			'args' => [123, 'abc']
		], $route->param());

		// invalid module
		$url = '/foobar/dashboard/edit';
		$route = new DefaultRoute('/{module}/{controller}/{action}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'foobar',
			'controller' => 'dashboard',
			'action' => 'edit',
			'query' => [],
			'args' => []
		], $route->param());

		// invalid controller
		$url = '/forum/foobar/view/123';
		$route = new DefaultRoute('/{module}/{controller}/{action}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'forum',
			'controller' => 'foobar',
			'action' => 'view',
			'query' => [],
			'args' => [123]
		], $route->param());
	}

	/**
	 * Test that isMatch() works for /module/controller paths.
	 */
	public function testIsMatchModuleController() {
		$url = '/forum/topic';
		$route = new DefaultRoute('/{module}/{controller}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'forum',
			'controller' => 'topic',
			'action' => 'index',
			'query' => [],
			'args' => []
		], $route->param());

		// invalid controller
		$url = '/forum/foobar';
		$route = new DefaultRoute('/{module}/{controller}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'forum',
			'controller' => 'foobar',
			'action' => 'index',
			'query' => [],
			'args' => []
		], $route->param());
	}

	/**
	 * Test that isMatch() works for /module paths.
	 */
	public function testIsMatchModule() {
		$url = '/users';
		$route = new DefaultRoute('/{module}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'users',
			'controller' => 'index',
			'action' => 'index',
			'query' => [],
			'args' => []
		], $route->param());

		// invalid controller
		$url = '/foobar';
		$route = new DefaultRoute('/{module}');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'foobar',
			'controller' => 'index',
			'action' => 'index',
			'query' => [],
			'args' => []
		], $route->param());
	}

	/**
	 * Test that isMatch() works for / root paths.
	 */
	public function testIsMatch() {
		$url = '/';
		$route = new DefaultRoute('/');

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'pages',
			'controller' => 'index',
			'action' => 'index',
			'query' => [],
			'args' => []
		], $route->param());
	}

	/**
	 * Test that isMatch() works for custom routes and tokens.
	 */
	public function testIsMatchCustom() {
		$url = '/user/miles';
		$route = new DefaultRoute('/user/{username}', [
			'module' => 'users',
			'controller' => 'profile'
		]);

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'users',
			'controller' => 'profile',
			'action' => 'index',
			'query' => [],
			'args' => [],
			'username' => 'miles'
		], $route->param());

		$url = '/blog/123456';
		$route = new DefaultRoute('/blog/[id]', [
			'module' => 'blog',
			'controller' => 'api',
			'action' => 'read'
		]);

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'blog',
			'controller' => 'api',
			'action' => 'read',
			'query' => [],
			'args' => [],
			'id' => 123456
		], $route->param());

		$url = '/blog/2012/02/26';
		$route = new DefaultRoute('/blog/[year]/[month]/[day]', [
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
			'custom' => 'value'
		], $route->param());

		$url = '/regex/123-abc';
		$route = new DefaultRoute('/regex/<pattern>', [
			'module' => 'regex'
		], [
			'patterns' => [
				'pattern' => '([0-9]+\-[a-z]+)'
			]
		]);

		$this->assertTrue($route->isMatch($url));
		$this->assertEquals([
			'ext' => '',
			'module' => 'regex',
			'controller' => 'index',
			'action' => 'index',
			'query' => [],
			'args' => [],
			'pattern' => '123-abc'
		], $route->param());
	}

	/**
	 * Test that isMethod() always returns true if no method scope was set. If a scope was set, the method must be in the whitelist.
	 */
	public function testIsMethod() {
		$noMethod = new DefaultRoute('/');
		$noMethod->request->toggleCache(false);

		$singleMethod = new DefaultRoute('/', [], ['method' => 'POST']);
		$singleMethod->request->toggleCache(false);

		$multiMethod = new DefaultRoute('/', [], ['method' => ['post', 'put']]);
		$multiMethod->request->toggleCache(false);

		$_SERVER['REQUEST_METHOD'] = 'GET';

		$this->assertTrue($noMethod->isMethod());
		$this->assertFalse($singleMethod->isMethod());
		$this->assertFalse($multiMethod->isMethod());

		$_SERVER['REQUEST_METHOD'] = 'POST';

		$this->assertTrue($noMethod->isMethod());
		$this->assertTrue($singleMethod->isMethod());
		$this->assertTrue($multiMethod->isMethod());

		$_SERVER['REQUEST_METHOD'] = 'PUT';

		$this->assertTrue($noMethod->isMethod());
		$this->assertFalse($singleMethod->isMethod());
		$this->assertTrue($multiMethod->isMethod());
	}

	/**
	 * Test that isSecure() returns true always, or false if https is off and secure is on.
	 */
	public function testIsSecure() {
		$unsecureRoute = new DefaultRoute('/', [], ['secure' => false]);
		$secureRoute = new DefaultRoute('/', [], ['secure' => true]);

		$this->assertTrue($unsecureRoute->isSecure());
		$this->assertFalse($secureRoute->isSecure());

		$_SERVER['HTTPS'] = 'on';

		$this->assertTrue($unsecureRoute->isSecure());
		$this->assertTrue($secureRoute->isSecure());

		$_SERVER['HTTPS'] = 'off';
	}

	/**
	 * Test that isStatic() returns true when the route has no patterns.
	 */
	public function testIsStatic() {
		$route = new DefaultRoute('/', [], ['static' => false]);
		$staticRoute = new DefaultRoute('/', [], ['static' => true]);
		$tokenRoute = new DefaultRoute('/{module}');

		$this->assertTrue($route->isStatic());
		$this->assertTrue($staticRoute->isStatic());
		$this->assertFalse($tokenRoute->isStatic());
	}

	/**
	 * Test that param() returns a single value, or all values.
	 * Test that url() returns the current URL.
	 */
	public function testParamAndUrl() {
		$url = '/blog/2012/02/26';
		$route = new DefaultRoute('/blog/[year]/[month]/[day]', [
			'module' => 'blog',
			'controller' => 'api',
			'action' => 'archives',
			'custom' => 'value'
		]);

		$route->match($url);

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
			'custom' => 'value'
		], $route->param());

		$this->assertEquals('blog', $route->param('module'));
		$this->assertEquals('archives', $route->param('action'));
		$this->assertEquals(2012, $route->param('year'));
		$this->assertEquals(2, $route->param('month'));
		$this->assertEquals(26, $route->param('day'));
		$this->assertEquals('value', $route->param('custom'));
		$this->assertEquals(null, $route->param('foobar'));
		$this->assertEquals($url, $route->url());
	}

}