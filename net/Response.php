<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\net;

use titon\Titon;
use titon\base\Base;
use titon\constant\Http;
use titon\net\NetException;
use titon\utility\Number;
use titon\utility\Hash;
use titon\utility\String;
use titon\utility\Time;

/**
 * The Response object handles the collection and output of data to the browser. It stores a list of HTTP headers,
 * the content body, the content type and associated status code to print out.
 *
 * @package	titon.net
 */
class Response extends Base {

	/**
	 * Configuration.
	 *
	 *	buffer	- The range in which to break up the body into chunks.
	 * 	md5		- When enabled, will add a Content-MD5 header
	 * 	debug	- When enabled, will return the response as a string instead of outputting.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'buffer' => 8192,
		'md5' => true,
		'debug' => false
	];

	/**
	 * The body content to be outputted.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_body = null;

	/**
	 * List of cookies to set for this response.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_cookies = [];

	/**
	 * Manually defined headers to output in the response.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_headers = [];

	/**
	 * HTTP status code to output.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_status = 302;

	/**
	 * Set the Accept-Ranges header.
	 *
	 * @access public
	 * @param string|int $range
	 * @return titon\net\Response
	 * @chainable
	 */
	public function acceptRanges($range) {
		if (!is_numeric($range) && $range !== 'none') {
			$range = Number::bytesFrom($range);
		}

		$this->header('Accept-Ranges', $range);

		return $this;
	}

	/**
	 * Set the Age header.
	 *
	 * @access public
	 * @return titon\net\Response
	 * @chainable
	 */
	public function age() {
		// @todo
		return $this;
	}

	/**
	 * Set the Allow header.
	 *
	 * @access public
	 * @param string|array $methods
	 * @return titon\net\Response
	 * @chainable
	 */
	public function allow($methods) {
		if (is_array($methods)) {
			$methods = implode(', ', $methods);
		}

		$this->header('Allow', mb_strtoupper($methods));

		return $this;
	}

	/**
	 * Set the Authorization header.
	 *
	 * @access public
	 * @return titon\net\Response
	 * @chainable
	 */
	public function authorization() {
		// @todo
		return $this;
	}

	/**
	 * Set the content body for the response.
	 *
	 * @access public
	 * @param string $body
	 * @return titon\net\Response
	 * @chainable
	 */
	public function body($body = null) {
		$this->_body = $body;

		return $this;
	}

	/**
	 * Force the clients browser to cache the current request.
	 *
	 * @access public
	 * @param int|string $expires
	 * @return titon\net\Response
	 * @chainable
	 */
	public function cache($expires = '+24 hours') {
		$this->expires($expires)->cacheControl('private', $expires);

		return $this;
	}

	/**
	 * Set the Cache-Control header.
	 *
	 * @access public
	 * @param string $scope
	 * @param int $time
	 * @param array $options
	 * @return titon\net\Response
	 * @chainable
	 */
	public function cacheControl($scope, $time = 0, array $options = []) {
		if (is_string($time)) {
			$time = Time::toUnix($time) - time();
		}

		$options = $options + [
			'no-store' => false,
			'no-transform' => false,
			'must-revalidate' => false,
			'proxy-ravalidate' => false,
			'max-age' => false,
			's-maxage' => false,
			'post-check' => 0,
			'pre-check' => 0
		];

		if ($time >= 0) {
			$options['max-age'] = $time;
			$options['post-check'] = $time;
		}

		if ($scope === 'no-cache') {
			$options['no-store'] = true;

			$this->header('Pragma', 'no-cache');
		}

		$header = $scope;

		foreach ($options as $key => $value) {
			if ($value !== false) {
				$header .= sprintf(($value === true ? ', %s' : ', %s=%s'), $key, $value);
			}
		}

		$this->header('Cache-Control', $header);

		return $this;
	}

	/**
	 * Set the Connection header.
	 *
	 * @access public
	 * @param boolean $status
	 * @return titon\net\Response
	 * @chainable
	 */
	public function connection($status) {
		if ($status === true) {
			$status = 'keep-alive';
		} else if ($status === false) {
			$status = 'close';
		}

		$this->header('Connection', $status);

		return $this;
	}

	/**
	 * Set the Content-Encoding header.
	 *
	 * @access public
	 * @param string|array $encoding
	 * @return titon\net\Response
	 * @chainable
	 */
	public function contentEncoding($encoding) {
		if (is_array($encoding)) {
			$encoding = implode(', ', $encoding);
		}

		$this->header('Content-Encoding', $encoding);

		return $this;
	}

	/**
	 * Set the Content-Language header. Attempt to use the locales set in G11n.
	 *
	 * @access public
	 * @param string|array $lang
	 * @return titon\net\Response
	 * @chainable
	 */
	public function contentLanguage($lang = null) {
		$g11n = Titon::g11n();

		if (empty($lang) && $g11n->isEnabled()) {
			$locales = $g11n->listing();
			array_unshift($locales, $g11n->current()->getLocale('key'));

			$lang = array_unique($locales);
		}

		if (is_array($lang)) {
			$lang = implode(', ', $lang);
		}

		$this->header('Content-Language', $lang);

		return $this;
	}

	/**
	 * Set the Content-Length header.
	 *
	 * @access public
	 * @param string|int $length
	 * @return titon\net\Response
	 * @chainable
	 */
	public function contentLength($length) {
		if (!is_numeric($length)) {
			$length = Number::bytesFrom($length);
		}

		$this->header('Content-Length', $length);

		return $this;
	}

	/**
	 * Set the Content-MD5 header.
	 *
	 * @access public
	 * @param boolean $enabled
	 * @return titon\net\Response
	 * @chainable
	 */
	public function contentMD5($enabled) {
		$this->config->md5 = (bool) $enabled;

		return $this;
	}

