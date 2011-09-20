<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\constant;

/**
 * HTTP related constants and static variables.
 *
 * @package titon.constant
 */
class Http {

	/**
	 * Valid format for HTTP timestamps.
	 */
	const DATE_FORMAT = 'D, d M Y H:i:s T';

	/**
	 * The HTTP 1.0 syntax.
	 */
	const HTTP_10 = 'HTTP/1.0';

	/**
	 * The HTTP 1.1 syntax.
	 */
	const HTTP_11 = 'HTTP/1.1';

	/**
	 * List of content types to parse the request as.
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	public static $contentTypes = array(
		'ai'			=> 'application/postscript',
		'amf'			=> 'application/x-amf',
		'atom'			=> 'application/atom+xml',
		'css'			=> 'text/css',
		'csv'			=> array('application/csv', 'application/vnd.ms-excel', 'text/plain'),
		'file'			=> 'multipart/form-data',
		'form'			=> 'application/x-www-form-urlencoded',
		'htm'			=> 'text/html',
		'html'			=> array('text/html', '*/*'),
		'javascript'	=> 'text/javascript',
		'js'			=> 'text/javascript',
		'json'			=> 'application/json',
		'pdf'			=> 'application/pdf',
		'rss'			=> 'application/rss+xml',
		'swf'			=> 'application/x-shockwave-flash',
		'tar'			=> 'application/x-tar',
		'text'			=> 'text/plain',
		'txt'			=> 'text/plain',
		'vcf'			=> 'text/x-vcard',
		'xhtml'			=> array('application/xhtml+xml', 'application/xhtml', 'text/xhtml'),
		'xhtml-mobile'	=> 'application/vnd.wap.xhtml+xml',
		'xml'			=> array('application/xml', 'text/xml'),
		'zip'			=> 'application/x-zip'
	);

	/**
	 * List of acceptable header types.
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	public static $headerTypes = array(
		'Accept', 
		'Accept-Charset', 
		'Accept-Encoding', 
		'Accept-Language', 
		'Accept-Ranges',
		'Age', 
		'Allow', 
		'Authentication-Info', 
		'Authorization', 
		'Cache-Control', 
		'Connection',
		'Content-Disposition', 
		'Content-Encoding', 
		'Content-Language', 
		'Content-Length',
		'Content-Location', 
		'Content-MD5', 
		'Content-Range', 
		'Content-Type', 
		'Cookie',
		'Date', 
		'ETag', 
		'Expires', 
		'Expect', 
		'From', 
		'Host', 
		'If-Match', 
		'If-Modified-Since',
		'If-None-Match', 
		'If-Unmodified-Since', 
		'If-Range', 
		'Keep-Alive', 
		'Last-Modified',
		'Location', 
		'Max-Forwards', 
		'Pragma', 
		'Proxy-Authenticate', 
		'Proxy-Authorization',
		'Range', 
		'Referer', 
		'Refresh', 
		'Retry-After', 
		'Server', 
		'Set-Cookie', 
		'TE', 
		'Trailer',
		'Transfer-Encoding', 
		'Upgrade', 
		'User-Agent', 
		'Vary', 
		'Via', 
		'Warning', 
		'WWW-Authenticate'
	);

	/**
	 * List of possible method types.
	 *
	 * @access public
	 * @var array
	 */
	public static $methodTypes = array(
		'GET', 
		'POST', 
		'PUT', 
		'DELETE',
		'HEAD', 
		'TRACE', 
		'OPTIONS', 
		'CONNECT'
	);

	/**
	 * List of all available response status codes.
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	public static $statusCodes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Large',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	);
	
	
	/**
	 * Get a single content type, or all content types.
	 *
	 * @access public
	 * @param string $type
	 * @return string|array
	 * @static
	 */
	public static function contentTypes($type = null) {
		return isset(self::$contentTypes[$type]) ? self::$contentTypes[$type] : self::$contentTypes;
	}

	/**
	 * Return all the standard types of HTTP headers.
	 *
	 * @access public
	 * @return array
	 * @static
	 */
	public static function headerTypes() {
		return self::$headerTypes;
	}

	/**
	 * Return all the supported method types.
	 *
	 * @access public
	 * @return array
	 * @static
	 */
	public static function methodTypes() {
		return self::$methodTypes;
	}

	/**
	 * Get a single status code, or all status codes.
	 *
	 * @access public
	 * @param int $code
	 * @return string|array
	 * @static
	 */
	public static function statusCodes($code = null) {
		return isset(self::$statusCodes[$code]) ? self::$statusCodes[$code] : self::$statusCodes;
	}

}