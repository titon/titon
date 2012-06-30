<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\Titon;
use titon\tests\TestCase;

/**
 * Test class for titon\core\Router.
 */
class RouterTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = Titon::router();
		$this->object
			->mapSlug('stringUrl', '/some/static/url')
			->mapSlug('module', ['module' => 'module'])
			->mapSlug('controller', ['controller' => 'controller'])
			->mapSlug('action', ['action' => 'action'])
			->mapSlug('extArgs', ['ext' => 'html', 123, 'abc'])
			->mapSlug('queryFragment', ['#' => 'fragment', 'foo' => 'bar'])
			->initialize();
	}

	/**
	 * Test that building a URL returns the correct Titon syntax.
	 */
	public function testBuild() {
		// module, controller, action
		$this->assertEquals('/', $this->object->build());
		$this->assertEquals('/module/index', $this->object->build(['module' => 'module']));
		$this->assertEquals('/module/index/action', $this->object->build(['module' => 'module', 'action' => 'action']));
		$this->assertEquals('/module/controller/action', $this->object->build(['module' => 'module', 'controller' => 'controller', 'action' => 'action']));
		$this->assertEquals('/pages/controller/action', $this->object->build(['controller' => 'controller', 'action' => 'action']));
		$this->assertEquals('/dash-ed/camelCase/under-score', $this->object->build(['module' => 'dash-ed', 'controller' => 'camelCase', 'action' => 'under_score']));
		$this->assertEquals('/s-p-a-c-e-s/1-2-3-numbers/punctuation', $this->object->build(['module' => 's p a c e s', 'controller' => '1 2 3 numbers', 'action' => 'punc"@*#$*&)tuation']));
		$this->assertEquals('/pages/controller/action#fragment', $this->object->build(['controller' => 'controller', 'action' => 'action', '#' => 'fragment']));

		// ext
		$this->assertEquals('/pages/index/index.html', $this->object->build(['ext' => 'html']));
		$this->assertEquals('/pages/foo/bar.json', $this->object->build(['controller' => 'foo', 'action' => 'bar', 'ext' => 'json']));
		$this->assertEquals('/api/index/index.xml', $this->object->build(['module' => 'api', 'ext' => 'xml']));

		// arguments
		$this->assertEquals('/pages/index/index/1/foo/838293', $this->object->build([1, 'foo', 838293]));
		$this->assertEquals('/pages/index/index/1/foo/838293', $this->object->build(['args' => [1, 'foo', 838293]]));
		$this->assertEquals('/users/index/action/a/b/3', $this->object->build(['module' => 'users', 'action' => 'action', 'a', 'b', 3]));
		$this->assertEquals('/pages/controller/index/a/1337', $this->object->build(['controller' => 'controller', 'args' => ['a'], 1337]));
		$this->assertEquals('/args/with/spaces/a+b+c/special+%3A1%23%24%28%3B%5B%5D+chars', $this->object->build(['module' => 'args', 'controller' => 'with', 'action' => 'spaces', 'args' => ['a b c'], 'special :1#$(;[] chars']));
		$this->assertEquals('/args/with/ext.html/123/456', $this->object->build(['module' => 'args', 'controller' => 'with', 'action' => 'ext', 'ext' => 'html', 123, 456]));
		$this->assertEquals('/args/with/fragment.json/some+Arg+UMent#fragment', $this->object->build(['module' => 'args', 'controller' => 'with', 'action' => 'fragment', 'ext' => 'json', 'some Arg UMent', '#' => 'fragment']));

		// query
		$this->assertEquals('/pages/index?foo=bar&number=1', $this->object->build(['foo' => 'bar', 'number' => 1]));
		$this->assertEquals('/pages/index?foo=bar&number=1', $this->object->build(['query' => ['foo' => 'bar', 'number' => 1]]));
		$this->assertEquals('/users/index/action/3?a=b', $this->object->build(['module' => 'users', 'action' => 'action', 'a' => 'b', 3]));
		$this->assertEquals('/pages/controller/index/1337?0=a', $this->object->build(['controller' => 'controller', 'query' => ['a'], 1337]));
		$this->assertEquals('/query/with/spaces?a=1&special=%3A1%23%24%28%3B%5B%5D+chars', $this->object->build(['module' => 'query', 'controller' => 'with', 'action' => 'spaces', 'query' => ['a' => 1, 'special' => ':1#$(;[] chars']]));
		$this->assertEquals('/query/with/ext.html?int=456&foo=bar', $this->object->build(['module' => 'query', 'controller' => 'with', 'action' => 'ext', 'ext' => 'html', 'int' => 456, 'foo' => 'bar']));
		$this->assertEquals('/query/with/fragment.json?0=some+Arg+UMent#fragment', $this->object->build(['module' => 'query', 'controller' => 'with', 'action' => 'fragment', 'ext' => 'json', 'query' => 'some Arg UMent', '#' => 'fragment']));

		// fragment
		$this->assertEquals('/pages/index#frag+ment', $this->object->build(['#' => 'frag ment']));
		$this->assertEquals('/pages/index#foo=bar&number=1', $this->object->build(['#' => ['foo' => 'bar', 'number' => 1]]));
		$this->assertEquals('/pages/index/action#frag+ment', $this->object->build(['#' => 'frag ment', 'action' => 'action']));
		$this->assertEquals('/pages/index/action.html#foo=bar&number=1', $this->object->build(['#' => ['foo' => 'bar', 'number' => 1], 'action' => 'action', 'ext' => 'html']));
		$this->assertEquals('/pages/index/action/123/abc#fragment', $this->object->build(['#' => 'fragment', 'action' => 'action', 123, 'abc']));
		$this->assertEquals('/pages/index/action.html/123/abc#foo=bar&number=1', $this->object->build(['#' => ['foo' => 'bar', 'number' => 1], 'action' => 'action', 'ext' => 'html', 'args' => [123], 'abc']));
		$this->assertEquals('/pages/index/action/123/abc?foo=bar&int=123#fragment', $this->object->build(['#' => 'fragment', 'action' => 'action', 123, 'abc', 'foo' => 'bar', 'int' => 123]));
		$this->assertEquals('/pages/index/action.html/123/abc?foo=bar&int=123#foo=bar&number=1', $this->object->build(['#' => ['foo' => 'bar', 'number' => 1], 'action' => 'action', 'ext' => 'html', 'args' => [123], 'abc', 'query' => ['foo' => 'bar'], 'int' => 123]));
	}

	/**
	 * Test that defaults() wraps the current array in fallback routes as well as parsing it to the correct format.
	 */
	public function testDefaults() {
		$this->assertEquals([
			'module' => 'pages',
			'controller' => 'index',
			'action' => 'index',
			'ext' => '',
			'query' => [],
			'args' => []
		], $this->object->defaults());

		$this->assertEquals([
			'module' => 'pages',
			'controller' => 'controller',
			'action' => 'some-action',
			'ext' => '',
			'query' => [],
			'args' => []
		], $this->object->defaults(['controller' => 'controller', 'action' => 'some_action']));

		$this->assertEquals([
			'module' => 'pages',
			'controller' => 'dashed-controller',
			'action' => 'someAction',
			'ext' => 'html',
			'query' => [],
			'args' => []
		], $this->object->defaults(['controller' => 'dashed-controller', 'action' => 'someAction', 'ext' => 'html']));

		$this->assertEquals([
			'module' => 'underscore-module',
			'controller' => 'index',
			'action' => 'some-action',
			'ext' => '',
			'query' => [],
			'args' => [],
			'#' => 'fragment'
		], $this->object->defaults(['module' => 'underscore_module', 'action' => 'some_action', '#' => 'fragment']));

		$this->assertEquals([
			'module' => 'random-module-chars',
			'controller' => 'index',
			'action' => 'index',
			'ext' => 'json',
			'query' => [],
			'args' => [],
			'#' => ['fragment' => 'array']
		], $this->object->defaults(['module' => 'ran%$dom-mo(*$#dule_c%(#hars', 'ext' => 'json', '#' => ['fragment' => 'array']]));

		$this->assertEquals([
			'module' => 'pages',
			'controller' => 'index',
			'action' => 'index',
			'ext' => '',
			'query' => [],
			'args' => [],
			'foo' => 'bar',
			'int' => 123,
			123,
			'abc'
		], $this->object->defaults([123, 'foo' => 'bar', 'abc', 'int' => 123]));

		$this->assertEquals([
			'module' => 'pages',
			'controller' => 'index',
			'action' => 'index',
			'ext' => '',
			'query' => ['foo' => 'bar', 'int' => 123],
			'args' => [123, 'abc']
		], $this->object->defaults(['query' => ['foo' => 'bar', 'int' => 123], 'args' => [123, 'abc']]));
	}

	/**
	 * Test that detect() returns a formatted URL, with or without a slug, and via string or array.
	 */
	public function testDetect() {
		$this->assertEquals('http://google.com', $this->object->detect('http://google.com'));

		// slug
		$this->assertEquals('/some/static/url', $this->object->detect('stringUrl'));
		$this->assertEquals('/pages/index/action', $this->object->detect('action'));
		$this->assertEquals('/pages/index?foo=bar#fragment', $this->object->detect('queryFragment'));

		// route
		$this->assertEquals('/module/index', $this->object->detect(['module' => 'module']));
		$this->assertEquals('/pages/f-o-o-/b-a-r/b/a/z', $this->object->detect(['controller' => 'f o o ', 'action' => 'b a r', 'b', 'a', 'z']));

		// slug via array
		$this->assertEquals('/module/index/index.html/123/abc', $this->object->detect(['module' => 'module', 'slug' => 'extArgs']));
		$this->assertEquals('/module/index/index.html/123/abc/567#fragment', $this->object->detect(['module' => 'module', 'slug' => 'extArgs', 567, '#' => 'fragment']));
	}

	/**
	 * Test that slugs() returns the fully qualified route array for the matching key.
	 */
	public function testSlugs() {
		$this->assertEquals(null, $this->object->slugs('fakeSlug'));
		$this->assertEquals('/some/static/url', $this->object->slugs('stringUrl'));

		$this->assertEquals([
			'module' => 'module',
			'controller' => 'index',
			'action' => 'index',
			'ext' => '',
			'query' => [],
			'args' => []
		], $this->object->slugs('module'));

		$this->assertEquals([
			'module' => 'pages',
			'controller' => 'controller',
			'action' => 'index',
			'ext' => '',
			'query' => [],
			'args' => []
		], $this->object->slugs('controller'));

		$this->assertEquals([
			'module' => 'pages',
			'controller' => 'index',
			'action' => 'action',
			'ext' => '',
			'query' => [],
			'args' => []
		], $this->object->slugs('action'));

		$this->assertEquals([
			'module' => 'pages',
			'controller' => 'index',
			'action' => 'index',
			'ext' => 'html',
			'query' => [],
			'args' => [],
			123,
			'abc'
		], $this->object->slugs('extArgs'));

		$this->assertEquals([
			'module' => 'pages',
			'controller' => 'index',
			'action' => 'index',
			'ext' => '',
			'query' => [],
			'args' => [],
			'foo' => 'bar',
			'#' => 'fragment'
		], $this->object->slugs('queryFragment'));
	}

	/**
	 * Test that initialize() pulls in the correct URL segments, base path and HTTP query.
	 *
	 * Run this method last as to not conflict with other tests.
	 */
	public function testInitializeAndSegmentsAndBase() {
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['PHP_SELF'] = '/index.php';
		$_SERVER['REQUEST_URI'] = '/';

		$this->object->initialize();
		$this->assertEquals('/', $this->object->base());
		$this->assertEquals('http://localhost/', $this->object->segments(true));
		$this->assertEquals('/', $this->object->segments('path'));
		$this->assertTrue(is_array($this->object->segments('query')));
		$this->assertEquals([
			'path' => '/',
			'scheme' => 'http',
			'query' => [],
			'host' => 'localhost'
		], $this->object->segments());

		// module, controller
		$_SERVER['HTTP_HOST'] = 'domain.com';
		$_SERVER['PHP_SELF'] = '/index.php/module/index';
		$_SERVER['REQUEST_URI'] = '/module/index';

		$this->object->initialize();
		$this->assertEquals('/', $this->object->base());
		$this->assertEquals('http://domain.com/module/index', $this->object->segments(true));
		$this->assertEquals('/module/index', $this->object->segments('path'));
		$this->assertTrue(is_array($this->object->segments('query')));
		$this->assertEquals([
			'path' => '/module/index',
			'scheme' => 'http',
			'query' => [],
			'host' => 'domain.com'
		], $this->object->segments());

		// module, controller, action, ext, base,
		$_SERVER['HTTP_HOST'] = 'sub.domain.com';
		$_SERVER['PHP_SELF'] = '/root/dir/index.php/module/controller/action.html';
		$_SERVER['REQUEST_URI'] = '/module/controller/action.html';

		$this->object->initialize();
		$this->assertEquals('/root/dir', $this->object->base());
		$this->assertEquals('http://sub.domain.com/root/dir/module/controller/action.html', $this->object->segments(true));
		$this->assertEquals('/module/controller/action.html', $this->object->segments('path'));
		$this->assertTrue(is_array($this->object->segments('query')));
		$this->assertEquals([
			'path' => '/module/controller/action.html',
			'scheme' => 'http',
			'query' => [],
			'host' => 'sub.domain.com'
		], $this->object->segments());

		// module, controller, action, ext, base, query, https
		$_SERVER['HTTP_HOST'] = 'subber.sub.domain.com';
		$_SERVER['PHP_SELF'] = '/rooter/root/dir/index.php/module/controller/action.html'; // query doesn't show up here
		$_SERVER['REQUEST_URI'] = '/module/controller/action.html?foo=bar&int=123';
		$_SERVER['HTTPS'] = 'on';
		$_GET = ['foo' => 'bar', 'int' => 123];

		$this->object->initialize();
		$this->assertEquals('/rooter/root/dir', $this->object->base());
		$this->assertEquals('https://subber.sub.domain.com/rooter/root/dir/module/controller/action.html?foo=bar&int=123', $this->object->segments(true));
		$this->assertEquals('/module/controller/action.html', $this->object->segments('path'));
		$this->assertTrue(is_array($this->object->segments('query')));
		$this->assertEquals([
			'path' => '/module/controller/action.html',
			'scheme' => 'https',
			'query' => ['foo' => 'bar', 'int' => 123],
			'host' => 'subber.sub.domain.com'
		], $this->object->segments());

		// module, controller, action, ext, base, query, https, args
		$_SERVER['HTTP_HOST'] = 'subbest.subber.sub.domain.com';
		$_SERVER['PHP_SELF'] = '/base/rooter/root/dir/index.php/module/controller/action.html/123/abc'; // query doesn't show up here
		$_SERVER['REQUEST_URI'] = '/module/controller/action.html/123/abc?foo=bar&int=123';
		$_SERVER['HTTPS'] = 'on';
		$_GET = ['foo' => 'bar', 'int' => 123];

		$this->object->initialize();
		$this->assertEquals('/base/rooter/root/dir', $this->object->base());
		$this->assertEquals('https://subbest.subber.sub.domain.com/base/rooter/root/dir/module/controller/action.html/123/abc?foo=bar&int=123', $this->object->segments(true));
		$this->assertEquals('/module/controller/action.html/123/abc', $this->object->segments('path'));
		$this->assertTrue(is_array($this->object->segments('query')));
		$this->assertEquals([
			'path' => '/module/controller/action.html/123/abc',
			'scheme' => 'https',
			'query' => ['foo' => 'bar', 'int' => 123],
			'host' => 'subbest.subber.sub.domain.com'
		], $this->object->segments());
	}

}