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

/**
 * The Response object handles the collection and output of data to the browser. It stores a list of HTTP headers,
 * the content body, the content type and associated status code to print out.
 *
 * @package	titon.net
 * @uses	titon\Titon
 * @uses	titon\constant\Http
 * @uses	titon\net\NetException
 */
class Response extends Base {

	/**
	 * Configuration.
	 *
	 *	buffer - The range in which to break up the body into chunks.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'buffer' => 8192
	);

	/**
	 * The body content to be outputted.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_body = null;

	/**
	 * Manually defined headers to output in the response.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_headers = array();

	/**
	 * The content type to output.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_type = null;

	/**
	 * HTTP status code to output.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_status = 302;

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
		$expires = is_int($expires) ? $expires : strtotime($expires);

		$this->headers(array(
			'Expires' => gmdate(Http::DATE_FORMAT, $expires) . ' GMT',
			'Cache-Control' => 'max-age=' . ($expires - time())
		));

		return $this;
	}

	/**
	 * Add an HTTP header into the list awaiting to be written in the response.
	 *
	 * @access public
	 * @param string $header
	 * @param string $value
	 * @param boolean $replace
	 * @return titon\net\Response
	 * @chainable
	 */
	public function header($header, $value, $replace = true) {
		$this->_headers[] = array(
			'header' => $header,
			'value' => $value,
			'replace' => $replace
		);

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
	public function headers(array $headers = array()) {
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
	 * Forces the clients browser not to cache the results of the current request.
	 *
	 * @access public
	 * @return titon\net\Response
	 * @chainable
	 */
	public function noCache() {
		$this->headers(array(
			'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
			'Last-Modified' => gmdate(Http::DATE_FORMAT) . ' GMT',
			'Cache-Control' => array(
				'no-store, no-cache, must-revalidate',
				'post-check=0, pre-check=0',
				'max-age=0'
			),
			'Pragma' => 'no-cache'
		));

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
			->header('Location', Titon::router()->detect($url))
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

		// Content type
		if (!empty($this->_type)) {
			header('Content-Type: ' . $this->_type);
		}

		// HTTP headers
		if (!empty($this->_headers)) {
			foreach ($this->_headers as $header) {
				header($header['header'] . ': ' . $header['value'], $header['replace']);
			}
		}

		// Body
		if (!empty($this->_body)) {
			$body = str_split($this->_body, $this->config('buffer'));

			foreach ($body as $chunk) {
				echo $chunk;
			}
		}
	}

	/**
	 * Set the status code to use for the current response.
	 *
	 * @access public
	 * @param int $code
	 * @return titon\net\Response
	 * @throws titon\net\NetException
	 * @chainable
	 */
	public function status($code = 302) {
		$this->_status = $code;

		return $this;
	}

	/**
	 * Set the content type for the response.
	 *
	 * @access public
	 * @param string $type
	 * @return titon\net\Response
	 * @chainable
	 */
	public function type($type = null) {
		if (strpos($type, '/') === false) {
			$contentType = Http::getContentType($type);

			if (is_array($contentType)) {
				$type = $contentType[0];
			} else {
				$type = $contentType;
			}
		}

		$this->_type = $type;

		return $this;
	}

}
