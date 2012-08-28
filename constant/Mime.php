<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\constant;

use titon\constant\ConstantException;
use titon\utility\Hash;

/**
 * MIME type related constants and static variables.
 *
 * @package titon.constant
 */
class Mime {

	/**
	 * Top level types.
	 */
	const APPLICATION = 'application';
	const AUDIO = 'audio';
	const IMAGE = 'image';
	const MULTIPART = 'multipart';
	const TEXT = 'text';
	const VIDEO = 'video';

	/**
	 * Application mime types.
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	public static $applications = [
		'ai'			=> 'application/postscript',
		'amf'			=> 'application/x-amf',
		'atom' 			=> 'application/atom+xml',
		'csv'			=> ['application/csv', 'application/vnd.ms-excel'],
		'doc'			=> 'application/msword',
		'dot'			=> 'application/msword',
		'dtd'			=> 'application/xml-dtd',
		'ecma' 			=> 'application/ecmascript',
		'ecmascript' 	=> 'application/ecmascript',
		'exe'			=> 'application/octet-stream',
		'form'			=> 'application/x-www-form-urlencoded',
		'gz'			=> 'application/x-gzip',
		'gzip'			=> 'application/x-gzip',
		'json'			=> 'application/json',
		'ogg'			=> 'application/ogg',
		'pdf'			=> 'application/pdf',
		'rar'			=> 'application/x-rar-compressed',
		'rdf'			=> 'application/rdf+xml',
		'rtf'			=> 'application/rtf',
		'rss'			=> 'application/rss+xml',
		'soap'			=> 'application/soap+xml',
		'swf'			=> 'application/x-shockwave-flash',
		'tar'			=> 'application/x-tar',
		'ttf'			=> 'application/x-font-ttf',
		'woff'			=> 'application/font-woff',
		'xhtml'			=> ['application/xhtml+xml', 'application/xhtml'],
		'xhtml-mobile'	=> 'application/vnd.wap.xhtml+xml',
		'zip'			=> ['application/zip', 'application/x-zip']
	];

	/**
	 * Audio mime types.
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	public static $audios = [
		'mp3'			=> 'audio/mpeg',
		'mp4'			=> 'audio/mp4',
		'mpeg'			=> 'audio/mpeg',
		'ogg'			=> 'audio/ogg',
		'vorbis'		=> 'audio/vorbis',
		'wav'			=> ['audio/x-wav', 'audio/vnd.wave'],
		'webm'			=> 'audio/webm'
	];

	/**
	 * Image mime types.
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	public static $images = [
		'bmp'			=> 'image/bmp',
		'gif'			=> 'image/gif',
		'jpg'			=> 'image/jpeg',
		'jpe'			=> 'image/jpeg',
		'jpeg'			=> 'image/jpeg',
		'pjpeg'			=> 'image/pjpeg',
		'ico'			=> ['image/x-icon', 'image/vnd.microsoft.icon'],
		'png'			=> 'image/png',
		'svg'			=> 'image/svg+xml',
		'tiff'			=> 'image/tiff'
	];

	/**
	 * Multipart mime types.
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	public static $multiparts = [
		'alternative'	=> 'multipart/alternative',
		'encrypted'		=> 'multipart/encrypted',
		'file'			=> 'multipart/form-data',
		'mixed'			=> 'multipart/mixed',
		'related'		=> 'multipart/related',
		'signed'		=> 'multipart/signed'
	];

	/**
	 * Text mime types.
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	public static $texts = [
		'css'			=> 'text/css',
		'csv'			=> ['text/csv', 'text/plain'],
		'htm'			=> 'text/html',
		'html'			=> ['text/html', '*/*'],
		'javascript'	=> 'text/javascript',
		'text'			=> 'text/plain',
		'txt'			=> 'text/plain',
		'vcard'			=> 'text/vcard',
		'vcf'			=> 'text/x-vcard',
		'xml'			=> 'text/xml'
	];

	/**
	 * Video mime types.
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	public static $videos = [
		'flv'			=> 'video/x-flv',
		'matroska'		=> 'video/x-matroska',
		'mp3'			=> 'video/mpeg',
		'mp4'			=> 'video/mp4',
		'mpeg'			=> 'video/mpeg',
		'mov'			=> 'video/quicktime',
		'ogg'			=> 'video/ogg',
		'qt'			=> 'video/quicktime',
		'webm'			=> 'video/webm',
		'wmv'			=> 'video/x-ms-wmv'
	];

	/**
	 * Get a mime type defined by key or all mime types within a category.
	 *
	 * @access public
	 * @param string $type
	 * @param string $key
	 * @return array|string
	 * @static
	 */
	public static function get($type, $key = null) {
		return Hash::get(self::_detect($type), $key);
	}

	/**
	 * Get all mime types from all categories within a single array. If a mime type shows up in multiple categories, merge the values.
	 *
	 * @access public
	 * @param string $key
	 * @return array|string
	 * @static
	 */
	public static function getAll($key = null) {
		$all = [];

		foreach ([self::$applications, self::$audios, self::$images, self::$multiparts, self::$texts, self::$videos] as $mimeTypes) {
			foreach ($mimeTypes as $ext => $type) {
				$all = self::_merge($all, $ext, $type, false);
			}
		}

		return Hash::get($all, $key);
	}

	/**
	 * Get all mime types from all categories within a single array. Use the mime type as the index and the extension as the value.
	 *
	 * @access pubic
	 * @param string $key
	 * @return array|string
	 * @static
	 */
	public static function getTypeToExt($key = null) {
		$all = [];

		foreach ([self::$applications, self::$audios, self::$images, self::$multiparts, self::$texts, self::$videos] as $mimeTypes) {
			foreach ($mimeTypes as $ext => $types) {
				foreach ((array) $types as $type) {
					$all = self::_merge($all, $type, $ext, false);
				}
			}
		}

		return Hash::get($all, $key);
	}

	/**
	 * Set a custom mime type. If overwrite is false, will merge values with a mime type of the same key.
	 *
	 * @access public
	 * @param string $type
	 * @param string $key
	 * @param mixed $value
	 * @param boolean $overwrite
	 * @return boolean
	 * @static
	 */
	public static function set($type, $key, $value, $overwrite = false) {
		$data = self::_merge(self::_detect($type), $key, $value, $overwrite);

		switch ($type) {
			case self::APPLICATION:		self::$applications = $data; break;
			case self::AUDIO:			self::$audios = $data; break;
			case self::IMAGE:			self::$images = $data; break;
			case self::MULTIPART:		self::$multiparts = $data; break;
			case self::TEXT:			self::$texts = $data; break;
			case self::VIDEO:			self::$videos = $data; break;
		}

		return true;
	}

	/**
	 * Detect which array to return based on the type.
	 *
	 * @access public
	 * @param string $type
	 * @return array
	 * @throws \titon\constant\ConstantException
	 * @static
	 */
	protected static function _detect($type) {
		switch ($type) {
			case self::APPLICATION:		return self::$applications; break;
			case self::AUDIO:			return self::$audios; break;
			case self::IMAGE:			return self::$images; break;
			case self::MULTIPART:		return self::$multiparts; break;
			case self::TEXT:			return self::$texts; break;
			case self::VIDEO:			return self::$videos; break;
			default:
				throw new ConstantException(sprintf('Invalid mime type %s.', $type));
			break;
		}
	}

	/**
	 * Merge the values of multiple arrays dependent on $overwrite.
	 *
	 * @access public
	 * @param array $data
	 * @param string $key
	 * @param mixed $value
	 * @param boolean $overwrite
	 * @return array
	 * @static
	 */
	protected static function _merge($data, $key, $value, $overwrite = false) {
		if ($overwrite) {
			$data[$key] = $value;

			return $data;
		}

		if (isset($data[$key])) {
			$data[$key] = array_merge((array) $data[$key], (array) $value);
		} else {
			$data[$key] = $value;
		}

		return $data;
	}

}