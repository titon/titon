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
use titon\constant\Mime;
use titon\net\NetException;
use titon\libs\traits\Cacheable;
use titon\utility\Hash;

/**
 * The Request object is the primary source of data and state management for the environment.
 * It extracts and cleans the GET, POST and FILES data from the current HTTP request.
 *
 * @package	titon.net
 */
class Request extends Base {
	use Cacheable;

	/**
	 * An combined array of $_POST and $_FILES data for the current request.
	 *
	 * @access public
	 * @var array
	 */
	public $data = [];

	/**
	 * The cleaned $_FILES global.
	 *
	 * @access public
	 * @var array
	 */
	public $files = [];

	/**
	 * The cleaned $_GET global.
	 *
	 * @access public
	 * @var array
	 */
	public $get = [];

	/**
	 * Data that has been generated internally via the framework during the request.
	 *
	 * @access public
	 * @var array
	 */
	public $internal = [];

	/**
	 * The cleaned $_POST global.
	 *
	 * @access public
	 * @var array
	 */
	public $post = [];

	/**
	 * Checks to see if the client accepts a certain content type, based on the Accept header.
	 *
	 * @access public
	 * @param string $type
	 * @return boolean
	 * @throws \titon\net\NetException
	 */
	public function accepts($type = 'html') {
		if (is_array($type)) {
			$contentType = $type;
		} else if (mb_strpos($type, '/') !== false) {
			$contentType = [$type];
		} else {
			$contentType = (array) Mime::getAll($type);
		}

		foreach ($this->_accepts('Accept') as $aType) {
			if (in_array(mb_strtolower($aType['type']), $contentType)) {
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
		if (!$charset) {
			$charset = 'utf-8';
		}

		foreach ($this->_accepts('Accept-Charset') as $set) {
			if (mb_strtolower($charset) === mb_strtolower($set['type'])) {
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
		if (!$language) {
			$language = 'en';
		}

		foreach ($this->_accepts('Accept-Language') as $lang) {
			if (mb_strpos(mb_strtolower($lang['type']), mb_strtolower($language)) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the IP address of the client, the correct way.
	 *
	 * @access public
	 * @param boolean $safe
	 * @return string
	 */
	public function clientIp($safe = true) {
		return $this->cache([__METHOD__, $safe], function() use ($safe) {
			$headers = ['HTTP_CLIENT_IP', 'REMOTE_ADDR'];

			if (!$safe) {
				array_unshift($headers, 'HTTP_X_FORWARDED_FOR');
			}

			$ip = null;

			foreach ($headers as $key) {
				if ($address = $this->env($key)) {
					$ip = $address;
					break;
				}
			}

			if (mb_strpos($ip, ',') !== false) {
				$ip = trim(explode(',', $ip)[0]);
			}

			return $ip;
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
		return $this->cache([__METHOD__, $header], function() use ($header) {
			$headerAlt = 'HTTP_' . mb_strtoupper(str_replace('-', '_', $header));

			foreach ([$_SERVER, $_ENV] as $data) {
				if (isset($data[$header])) {
					return $data[$header];

				} else if (isset($data[$headerAlt])) {
					return $data[$headerAlt];
				}
			}

			return null;
		});
	}

	/**
	 * Return a value from the internal request.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key = null) {
		return Hash::get($this->internal, $key);
	}

	/**
	 * Return true if the key exists in the internal request.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return Hash::has($this->internal, $key);
	}

	/**
	 * Loads the $_POST, $_GET and $_FILES data, configures the query params and populates the accepted headers fields.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		if (isset($_POST['_method'])) {
			$_SERVER['REQUEST_METHOD'] = $_POST['_method'];
			unset($_POST['_method']);
		}

		$this->get = $_GET;
		$this->post = $_POST;

		if ($_FILES) {
			$files = Hash::flatten($_FILES);

			foreach ($files as $key => $value) {
				if (preg_match('/\.(?:name|type|tmp_name|error|size)/', $key, $matches)) {
					$key = str_replace($matches[0], '', $key);
					$key .= $matches[0];
				}

				$this->files = Hash::set($this->files, $key, $value);
			}
		}

		$this->data = Hash::merge($this->post, $this->files);
	}

	/**
	 * Returns true if the page was requested with AJAX.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isAjax() {
		return (mb_strtolower($this->env('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest');
	}

	/**
	 * Returns true if the interface environment is CGI.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isCGI() {
		return (mb_substr(PHP_SAPI, 0, 3) === 'cgi');
	}

	/**
	 * Returns true if the interface environment is CLI (command line).
	 *
	 * @access public
	 * @return boolean
	 */
	public function isCLI() {
		return (mb_substr(PHP_SAPI, 0, 3) === 'cli');
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
		return $this->cache(__METHOD__, function() {
			return (bool) preg_match('/^(Shockwave|Adobe) Flash/', $this->userAgent(false));
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
		return (mb_substr(PHP_SAPI, 0, 5) === 'isapi');
	}

	/**
	 * Primary container function for all method type checking. Returns true if the current request method matches the given argument.
	 *
	 * @access public
	 * @param string $type
	 * @return boolean
	 */
	public function isMethod($type = 'post') {
		return (mb_strtolower($type) === $this->method());
	}

	/**
	 * Returns true if the page was requested with a mobile device.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isMobile() {
		return $this->cache(__METHOD__, function() {
			$mobiles  = 'up\.browser|up\.link|mmp|symbian|smartphone|midp|wap|phone|droid|';
			$mobiles .= 'palmaource|portalmmm|plucker|reqwirelessweb|sonyericsson|windows ce|xiino|';
			$mobiles .= 'iphone|midp|avantgo|blackberry|j2me|opera mini|docoo|netfront|nokia|palmos';

			return (bool) preg_match('/(' . $mobiles . ')/i', $this->userAgent(false));
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
		return $this->cache(__METHOD__, function() {
			$https = mb_strtolower($this->env('HTTPS'));

			if ($https === 'on') {
				$https = true;
			} else if ($https === 'off') {
				$https = false;
			}

			return ($https || $this->env('SERVER_PORT') == 443);
		});
	}

	/**
	 * The current HTTP request method.
	 *
	 * @access public
	 * @return string
	 */
	public function method() {
		return mb_strtolower($this->env('HTTP_X_HTTP_METHOD_OVERRIDE') ?: $this->env('REQUEST_METHOD'));
	}

	/**
	 * Get the current protocol for the current request: HTTP or HTTPS
	 *
	 * @access public
	 * @return string
	 */
	public function protocol() {
		return Titon::router()->segments('scheme');
	}

	/**
	 * Get the referring URL. Will strip the hostname if it comes from the same domain.
	 *
	 * @access public
	 * @return string
	 */
	public function referrer() {
		return $this->cache(__METHOD__, function() {
			$referrer = $this->env('HTTP_REFERER');

			if (!$referrer) {
				return '/';
			}

			$host = $this->env('HTTP_HOST');

			if (mb_strpos($referrer, $host) !== false) {
				$referrer = str_replace($this->protocol() . '://' . $host, '', $referrer);
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
	 * Set a value into the internal request.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return \titon\net\Request
	 */
	public function set($key, $value) {
		$this->internal = Hash::set($this->internal, $key, $value);

		return $this;
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
		return $this->cache([__METHOD__, $explicit], function() use ($explicit) {
			$agent = $this->env('HTTP_USER_AGENT');

			if ($explicit && function_exists('get_browser')) {
				$browser = get_browser($agent, true);

				return [
					'browser' => $browser['browser'],
					'version' => $browser['version'],
					'cookies' => $browser['cookies'],
					'agent' => $agent,
					'os' => $browser['platform']
				];
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
		return $this->cache([__METHOD__, $header], function() use ($header) {
			$data = [];

			if ($accept = explode(',', $this->env($header))) {
				foreach ($accept as $type) {
					$type = str_replace(' ', '', $type);

					if (mb_strpos($type, ';') !== false) {
						list($type, $quality) = explode(';', $type);
					} else {
						$quality = 1;
					}

					$data[] = [
						'type' => $type,
						'quality' => str_replace('q=', '', $quality)
					];
				}
			}

			return $data;
		});
	}

}

