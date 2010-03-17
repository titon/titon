<?php
/**
 * @todo
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */
 
namespace titon\http;

use \titon\core\App;
use \titon\http\Http;
use \titon\router\Router;

/**
 * Request Class
 * 
 * @package		Titon
 * @subpackage	Titon.Http
 */
class Request extends Http {

    /**
     * Return full user agent / browser detail. Argument setting for userAgent().
     *
     * @var boolean
     */
    const USERAGENT_FULL = true;

    /**
     * Return basic user agent data (HTTP_USER_AGENT). Argument setting for userAgent().
     *
     * @var boolean
     */
    const USERAGENT_MINIMAL = false;

    /**
	 * An array of $_POST and $_FILES data for the current request, referenced from App::$data.
	 *
	 * @access public
	 * @var array
	 */
	public $data = array();

    /**
     * Named and query params for the current request. Also contains routing information.
     *
     * @access public
     * @var array
     */
    public $params = array();

	/**
	 * The accepted charset types, based on the Accept-Charset header.
	 *
	 * @access private
	 * @var array
	 */
	private $__acceptCharsets = array();

	/**
	 * The accepted language / locale types, based on the Accept-Language header.
	 *
	 * @access private
	 * @var array
	 */
	private $__acceptLangs = array();

	/**
	 * The accepted content types, based on the Accept header.
	 *
	 * @access private
	 * @var array
	 */
	private $__acceptTypes = array();

	/**
	 * Loads the $_POST, $_FILES data, configures the query params and populates the accepted headers fields.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
        if ($this->_initialized) {
            return;
        }

        parent::initialize();

        // Store data
        $this->data =& App::$data;
        $this->params = Router::current();

        // Store accept HTTP headers
		foreach (array('Accept', 'Accept-Language', 'Accept-Charset') as $acception) {
			if ($accept = $this->env($acception)) {
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
						$this->__acceptLangs[] = $data;
					} else if ($acception == 'Accept-Charset') {
						$this->__acceptCharsets[] = $data;
					} else {
						$this->__acceptTypes[] = $data;
					}
				}
			}
		}
	}

	/**
	 * Checks to see if the client accepts a certain content type, based on the Accept header.
	 *
	 * @access public
	 * @param string $type
	 * @return boolean
	 */
	public function accepts($type = 'html') {
		if (!isset($this->_contentTypes[$type])) {
			$type = 'html';
		}

		$contentType = $this->_contentTypes[$type];

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
        $method = $this->env('HTTP_X_HTTP_METHOD_OVERRIDE');

        if (!$method) {
            $method = $this->env('REQUEST_METHOD');
        }

        return (strtolower($type) == strtolower($method));
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
	public function param($key) {
		return (isset($this->params['params'][$key]) ? $this->params['params'][$key] : null);
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
	 * Grabs the value from a named param, parsed from the router.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed|null
	 */
	public function queryParam($key) {
		return (isset($this->params['query'][$key]) ? $this->params['query'][$key] : null);
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
		} else {
            $host = $this->env('HTTP_HOST');
            
			if (strpos($referrer, $host)) {
				$referrer = str_replace($this->protocol() .'://'. $host, '', $referrer);
			}

			return trim($referrer);
		}
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
	 * @link http://php.net/manual/en/function.get-browser.php
	 * @access public
	 * @param boolean $explicit
	 * @return array|string
	 */
	public function userAgent($explicit = self::USERAGENT_FULL) {
		$agent = $this->env('HTTP_USER_AGENT');

		if ($explicit === self::USERAGENT_FULL && function_exists('get_browser')) {
			$browser = get_browser($agent, true);

			return array(
				'browser' => $browser['browser'],
				'version' => $browser['version'],
				'parent'  => $browser['parent'],
				'cookies' => $browser['cookies'],
				'agent' => $agent,
				'os' => $browser['platform']
			);
		} else {
			return $agent;
		}
	}
	
}

