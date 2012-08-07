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
use titon\constant\Http;
use titon\libs\translators\messages\MessageTranslator;
use titon\tests\TestCase;
use titon\net\Response;
use \Exception;

/**
 * Test class for titon\net\Response.
 */
class ResponseTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new Response(['debug' => true]);

		// used for contentLanguage()
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ex-va,ex;q=0.5';
		Titon::g11n()->setup('ex-va')->setup('no')->fallbackAs('ex')->setTranslator(new MessageTranslator())->initialize();
	}

	/**
	 * Test that acceptRanges() sets the correct header.
	 */
	public function testAcceptRanges() {
		$this->object->acceptRanges();
		$this->assertEquals('Accept-Ranges: bytes', $this->object->getHeader('Accept-Ranges'));

		$this->object->acceptRanges('none');
		$this->assertEquals('Accept-Ranges: none', $this->object->getHeader('Accept-Ranges'));

		$this->object->acceptRanges('custom');
		$this->assertEquals('Accept-Ranges: custom', $this->object->getHeader('Accept-Ranges'));
	}

	/**
	 * Test that age() sets the correct header.
	 */
	public function testAge() {
		$this->object->age(120);
		$this->assertEquals('Age: 120', $this->object->getHeader('Age'));

		$this->object->age('+1 hour');
		$this->assertEquals('Age: 3600', $this->object->getHeader('Age'));
	}

	/**
	 * Test that allow() sets the correct header.
	 */
	public function testAllow() {
		$this->object->allow('get');
		$this->assertEquals('Allow: GET', $this->object->getHeader('Allow'));

		$this->object->allow(['POST', 'put', 'CUSTOM']);
		$this->assertEquals('Allow: POST, PUT', $this->object->getHeader('Allow'));
	}

	/**
	 * Test that cache() sets the correct headers.
	 */
	public function testCache() {
		$this->object->cache('+12 hours');
		$this->assertEquals('Expires: ' . gmdate(Http::DATE_FORMAT, strtotime('+12 hours')), $this->object->getHeader('Expires'));
		$this->assertEquals('Cache-Control: private, max-age=43200, post-check=43200, pre-check=0', $this->object->getHeader('Cache-Control'));
	}

	/**
	 * Test that cacheControl() sets the correct header.
	 */
	public function testCacheControl() {
		$this->object->cacheControl('public', 0);
		$this->assertEquals('Cache-Control: public, max-age=0, post-check=0, pre-check=0', $this->object->getHeader('Cache-Control'));

		$this->object->cacheControl('private', 120, ['must-revalidate' => true]);
		$this->assertEquals('Cache-Control: private, must-revalidate, max-age=120, post-check=120, pre-check=0', $this->object->getHeader('Cache-Control'));

		$this->object->cacheControl('no-cache', 0);
		$this->assertEquals('Cache-Control: no-cache, no-store, max-age=0, post-check=0, pre-check=0', $this->object->getHeader('Cache-Control'));
	}

	/**
	 * Test that connection() sets the correct header.
	 */
	public function testConnection() {
		$this->object->connection(true);
		$this->assertEquals('Connection: keep-alive', $this->object->getHeader('Connection'));

		$this->object->connection(false);
		$this->assertEquals('Connection: close', $this->object->getHeader('Connection'));

		$this->object->connection('custom');
		$this->assertEquals('Connection: custom', $this->object->getHeader('Connection'));
	}

	/**
	 * Test that contentDisposition() sets the correct header.
	 */
	public function testContentDisposition() {
		$this->object->contentDisposition('file.png');
		$this->assertEquals('Content-Disposition: attachment; filename="file.png"', $this->object->getHeader('Content-Disposition'));
	}

	/**
	 * Test that contentEncoding() sets the correct header.
	 */
	public function testContentEncoding() {
		$this->object->contentEncoding('gzip');
		$this->assertEquals('Content-Encoding: gzip', $this->object->getHeader('Content-Encoding'));

		$this->object->contentEncoding(['gzip', 'compress']);
		$this->assertEquals('Content-Encoding: gzip, compress', $this->object->getHeader('Content-Encoding'));
	}

	/**
	 * Test that contentLanguage() sets the correct header.
	 */
	public function testContentLanguage() {
		$this->object->contentLanguage('');
		$this->assertEquals('Content-Language: ', $this->object->getHeader('Content-Language'));

		$this->object->contentLanguage('en, en-us');
		$this->assertEquals('Content-Language: en, en-us', $this->object->getHeader('Content-Language'));

		$this->object->contentLanguage(['en', 'en-us']);
		$this->assertEquals('Content-Language: en, en-us', $this->object->getHeader('Content-Language'));

		// g11n
		$this->object->contentLanguage();
		$this->assertEquals('Content-Language: ex-va, ex, no', $this->object->getHeader('Content-Language'));
	}

	/**
	 * Test that contentLength() sets the correct header.
	 */
	public function testContentLength() {
		$this->object->contentLength(1234);
		$this->assertEquals('Content-Length: 1234', $this->object->getHeader('Content-Length'));

		$this->object->contentLength('2GB');
		$this->assertEquals('Content-Length: 2147483648', $this->object->getHeader('Content-Length'));
	}

	/**
	 * Test that contentMD5() sets the correct header.
	 */
	public function testContentMD5() {
		$this->object->body('body')->contentMD5(false)->respond();
		$this->assertEquals('Content-MD5: ', $this->object->getHeader('Content-MD5'));

		$this->object->contentMD5(true)->respond();
		$this->assertEquals('Content-MD5: hBotaJrYa9FhFEdFPCLG/A==', $this->object->getHeader('Content-MD5'));
	}

	/**
	 * Test that contentType() sets the correct header.
	 */
	public function testContentType() {
		$this->object->contentType('html');
		$this->assertEquals('Content-Type: text/html; charset=UTF-8', $this->object->getHeader('Content-Type'));

		$this->object->contentType('application/json');
		$this->assertEquals('Content-Type: application/json', $this->object->getHeader('Content-Type'));

		$this->object->contentType('xhtml');
		$this->assertEquals('Content-Type: application/xhtml+xml', $this->object->getHeader('Content-Type'));

		try {
			$this->object->contentType('fake');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that date() sets the correct header.
	 */
	public function testDate() {
		$this->object->date('+12 hours');
		$this->assertEquals('Date: ' . gmdate(Http::DATE_FORMAT, strtotime('+12 hours')), $this->object->getHeader('Date'));
	}

	/**
	 * Test that etag() sets the correct header.
	 */
	public function testEtag() {
		$this->object->etag('FooBar');
		$this->assertEquals('ETag: "FooBar"', $this->object->getHeader('ETag'));

		$this->object->etag('FooBar', true);
		$this->assertEquals('ETag: W/"FooBar"', $this->object->getHeader('ETag'));
	}

	/**
	 * Test that expires() sets the correct header.
	 */
	public function testExpires() {
		$this->object->expires('+12 hours');
		$this->assertEquals('Expires: ' . gmdate(Http::DATE_FORMAT, strtotime('+12 hours')), $this->object->getHeader('Expires'));
	}

	/**
	 * Test that getCookie() returns defined cookies.
	 */
	public function testGetCookie() {
		$this->assertEquals([], $this->object->getCookie());

		$this->object->setCookie('name', 'value');
		$this->assertEquals([
			'value' => 'value',
			'domain' => '',
			'expires' => '+1 week',
			'path' => '/',
			'secure' => false,
			'httpOnly' => true
		], $this->object->getCookie('name'));

		$this->assertEquals([
			'name' => [
				'value' => 'value',
				'domain' => '',
				'expires' => '+1 week',
				'path' => '/',
				'secure' => false,
				'httpOnly' => true
			]
		], $this->object->getCookie());
	}

	/**
	 * Test that getHeader() returns defined headers.
	 */
	public function testGetHeader() {
		// set by default
		$this->assertEquals([
			'Connection' => 'keep-alive',
			'Content-Language' => 'ex-va, ex, no',
			'Cache-Control' => 'private, must-revalidate, max-age=0, post-check=0, pre-check=0'
		], $this->object->getHeader());

		$this->object->contentType('html');
		$this->assertEquals('Content-Type: text/html; charset=UTF-8', $this->object->getHeader('Content-Type'));

		$this->assertEquals([
			'Connection' => 'keep-alive',
			'Content-Language' => 'ex-va, ex, no',
			'Cache-Control' => 'private, must-revalidate, max-age=0, post-check=0, pre-check=0',
			'Content-Type' => 'text/html; charset=UTF-8'
		], $this->object->getHeader());
	}

	/**
	 * Test that header() sets a header.
	 */
	public function testHeader() {
		$this->object->header('X-Framework', 'Titon');
		$this->assertEquals('X-Framework: Titon', $this->object->getHeader('X-Framework'));
	}

	/**
	 * Test that headers() sets headers.
	 */
	public function testHeaders() {
		$this->object->headers([
			'X-Framework' => 'Titon',
			'X-Version' => '1.2.3'
		]);

		$this->assertEquals('X-Framework: Titon', $this->object->getHeader('X-Framework'));
		$this->assertEquals('X-Version: 1.2.3', $this->object->getHeader('X-Version'));
	}

	/**
	 * Test that lastModified() sets the correct header.
	 */
	public function testLastModified() {
		$this->object->lastModified('+12 hours');
		$this->assertEquals('Last-Modified: ' . gmdate(Http::DATE_FORMAT, strtotime('+12 hours')), $this->object->getHeader('Last-Modified'));
	}

	/**
	 * Test that location() sets the correct header.
	 */
	public function testLocation() {
		$this->object->location('/local/url');
		$this->assertEquals('Location: /local/url', $this->object->getHeader('Location'));

		$this->object->location('http://google.com');
		$this->assertEquals('Location: http://google.com', $this->object->getHeader('Location'));

		$this->object->location(['controller' => 'con', 'module' => 'mod']);
		$this->assertEquals('Location: /mod/con', $this->object->getHeader('Location'));
	}

	/**
	 * Test that noCache() sets the correct headers.
	 */
	public function testNoCache() {
		$this->object->noCache();
		$this->assertEquals('Expires: ' . gmdate(Http::DATE_FORMAT, strtotime('-1 year')), $this->object->getHeader('Expires'));
		$this->assertEquals('Last-Modified: ' . gmdate(Http::DATE_FORMAT), $this->object->getHeader('Last-Modified'));
		$this->assertEquals('Cache-Control: no-cache, must-revalidate, proxy-revalidate, no-store, max-age=0, post-check=0, pre-check=0', $this->object->getHeader('Cache-Control'));
	}

	/**
	 * Test that notModified() sets status and removes headers.
	 */
	public function testNotModified() {
		$this->object->contentType('html')->notModified();
		$this->assertEquals([
			'Connection' => 'keep-alive',
			'Cache-Control' => 'private, must-revalidate, max-age=0, post-check=0, pre-check=0',
			'Status-Code' => '304 Not Modified'
		], $this->object->getHeader());
	}

	/**
	 * Test that body() sets the body and respond() outputs the body.
	 */
	public function testBodyAndRespond() {
		$this->object->body('<html><body>body</body></html>');
		$this->assertEquals('<html><body>body</body></html>', $this->object->respond());
	}

	/**
	 * Test that retryAfter() sets the correct header.
	 */
	public function testRetryAfter() {
		$this->object->retryAfter(120);
		$this->assertEquals('Retry-After: 120', $this->object->getHeader('Retry-After'));

		$this->object->retryAfter('+1 hour');
		$this->assertEquals('Retry-After: ' . gmdate(Http::DATE_FORMAT, strtotime('+1 hour')), $this->object->getHeader('Retry-After'));
	}

	/**
	 * Test that setCookie() sets the correct header.
	 */
	public function testSetCookie() {
		$this->assertEquals(null, $this->object->getCookie('name'));

		$this->object->setCookie('name', 'value');
		$this->assertEquals([
			'value' => 'value',
			'domain' => '',
			'expires' => '+1 week',
			'path' => '/',
			'secure' => false,
			'httpOnly' => true
		], $this->object->getCookie('name'));

		$this->object->setCookie('name', 'value', ['domain' => 'example.com', 'secure' => true]);
		$this->assertEquals([
			'value' => 'value',
			'domain' => 'example.com',
			'expires' => '+1 week',
			'path' => '/',
			'secure' => true,
			'httpOnly' => true
		], $this->object->getCookie('name'));
	}

	/**
	 * Test that statusCode() sets the correct header.
	 */
	public function testStatusCode() {
		$this->object->statusCode(404);
		$this->assertEquals('Status-Code: 404 Not Found', $this->object->getHeader('Status-Code'));

		$this->object->statusCode(500);
		$this->assertEquals('Status-Code: 500 Internal Server Error', $this->object->getHeader('Status-Code'));

		try {
			$this->object->statusCode(666);
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that vary() sets the correct header.
	 */
	public function testVary() {
		$this->object->vary('Accept');
		$this->assertEquals('Vary: Accept', $this->object->getHeader('Vary'));

		$this->object->vary(['Accept', 'Cookie']);
		$this->assertEquals('Vary: Accept, Cookie', $this->object->getHeader('Vary'));
	}

	/**
	 * Test that wwwAuthenticate() sets the correct header.
	 */
	public function testWwwAuthenticate() {
		$this->object->wwwAuthenticate('basic');
		$this->assertEquals('WWW-Authenticate: Basic', $this->object->getHeader('WWW-Authenticate'));

		$this->object->wwwAuthenticate('digest');
		$this->assertEquals('WWW-Authenticate: Digest', $this->object->getHeader('WWW-Authenticate'));

		try {
			$this->object->wwwAuthenticate('custom');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

}