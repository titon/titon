<?php
/**
 * @todo
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\http;

use \titon\http\Http;
use \titon\log\Exception;

/**
 * Response Class
 *
 * @package		Titon
 * @subpackage	Titon.Http
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
     * The content body, content type and status code for the output response.
     *
     * @access private
     * @var array
     */
    private $__response = array(
        'type' => null,
        'body' => null,
        'status' => 302
    );

    /**
     * Manually defined headers to output in the response.
     *
     * @access private
     * @var array
     */
    private $__headers = array();

    /**
     * Set the content body for the response.
     *
     * @access public
     * @param string $body
     * @return object
     */
    public function contentBody($body = '') {
        $this->__response['body'] = $body;
        
        return $this;
    }

    /**
     * Set the content type for the response.
     *
     * @access public
     * @param string $type
     * @return object
     */
    public function contentType($type = '') {
        if (strpos($type, '/') === false) {
            if (!isset($this->_contentTypes[$type])) {
                throw new Exception(sprintf('The content type %s is not supported.', $type));
            }

            if (is_array($this->_contentTypes[$type])) {
                $type = $this->_contentTypes[$type][0];
            } else {
                $type = $this->_contentTypes[$type];
            }
        }

        $this->__response['type'] = $type;

        return $this;
    }

	/**
	 * Forces the clients browser not to cache the results of the current request.
	 *
	 * @access public
	 * @return object
	 */
	public function disableCache() {
		$this->headers(array(
			'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
			'Last-Modified' => gmdate(static::DATE_FORMAT) .' GMT',
			'Cache-Control' => 'no-store, no-cache, must-revalidate',
			'Pragma' => 'no-cache'
		));

		$this->header('Cache-Control', 'post-check=0, pre-check=0', false);

		return $this;
	}

    /**
	 * Sets an HTTP header into a list awaiting to be written in the response.
	 *
	 * @access public
	 * @param string $header
	 * @param string $value
     * @param boolean $replace
	 * @return object
	 */
	public function header($header, $value, $replace = true) {
        $this->headers[] = array(
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
     * @return object
     */
    public function headers(array $headers = array()) {
        if (is_array($headers)) {
			foreach ($headers as $header => $value) {
				$this->header($header, $value);
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
        if (is_array($url)) {
            $url = Router::build($url);
        }
        
		$this->status($code)
			->header('Location', $url, true)
			->contentBody(null)
			->respond();
	}

	/**
	 * Responds to the client by buffering out all the stored HTTP headers.
	 *
	 * @access public
	 * @return void
	 */
	public function respond() {
        header(sprintf('%s %s %s', static::HTTP_11, $this->__response['status'], $this->_statusCodes[$this->__response['status']]));
        
        // Content type
        if (!empty($this->__response['type'])) {
            header('Content-Type: '. $this->__response['type']);
        }

        // HTTP headers
        if (!empty($this->__headers)) {
            foreach ($this->__headers as $header) {
                header($header['header'] .': '. $header['value'], $header['replace']);
            }
        }

        // Body
        if (!empty($this->__response['body'])) {
            $body = str_split($this->__response['body'], $this->_config['buffer']);

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
     * @return object
     */
    public function status($code) {
        if (is_numeric($code)) {
            if (!isset($this->_statusCodes[$code])) {
                throw new Exception(sprintf('The status code %d is not supported.', $code));
            }
            
            $this->__response['status'] = $code;
        }
        
        return $this;
    }

}
