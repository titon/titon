<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\net;

use titon\Titon;
use titon\tests\TestCase;
use titon\net\Request;

/**
 * Test class for titon\net\Request.
 */
class RequestTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		Titon::router()->initialize();

		$_GET = [
			'key' => 'value',
			'Model' => [
				'foo' => 'bar'
			]
		];

		$_POST = [
			'_method' => 'PUT',
			'key' => 'value',
			'Model' => [
				'foo' => 'bar'
			]
		];

		$_FILES = [
			'file' => [
				'name' => 'file1.jpg',
				'type' => 'image/jpeg',
				'tmp_name' => '/tmp/phpUkYTB5',
				'error' => 0,
				'size' => 307808
			],
			'two' => [
				'name' => ['file' => 'file2.png'],
				'type' => ['file' => 'image/png'],
				'tmp_name' => ['file' => '/tmp/phpo3HxIF'],
				'error' => ['file' => 0],
				'size' => ['file' => 10554]
			],
			'three' => [
				'name' => [
					'two' => ['file' => 'file3.png'],
				],
				'type' => [
					'two' => ['file' => 'image/png'],
				],
				'tmp_name' => [
					'two' => ['file' => '/tmp/phpgUtcPf'],
				],
				'error' => [
					'two' => ['file' => 0],
				],
				'size' => [
					'two' => ['file' => 19571],
				]
			],
			'four' => [
				'name' => [
					'three' => [
						'two' => ['file' => 'file4.jpg'],
					],
				],
				'type' => [
					'three' => [
						'two' => ['file' => 'image/jpeg'],
					],
				],
				'tmp_name' => [
					'three' => [
						'two' => ['file' => '/tmp/phpMTxSVP'],
					],
				],
				'error' => [
					'three' => [
						'two' => ['file' => 0],
					],
				],
				'size' => [
					'three' => [
						'two' => ['file' => 307808],
					],
				],
			],
		];

		$this->object = new Request();
		$this->object->toggleCache(false);
	}

	/**
	 * Test that initialize() re-formats GET, POST and FILES to a usable structure.
	 */
	public function testInitialize() {
		$this->assertEquals([
			'key' => 'value',
			'Model' => [
				'foo' => 'bar'
			]
		], $this->object->get);

		$this->assertEquals([
			'key' => 'value',
			'Model' => [
				'foo' => 'bar'
			]
		], $this->object->post);

		$this->assertEquals([
			'file' => [
				'name' => 'file1.jpg',
				'type' => 'image/jpeg',
				'tmp_name' => '/tmp/phpUkYTB5',
				'error' => 0,
				'size' => 307808
			],
			'two' => [
				'file' => [
					'name' => 'file2.png',
					'type' => 'image/png',
					'tmp_name' => '/tmp/phpo3HxIF',
					'error' => 0,
					'size' => 10554
				],
			],
			'three' => [
				'two' => [
					'file' => [
						'name' => 'file3.png',
						'type' => 'image/png',
						'tmp_name' => '/tmp/phpgUtcPf',
						'error' => 0,
						'size' => 19571
					],
				],
			],
			'four' => [
				'three' => [
					'two' => [
						'file' => [
							'name' => 'file4.jpg',
							'type' => 'image/jpeg',
							'tmp_name' => '/tmp/phpMTxSVP',
							'error' => 0,
							'size' => 307808
						],
					],
				],
			],
		], $this->object->files);
	}

	/**
	 * Test that accepts() returns true if the content type is found within the Accept header.
	 */
	public function testAccepts() {
		$_SERVER['HTTP_ACCEPT'] = 'text/xml,application/xml;q=0.9,application/xhtml+xml,text/html,text/plain,image/png';

		$this->assertTrue($this->object->accepts('html'));
		$this->assertTrue($this->object->accepts('xhtml'));
		$this->assertTrue($this->object->accepts('xml'));
		$this->assertFalse($this->object->accepts('json'));

		$_SERVER['HTTP_ACCEPT'] = 'application/json,*/*';

		$this->assertTrue($this->object->accepts('json'));
		$this->assertTrue($this->object->accepts('html')); // */*
		$this->assertFalse($this->object->accepts('xml'));

		$_SERVER['HTTP_ACCEPT'] = 'text/xml,text/html';

		$this->assertTrue($this->object->accepts('text/html'));
		$this->assertTrue($this->object->accepts(['text/xml', 'application/json']));
		$this->assertFalse($this->object->accepts('text/plain'));
		$this->assertFalse($this->object->accepts(['image/png', 'application/json']));
	}

	/**
	 * Test that acceptsCharset() returns true of the charset is found within the Accept-Charset header.
	 */
	public function testAcceptsCharset() {
		$_SERVER['HTTP_ACCEPT_CHARSET'] = 'UTF-8';

		$this->assertTrue($this->object->acceptsCharset());
		$this->assertTrue($this->object->acceptsCharset('utf-8'));
		$this->assertFalse($this->object->acceptsCharset('iso-8859-1'));

		$_SERVER['HTTP_ACCEPT_CHARSET'] = 'ISO-8859-1';

		$this->assertTrue($this->object->acceptsCharset('iso-8859-1'));
	}

	/**
	 * Test that acceptsLanguage() returns true if the locale is found within the Accept-Language header.
	 */
	public function testAcceptsLanguage() {
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-us,en;q=0.8,fr-fr;q=0.5,fr;q=0.3';

		$this->assertTrue($this->object->acceptsLanguage());
		$this->assertTrue($this->object->acceptsLanguage('en-US'));
		$this->assertTrue($this->object->acceptsLanguage('en'));
		$this->assertTrue($this->object->acceptsLanguage('fr'));
		$this->assertFalse($this->object->acceptsLanguage('DE'));
	}

	/**
	 * Test that clientIp() grabs the correct IP from the environment.
	 */
	public function testClientIp() {
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.3, 10.0.255.255, proxy.10.1';
		$_SERVER['HTTP_CLIENT_IP'] = '192.168.1.2';
		$_SERVER['REMOTE_ADDR'] = '192.168.1.1';

		$this->assertEquals('192.168.1.2', $this->object->clientIp());
		$this->assertEquals('192.168.1.3', $this->object->clientIp(false)); // not safe

		unset($_SERVER['HTTP_X_FORWARDED_FOR']);

		$this->assertEquals('192.168.1.2', $this->object->clientIp());
		$this->assertEquals('192.168.1.2', $this->object->clientIp(false));

		unset($_SERVER['HTTP_CLIENT_IP']);

		$this->assertEquals('192.168.1.1', $this->object->clientIp());
	}

	/**
	 * Test that env() returns an $_ENV or $_SERVER variable.
	 */
	public function testEnv() {
		$this->assertEquals('localhost', $this->object->env('HTTP_HOST'));
		$this->assertEquals('localhost', $this->object->env('host'));
		$this->assertEquals('/index.php', $this->object->env('PHP_SELF'));

		$_SERVER['HTTP_ACCEPT_CHARSET'] = 'UTF-8';
		$_SERVER['REMOTE_ADDR'] = '192.168.1.1';
		$_SERVER['HTTP_ACCEPT'] = 'text/xml,text/html';

		$this->assertEquals('text/xml,text/html', $this->object->env('accept'));
		$this->assertEquals('UTF-8', $this->object->env('Accept-Charset'));
		$this->assertEquals(null, $this->object->env('Fake-Header'));
	}

	/**
	 * Test that isAjax() returns true if the request with header is set.
	 */
	public function testIsAjax() {
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		$this->assertTrue($this->object->isAjax());

		unset($_SERVER['HTTP_X_REQUESTED_WITH']);
		$this->assertFalse($this->object->isAjax());
	}

	/**
	 * Test that isGet(), isDelete(), isPost(), isPut() return true when the request method matches up.
	 */
	public function testIsMethods() {
		$this->assertTrue($this->object->isPut()); // set in $_POST

		$_SERVER['REQUEST_METHOD'] = 'get';
		$this->assertTrue($this->object->isGet());

		$_SERVER['REQUEST_METHOD'] = 'DELETE';
		$this->assertTrue($this->object->isDelete());

		$_SERVER['REQUEST_METHOD'] = 'post';
		$this->assertTrue($this->object->isPost());

		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$this->assertTrue($this->object->isMethod('put'));
	}

	/**
	 * Test that isFlash() returns true when flash exists in the user agent.
	 */
	public function testIsFlash() {
		$_SERVER['HTTP_USER_AGENT'] = '';
		$this->assertFalse($this->object->isFlash());

		$_SERVER['HTTP_USER_AGENT'] = 'Shockwave Flash';
		$this->assertTrue($this->object->isFlash());

		$_SERVER['HTTP_USER_AGENT'] = 'Adobe Flash';
		$this->assertTrue($this->object->isFlash());
	}

	/**
	 * Test that isMobile() returns true when the user agent contains mobile info.
	 */
	public function testIsMobile() {
		$_SERVER['HTTP_USER_AGENT'] = '';
		$this->assertFalse($this->object->isMobile());

		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Linux; U; Android 2.3; en-us) AppleWebKit/999+ (KHTML, like Gecko) Safari/999.9';
		$this->assertTrue($this->object->isMobile());

		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (BlackBerry; U; BlackBerry 9800; it) AppleWebKit/534.8+ (KHTML, like Gecko) Version/6.0.0.668 Mobile Safari/534.8+';
		$this->assertTrue($this->object->isMobile());

		$_SERVER['HTTP_USER_AGENT'] = 'Nokia5320XpressMusic/GoBrowser/1.6.0.70';
		$this->assertTrue($this->object->isMobile());

		$_SERVER['HTTP_USER_AGENT'] = 'Opera/9.80 (J2ME/MIDP; Opera Mini/5.0.18741/870; U; fr) Presto/2.4.15';
		$this->assertTrue($this->object->isMobile());

		$_SERVER['HTTP_USER_AGENT'] = 'iPhone';
		$this->assertTrue($this->object->isMobile());
	}

	/**
	 * Test that isSecure() returns true when under HTTPS/SSL.
	 */
	public function testIsSecure() {
		$_SERVER['HTTPS'] = 'off';
		$_SERVER['SERVER_PORT'] = 80;

		$this->assertFalse($this->object->isSecure());

		$_SERVER['HTTPS'] = 'on';
		$_SERVER['SERVER_PORT'] = 80;

		$this->assertTrue($this->object->isSecure());

		$_SERVER['HTTPS'] = 'off';
		$_SERVER['SERVER_PORT'] = 443;

		$this->assertTrue($this->object->isSecure());

		$_SERVER['HTTPS'] = true;
		unset($_SERVER['SERVER_PORT']);

		$this->assertTrue($this->object->isSecure());

		$_SERVER['HTTPS'] = 0;

		$this->assertFalse($this->object->isSecure());
	}

	/**
	 * Test that method() returns the current HTTP request method.
	 */
	public function testMethod() {
		$this->assertEquals('put', $this->object->method()); // set in $_POST

		$_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'post';
		$this->assertEquals('post', $this->object->method());

		$_SERVER['REQUEST_METHOD'] = 'get';
		unset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
		$this->assertEquals('get', $this->object->method());
	}

	/**
	 * Test that referrer() returns the HTTP referrer and trims self domains.
	 */
	public function testReferrer() {
		$_SERVER['HTTP_REFERER'] = '';
		$this->assertEquals('/', $this->object->referrer());

		$_SERVER['HTTP_REFERER'] = 'http://google.com';
		$this->assertEquals('http://google.com', $this->object->referrer());

		$_SERVER['HTTP_REFERER'] = 'http://localhost/module/controller';
		$this->assertEquals('/module/controller', $this->object->referrer());
	}

	/**
	 * Test that serverIp() returns the server IP.
	 */
	public function testServerIp() {
		$_SERVER['SERVER_ADDR'] = '127.0.0.1';
		$this->assertEquals('127.0.0.1', $this->object->serverIp());
	}

	/**
	 * Test that userAgent() returns the clients browser info.
	 */
	public function testUserAgent() {
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64; rv:12.0) Gecko/20100101 Firefox/12.0';
		$this->assertEquals('Mozilla/5.0 (X11; Linux x86_64; rv:12.0) Gecko/20100101 Firefox/12.0', $this->object->userAgent());
	}

	/**
	 * Test that get(), set(), has() interact with internal request data.
	 */
	public function testGetSetHas() {
		$this->assertFalse($this->object->has('key'));
		$this->assertEquals(null, $this->object->get('key'));

		$this->object->set('key', 'value');

		$this->assertTrue($this->object->has('key'));
		$this->assertEquals('value', $this->object->get('key'));
	}

}