<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */
 
namespace titon\source\net;

use \titon\source\log\Exception;
use \titon\source\net\Http;

/**
 * The Request object is the primary source of data and state management for the environment.
 * It extracts and cleans the GET, POST and FILES data from the current HTTP request.
 * 
 * @package	titon.source.net
 */
class Request extends Http {

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
	 * Named and query params for the current request.
	 *
	 * @access public
	 * @var array
	 */
	public $query = array();

	/**
	 * Request configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array('extract' => true);

	/**
	 * The accepted charset types, based on the Accept-Charset header.
	 *
	 * @access private
	 * @var array
	 */
	private $__charsets = array();

	/**
	 * The accepted language / locale types, based on the Accept-Language header.
	 *
	 * @access private
	 * @var array
	 */
	private $__locales = array();

	/**
	 * The current HTTP method used.
	 *
	 * @access private
	 * @var string
	 */
	private $__method = 'get';

	/**
	 * The accepted content types, based on the Accept header.
	 *
	 * @access private
	 * @var array
	 */
	private $__types = array();

	/**
	 * Loads the $_POST, $_FILES data, configures the query params and populates the accepted headers fields.
	 *
	 * @access public
	 * @return void
	 */
	public function construct() {
		parent::construct();

		$get = $_GET;
		$post = $_POST;
		$files = array();

		if (!empty($_FILES)) {
			foreach ($_FILES as $model => $data) {
				foreach ($data as $meta => $values) {
					$keys = array_keys($values);
					$files[$model][$keys[0]][$meta] = $values[$keys[0]];
				}
			}
		}

		// Clear magic quotes, just in case
		if (get_magic_quotes_gpc() > 0) {
			$stripSlashes = function($data) {
				return is_array($data) ? array_map($stripSlashes, $data) : stripslashes($data);
			};

			$get = $stripSlashes($get);
			$post = $stripSlashes($post);
			$files = $stripSlashes($files);
		}

		// Store into the class
		$this->data = array_merge_recursive($post, $files);
		$this->files = $files;
		$this->get = $get;
		$this->post = $post;
		$this->query = $app->router->current('query');

		// Store accept HTTP headers
		if ($this->_config['extract']) {
			foreach (array('Accept', 'Accept-Language', 'Accept-Charset') as $acception) {
				$accept = $this->env($acception);

				if ($accept !== null) {
					$accept = explode(',', $accept);

					foreach ($accept as $type) {
						if (strpos($type, ';') !== false) {
							list($type, $quality) = explode(';', $type);
						} else {
							$quality = 1;
						}

						$data = array(
							'type' => $type,
							'quality' => str_replace('q=', '', $quality)
						);

						if ($acception == 'Accept-Language') {
							$this->__locales[] = $data;
						} else if ($acception == 'Accept-Charset') {
							$this->__charsets[] = $data;
						} else {
							$this->__types[] = $data;
						}
					}
				}
			}
		}

		// Get method
		$this->__method = strtolower($this->env('HTTP_X_HTTP_METHOD_OVERRIDE') ?: $this->env('REQUEST_METHOD'));
	}

	/**
	 * Checks to see if the client accepts a certain content type, based on the Accept header.
	 *
	 * @access public
	 * @param string $type
	 * @return boolean
	 */
	public function accepts($type = 'html') {
		$contentType = $this->getContentType($type);

		if ($contentType === null) {
			throw new Exception(sprintf('The content type %s is not supported.', $type));
		}

		if (!is_array($contentType)) {
			$contentType = array($contentType);
		}

		foreach ($this->__acceptTypes as $aType) {
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

		foreach ($this->__acceptCharsets as $set) {
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
	public function acceptsLang($language = 'en') {
		if (empty($language)) {
			$language = 'en';
		}

		foreach ($this->__acceptLangs as $lang) {
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
		if ($address = $this->env('HTTP_CLIENT_IP')) {
			return $address;

		} else if ($address = $this->env('HTTP_X_FORWARDED_FOR')) {
			return $address;

		} else {
			return $this->env('REMOTE_ADDR');
		}
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
		return (bool)preg_match('/^(Shockwave|Adobe) Flash/', $this->userAgent(self::USERAGENT_MINIMAL));
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
		return (strtolower($type) == $this->__method);
	}

	/**
	 * Returns true if the page was requested with a mobile device.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isMobile() {
		$mobiles  = 'up\.browser|up\.link|mmp|symbian|smartphone|midp|wap|phone|';
		$mobiles .= 'palmaource|portalmmm|plucker|reqwirelessweb|sonyericsson|windows ce|xiino|';
		$mobiles .= 'iphone|midp|avantgo|blackberry|j2me|opera mini|docoo|netfront|nokia|palmos';

		return (bool)preg_match('/('. $mobiles .')/i', $this->userAgent(self::USERAGENT_MINIMAL));
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
	 * Returns true if the page was requested through SSL.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isSSL() {
		return $this->env('HTTPS');
	}

	/**
	 * Grabs the value from a named param, parsed from the router.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed|null
	 */
	public function param($key, $default = null) {
		return $this->query[$key] ?: $default;
	}

	/**
	 * Get the current protocol for the current request: HTTP or HTTPS
	 *
	 * @access public
	 * @return string
	 */
	public function protocol() {
		return (strtolower($this->env('HTTPS')) == 'on' ? 'https' : 'http');
	}

	/**
	 * Get the referring URL. Will strip the hostname if it comes from the same domain.
	 *
	 * @access public
	 * @return string
	 */
	public function referrer() {
		$referrer = $this->env('HTTP_REFERER');

		if (empty($referrer)) {
			return;
		}

		$host = $this->env('HTTP_HOST');

		if (strpos($referrer, $host) != false) {
			$referrer = str_replace($this->protocol() .'://'. $host, '', $referrer);
		}

		return trim($referrer);
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
	 * @access public
	 * @param boolean $explicit
	 * @return array|string
	 */
	public function userAgent($explicit = true) {
		$agent = $this->env('HTTP_USER_AGENT');

		if ($explicit === true && function_exists('get_browser')) {
			$browser = get_browser($agent, true);

			return array(
				'browser' => $browser['browser'],
				'version' => $browser['version'],
				'parent'  => $browser['parent'],
				'cookies' => $browser['cookies'],
				'agent' => $agent,
				'os' => $browser['platform']
			);
		}

		return $agent;
	}

}