	/**
	 * Set the Content-Range header.
	 *
	 * @access public
	 * @return titon\net\Response
	 * @chainable
	 */
	public function contentRange() {
		// @todo
		return $this;
	}

	/**
	 * Set the Content-Type header.
	 *
	 * @access public
	 * @param string $type
	 * @return titon\net\Response
	 * @chainable
	 */
	public function contentType($type) {
		if (mb_strpos($type, '/') === false) {
			$contentType = Http::getContentType($type);

			if (is_array($contentType)) {
				$type = $contentType[0];
			} else {
				$type = $contentType;
			}
		}

		if (String::startsWith($type, 'text/')) {
			$type .= '; charset=' . Titon::config()->encoding();
		}

		$this->header('Content-Type', $type);

		return $this;
	}

	/**
	 * Set the Date header.
	 *
	 * @access public
	 * @param string|int $time
	 * @return titon\net\Response
	 * @chainable
	 */
	public function date($time) {
		$this->header('Date', gmdate(Http::DATE_FORMAT, Time::toUnix($time)) . ' GMT');

		return $this;
	}

	/**
	 * Set the ETag header.
	 *
	 * @access public
	 * @return titon\net\Response
	 * @chainable
	 */
	public function etag() {
		// @todo
		return $this;
	}

	/**
	 * Set the Expires header.
	 *
	 * @access public
	 * @param string|int $expires
	 * @return titon\net\Response
	 * @chainable
	 */
	public function expires($expires = '+24 hours') {
		$this->header('Expires', gmdate(Http::DATE_FORMAT, Time::toUnix($expires)) . ' GMT');

		return $this;
	}

	/**
	 * Return a defined cookie or all cookies.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function getCookie($key = null) {
		return Hash::get($this->_cookies, $key);
	}

	/**
	 * Return all defined headers.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function getHeader($key = null) {
		return Hash::get($this->_headers, $key);
	}

	/**
	 * Add an HTTP header into the list awaiting to be written in the response.
	 *
	 * @access public
	 * @param string $header
	 * @param string $value
	 * @return titon\net\Response
	 * @chainable
	 */
	public function header($header, $value) {
		$this->_headers[$header][] = $value;

		return $this;
	}

	/**
	 * Pass an array to set multiple headers. Allows for basic support.
	 *
	 * @access public
	 * @param array $headers
	 * @return titon\net\Response
	 * @chainable
	 */
	public function headers(array $headers = []) {
		if (is_array($headers)) {
			foreach ($headers as $header => $value) {
				if (is_array($value)) {
					foreach ($value as $v) {
						$this->header($header, $v);
					}
				} else {
					$this->header($header, $value);
				}
			}
		}

		return $this;
	}

	/**
	 * Set default headers.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this
			->cacheControl('private', 0, ['must-revalidate' => true])
			->connection(true)
			->contentLanguage();
	}

	/**
	 * Set the Last-Modified header.
	 *
	 * @access public
	 * @param mixed $time
	 * @return titon\net\Response
	 * @chainable
	 */
	public function lastModified($time = null) {
		$this->header('Last-Modified', gmdate(Http::DATE_FORMAT, Time::toUnix($time)) . ' GMT');

		return $this;
	}

	/**
	 * Forces the clients browser not to cache the results of the current request.
	 *
	 * @access public
	 * @return titon\net\Response
	 * @chainable
	 */
	public function noCache() {
		$this->expires('-1 year')->lastModified()->cacheControl('no-cache', 0, [
			'must-revalidate' => true,
			'proxy-revalidate' => true
		]);

		return $this;
	}

	/**
	 * Redirect to another URL with an HTTP header. Can pass along an HTTP status code.
	 *
	 * @access public
	 * @param array $url
	 * @param int $code
	 * @return void
	 */
	public function redirect($url, $code = 302) {
		$this->status($code)
			->location(Titon::router()->detect($url))
			->body(null)
			->respond();

		exit();
	}

	/**
	 * Responds to the client by buffering out all the stored HTTP headers.
	 *
	 * @access public
	 * @return void
	 */
	public function respond() {
		header(sprintf('%s %d %s',
			Http::HTTP_11,
			$this->_status,
			Http::getStatusCode($this->_status)
		));

		// Create an MD5 digest?
		if ($this->config->md5) {
			$this->header('Content-MD5', base64_encode(pack('H*', md5($this->_body))));
		}

		// HTTP headers
		if (!empty($this->_headers)) {
			foreach ($this->_headers as $header) {
				header($header['header'] . ': ' . $header['value'], $header['replace']);
			}
		}

		// Cookie headers
		if (!empty($this->_cookies)) {
			foreach ($this->_cookies as $key => $cookie) {
				setcookie($key, $cookie['value'], $cookie['expires'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httpOnly']);
			}
		}

		// Body
		if (!empty($this->_body)) {
			$body = str_split($this->_body, $this->config->buffer);

			foreach ($body as $chunk) {
				echo $chunk;
			}
		}
	}

	/**
	 * Set a cookie into the response.
	 *
	 * @access public
	 * @param string $key
	 * @param string $value
	 * @param array $config
	 * @return titon\net\Response
	 * @chainable
	 */
	public function setCookie($key, $value, array $config) {
		$this->_cookies[$key] = $config + [
			'value' => $value,
			'domain' => '',
			'expires' => '+1 week',
			'path' => '/',
			'secure' => false,
			'httpOnly' => true
		];

		return $this;
	}

	/**
	 * Set the status code to use for the current response.
	 *
	 * @access public
	 * @param int $code
	 * @return titon\net\Response
	 * @chainable
	 */
	public function status($code = 302) {
		$this->_status = $code;

		return $this;
	}

}
