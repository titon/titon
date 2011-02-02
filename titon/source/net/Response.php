<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\net;

use \titon\source\Titon;
use \titon\source\log\Exception;
use \titon\source\net\Http;

/**
 * The Response object handles the collection and output of data to the browser. It stores a list of HTTP headers,
 * the content body, the content type and associated status code to print out.
 *
 * @package	titon.source.net
 * @uses	titon\source\Titon
 */
class Response extends Http {

	/**
	 * Configuration for the Response object.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array('buffer' => 8192);

	/**
	 * The body content to be outputted.
	 *
	 * @access private
	 * @var string
	 */
	private $__body = null;

	/**
	 * Manually defined headers to output in the response.
	 *
	 * @access private
	 * @var array
	 */
	private $__headers = array();

	/**
	 * The content type to output.
	 *
	 * @access private
	 * @var string
	 */
	private $__type = null;

	/**
	 * HTTP status code to output.
	 *
	 * @access private
	 * @var int
	 */
	private $__status = 302;

	/**
	 * Set the content body for the response.
	 *
	 * @access public
	 * @param string $body
	 * @return this
	 * @chainable
	 */
	public function body($body = '') {
		$this->__body = $body;

		return $this;
	}

	/**
	 * Force the clients browser to cache the current request.
	 * 
	 * @access public
	 * @param int|string $expires
	 * @return this
	 * @chainable
	 */
	public function cache($expires = '+24 hours') {
		$expires = is_int($expires) ? $expires : strtotime($expires);

		$this->headers(array(
			'Expires' => gmdate(self::DATE_FORMAT, $expires) .' GMT',
			'Cache-Control' => 'max-age='. ($expires - time())
		));

		return $this;
	}

	/**
	 * Forces the clients browser not to cache the results of the current request.
	 *
	 * @access public
	 * @return this
	 * @chainable
	 */
	public function disableCache() {
		$this->headers(array(
			'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
			'Last-Modified' => gmdate(self::DATE_FORMAT) .' GMT',
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
	 * Sets an HTTP header into a list awaiting to be written in the response.
	 *
	 * @access public
	 * @param string $header
	 * @param string $value
	 * @param boolean $replace
	 * @return this
	 * @chainable
	 */
	public function header($header, $value, $replace = true) {
		$this->__headers[] = array(
			'header'    => $header,
			'value'     => $value,
			'replace'   => $replace
		);

		return $this;
	}

	/**
	 * Pass an array to set multiple headers. Allows for basic support.
	 *
	 * @access public
	 * @param array $headers
	 * @return this
	 * @chainable
	 */
	public function headers(array $headers = array()) {
		if (is_array($headers)) {
			foreach ($headers as $header => $value) {
				if (is_array($value)) {
					foreach ($value as $h => $v) {
						$this->header($h, $v);
					}
				} else {
					$this->header($header, $value);
				}
			}
		}

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
			->header('Location', Titon::router()->build($url))
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
		header(sprintf('%s %s %s',
			self::HTTP_11,
			$this->__status,
			$this->getStatusCode($this->__status)
		));

		// Content type
		if (!empty($this->__type)) {
			header('Content-Type: '. $this->__type);
		}

		// HTTP headers
		if (!empty($this->__headers)) {
			foreach ($this->__headers as $header) {
				header($header['header'] .': '. $header['value'], $header['replace']);
			}
		}

		// Body
		if (!empty($this->__body)) {
			$body = str_split($this->__body, $this->_config['buffer']);

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
	 * @return this
	 * @chainable
	 */
	public function status($code = 302) {
		if (!$this->getStatusCode($code)) {
			throw new Exception(sprintf('The status code %d is not supported.', $code));
		}

		$this->__status = $code;

		return $this;
	}

	/**
	 * Set the content type for the response.
	 *
	 * @access public
	 * @param string $type
	 * @return this
	 * @chainable
	 */
	public function type($type = '') {
		if (strpos($type, '/') === false) {
			$contentType = $this->getContentType($type);

			if ($contentType === null) {
				throw new Exception(sprintf('The content type %s is not supported.', $type));
			}

			if (is_array($contentType)) {
				$type = $contentType[0];
			} else {
				$type = $contentType;
			}
		}

		$this->__type = $type;

		return $this;
	}

}
