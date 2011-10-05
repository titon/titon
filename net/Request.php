<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */
 
namespace titon\net;

use \titon\Titon;
use \titon\base\Base;
use \titon\constant\Http;
use \titon\net\NetException;

/**
 * The Request object is the primary source of data and state management for the environment.
 * It extracts and cleans the GET, POST and FILES data from the current HTTP request.
 * 
 * @package	titon.net
 * @uses	titon\Titon
 * @uses	titon\constant\Http
 * @uses	titon\net\NetException
 */
class Request extends Base {

	/**
	 * An combined array of $_POST and $_FILES data for the current request.
	 *
	 * @access public
	 * @var array
	 */
	public $data = array();

	/**
	 * The cleaned $_FILES global.
	 *
	 * @access public
	 * @var array
	 */
	public $files = array();

	/**
	 * The cleaned $_GET global.
	 *
	 * @access public
	 * @var array
	 */
	public $get = array();

	/**
	 * The cleaned $_POST global.
	 *
	 * @access public
	 * @var array
	 */
	public $post = array();

	/**
	 * The current HTTP method used.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_method = 'get';

	/**
	 * Checks to see if the client accepts a certain content type, based on the Accept header.
	 *
	 * @access public
	 * @param string $type
	 * @return boolean
	 * @throws NetException
	 */
	public function accepts($type = 'html') {
		$contentType = Http::contentTypes($type);

		if ($contentType === null) {
			throw new NetException(sprintf('The content type %s is not supported.', $type));
		}

		if (!is_array($contentType)) {
			$contentType = array($contentType);
		}

		foreach ($this->_accepts('Accept') as $aType) {
			if (in_array(strtolower($aType['type']), $contentType)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks to see if the client accepts a certain charset, based on the Accept-Charset header.
	 *
	 * @access public
	 * @param string $charset
	 * @return boolean
	 */
	public function acceptsCharset($charset = 'utf-8') {
		if (empty($charset)) {
			$charset = 'utf-8';
		}

		foreach ($this->_accepts('Accept-Charset') as $set) {
			if (strtolower($charset) == strtolower($set['type'])) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks to see if the client accepts a certain charset, based on the Accept-Lang header.
	 * Will verify a partial match, example en-us will match en.
	 *
	 * @access public
	 * @param string $language
	 * @return boolean
	 */
	public function acceptsLanguage($language = 'en') {
		if (empty($language)) {
			$language = 'en';
		}

		foreach ($this->_accepts('Accept-Language') as $lang) {
			if (strpos(strtolower($lang['type']), strtolower($language)) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the IP address of the client, the correct way.
	 *
	 * @access public
	 * @return string
	 */
	public function clientIp() {
		return $this->lazyLoad(__FUNCTION__, function($self) {
			foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR') as $key) {
				if (($address = $self->env($key)) != null) {
					return $address;
				}
			}

			return null;
		});
	}
	
	/**
	 * Get the value of a header by searching through the HTTP headers, $_SERVER and $_ENV globals.
	 *
	 * @access public
	 * @param string $header
	 * @return string
	 */
	public function env($header) {
		$headerAlt = 'HTTP_' . strtoupper(str_replace('-', '_', $header));

		foreach (array($_SERVER, $_ENV) as $data) {
			if (isset($data[$header])) {
				return $data[$header];

			} else if (isset($data[$headerAlt])) {
				return $data[$headerAlt];
			}
		}

		return null;
	}

	/**
	 * Loads the $_POST, $_FILES data, configures the query params and populates the accepted headers fields.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$get = $_GET;
		$post = $_POST;
		$files = array();

		if (!empty($_FILES)) {
			foreach ($_FILES as $model => $data) {
				foreach ($data as $key => $values) {
					$keys = array_keys($values);
					$files[$model][$keys[0]][$key] = $values[$keys[0]];
				}
			}
		}

		// Clear magic quotes, just in case
		if (get_magic_quotes_gpc() > 0) {
			$stripSlashes = function($data) {
				return is_array($data) ? array_map($stripSlashes, $data) : filter_var($data, FILTER_SANITIZE_MAGIC_QUOTES);
			};

			$get = $stripSlashes($get);
			$post = $stripSlashes($post);
			$files = $stripSlashes($files);
		}

		$this->data = array_merge_recursive($post, $files);
		$this->files = $files;
		$this->get = $get;
		$this->post = $post;
		$this->_method = strtolower($this->env('HTTP_X_HTTP_METHOD_OVERRIDE') ?: $this->env('REQUEST_METHOD'));
	}

	/**
	 * Returns true if the page was requested with AJAX.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isAjax() {
		return (strtolower($this->env('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest');
	}

	/**
	 * Returns true if the interface environment is CGI.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isCGI() {
		return (substr(PHP_SAPI, 0, 3) === 'cgi');
	}

	/**
	 * Returns true if the interface environment is CLI (command line).
	 *
	 * @access public
	 * @return boolean
	 */
	public function isCLI() {
		return (substr(PHP_SAPI, 0, 3) === 'cli');
	}

	/**
	 * Returns true if the page was requested with a DELETE method.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isDelete() {
		return $this->isMethod('delete');
	}

	/**
	 * Returns true if the page was requested through Flash.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isFlash() {
		return $this->lazyLoad(__FUNCTION__, function($self) {
			return (bool) preg_match('/^(Shockwave|Adobe) Flash/', $self->userAgent(false));
		});
	}

	/**
	 * Returns true if the page was requested with a GET method.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isGet() {
		return $this->isMethod('get');
	}

	/**
	 * Returns true if the interface environment is IIS.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isIIS() {
		return (substr(PHP_SAPI, 0, 5) === 'isapi');
	}

	/**
	 * Primary container function for all method type checking. Returns true if the current request method matches the given argument.
	 *
	 * @access public
	 * @param string $type
	 * @return boolean
	 */
	public function isMethod($type = 'post') {
		return (strtolower($type) == $this->method());
	}

	/**
	 * Returns true if the page was requested with a mobile device.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isMobile() {
		return $this->lazyLoad(__FUNCTION__, function($self) {
			$mobiles  = 'up\.browser|up\.link|mmp|symbian|smartphone|midp|wap|phone|';
			$mobiles .= 'palmaource|portalmmm|plucker|reqwirelessweb|sonyericsson|windows ce|xiino|';
			$mobiles .= 'iphone|midp|avantgo|blackberry|j2me|opera mini|docoo|netfront|nokia|palmos';

			return (bool) preg_match('/('. $mobiles .')/i', $self->userAgent(false));
		});
	}

	/**
	 * Returns true if the page was requested with a POST method.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isPost() {
		return $this->isMethod('post');
	}

	/**
	 * Returns true if the page was requested with a PUT method.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isPut() {
		return $this->isMethod('put');
	}

	/**
	 * Is the page being requested through a secure connection.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isSecure() {
		return $this->lazyLoad(__FUNCTION__, function($self) {
			return ($self->env('HTTPS') == 'on' || $self->env('SERVER_PORT') == 443);
		});
	}

	/**
	 * The current HTTP request method.
	 *
	 * @access public
	 * @return string
	 */
	public function method() {
		return $this->_method;
	}

	/**
	 * Get the current protocol for the current request: HTTP or HTTPS
	 *
	 * @access public
	 * @return string
	 */
	public function protocol() {
		return Titon::router()->segment('scheme');
	}

	/**
	 * Get the referring URL. Will strip the hostname if it comes from the same domain.
	 *
	 * @access public
	 * @return string
	 */
	public function referrer() {
		return $this->lazyLoad(__FUNCTION__, function($self) {
			$referrer = $self->env('HTTP_REFERER');

			if (empty($referrer)) {
				return;
			}

			$host = $self->env('HTTP_HOST');

			if (strpos($referrer, $host) !== false) {
				$referrer = str_replace($self->protocol() .'://'. $host, '', $referrer);
			}

			return trim($referrer);
		});
	}

	/**
	 * Get the IP address of the current server.
	 *
	 * @access public
	 * @return string
	 */
	public function serverIp() {
		return $this->env('SERVER_ADDR');
	}

	/**
	 * Grabs information about the browser, os and other client information.
	 * Must have browscap installed for $explicit use.
	 *
	 * @link http://php.net/get_browser
	 * @link http://php.net/manual/misc.configuration.php#ini.browscap
	 * 
	 * @access public
	 * @param boolean $explicit
	 * @return array|string
	 */
	public function userAgent($explicit = false) {
		return $this->lazyLoad(__FUNCTION__ . $explicit, function($self) use ($explicit) {
			$agent = $self->env('HTTP_USER_AGENT');

			if ($explicit && function_exists('get_browser')) {
				$browser = get_browser($agent, true);

				return array(
					'browser' => $browser['browser'],
					'version' => $browser['version'],
					'cookies' => $browser['cookies'],
					'agent' => $agent,
					'os' => $browser['platform']
				);
			}

			return $agent;
		});
	}
	
	/**
	 * Lazy loading functionality for extracting Accept header information and parsing it.
	 * 
	 * @access protected
	 * @param string $header
	 * @return array
	 */
	protected function _accepts($header) {
		return $this->lazyLoad(__FUNCTION__ . ':' . $header, function($self) use ($header) {
			$accept = explode(',', $self->env($header));
			$data = array();

			if (count($accept) > 0) {
				foreach ($accept as $type) {
					if (strpos($type, ';') !== false) {
						list($type, $quality) = explode(';', $type);
					} else {
						$quality = 1;
					}

					$data[] = array(
						'type' => $type,
						'quality' => str_replace('q=', '', $quality)
					);
				}
			}
			
			return $data;
		});
	}

}

