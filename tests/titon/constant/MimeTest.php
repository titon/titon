<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\constant;

use titon\Titon;
use titon\constant\Mime;
use titon\tests\TestCase;
use \Exception;

/**
 * Test class for titon\constant\Mime.
 */
class MimeTest extends TestCase {

	/**
	 * Test that get() returns all mime types by category, or a single type.
	 */
	public function testGet() {
		$this->assertEquals(Mime::$applications, Mime::get(Mime::APPLICATION));
		$this->assertEquals(Mime::$audios, Mime::get(Mime::AUDIO));
		$this->assertEquals(Mime::$images, Mime::get(Mime::IMAGE));
		$this->assertEquals(Mime::$multiparts, Mime::get(Mime::MULTIPART));
		$this->assertEquals(Mime::$texts, Mime::get(Mime::TEXT));
		$this->assertEquals(Mime::$videos, Mime::get(Mime::VIDEO));

		// By key
		$this->assertEquals('application/pdf', Mime::get(Mime::APPLICATION, 'pdf'));
		$this->assertEquals('audio/ogg', Mime::get(Mime::AUDIO, 'ogg'));
		$this->assertEquals('image/png', Mime::get(Mime::IMAGE, 'png'));
		$this->assertEquals('multipart/form-data', Mime::get(Mime::MULTIPART, 'file'));
		$this->assertEquals(['text/html', '*/*'], Mime::get(Mime::TEXT, 'html'));
		$this->assertEquals('video/quicktime', Mime::get(Mime::VIDEO, 'mov'));

		// Non-existent key
		$this->assertEquals(null, Mime::get(Mime::APPLICATION, 'html'));
		$this->assertEquals(null, Mime::get(Mime::AUDIO, 'jpeg'));
		$this->assertEquals(null, Mime::get(Mime::IMAGE, 'zip'));
		$this->assertEquals(null, Mime::get(Mime::MULTIPART, 'xml'));
		$this->assertEquals(null, Mime::get(Mime::TEXT, 'mp4'));
		$this->assertEquals(null, Mime::get(Mime::VIDEO, 'wav'));
	}

	/**
	 * Test that getAll() merges all the types into one giant list.
	 */
	public function testGetAll() {
		$expected = [
			'ai'			=> 'application/postscript',
			'amf'			=> 'application/x-amf',
			'atom' 			=> 'application/atom+xml',
			'csv'			=> ['application/csv', 'application/vnd.ms-excel', 'text/csv', 'text/plain'],
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
			'ogg'			=> ['application/ogg', 'audio/ogg', 'video/ogg'],
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
			'zip'			=> ['application/zip', 'application/x-zip'],
			'mp3'			=> ['audio/mpeg', 'video/mpeg'],
			'mp4'			=> ['audio/mp4', 'video/mp4'],
			'mpeg'			=> ['audio/mpeg', 'video/mpeg'],
			'vorbis'		=> 'audio/vorbis',
			'wav'			=> ['audio/x-wav', 'audio/vnd.wave'],
			'webm'			=> ['audio/webm', 'video/webm'],
			'bmp'			=> 'image/bmp',
			'gif'			=> 'image/gif',
			'jpg'			=> 'image/jpeg',
			'jpe'			=> 'image/jpeg',
			'jpeg'			=> 'image/jpeg',
			'pjpeg'			=> 'image/pjpeg',
			'ico'			=> ['image/x-icon', 'image/vnd.microsoft.icon'],
			'png'			=> 'image/png',
			'svg'			=> 'image/svg+xml',
			'tiff'			=> 'image/tiff',
			'alternative'	=> 'multipart/alternative',
			'encrypted'		=> 'multipart/encrypted',
			'file'			=> 'multipart/form-data',
			'mixed'			=> 'multipart/mixed',
			'related'		=> 'multipart/related',
			'signed'		=> 'multipart/signed',
			'css'			=> 'text/css',
			'htm'			=> 'text/html',
			'html'			=> ['text/html', '*/*'],
			'javascript'	=> 'text/javascript',
			'text'			=> 'text/plain',
			'txt'			=> 'text/plain',
			'vcard'			=> 'text/vcard',
			'vcf'			=> 'text/x-vcard',
			'xml'			=> 'text/xml',
			'flv'			=> 'video/x-flv',
			'matroska'		=> 'video/x-matroska',
			'mov'			=> 'video/quicktime',
			'qt'			=> 'video/quicktime',
			'wmv'			=> 'video/x-ms-wmv'
		];

		$this->assertEquals($expected, Mime::getAll());
		$this->assertEquals(['application/ogg', 'audio/ogg', 'video/ogg'], Mime::getAll('ogg'));
	}

	/**
	 * Test that getTypeToExt() returns all types indexed by the mime type and not extension.
	 */
	public function testGetTypeToExt() {
		$expected = [
			'application/postscript' => 'ai',
			'application/x-amf' => 'amf',
			'application/atom+xml' => 'atom',
			'application/csv' => 'csv',
			'application/vnd.ms-excel' => 'csv',
			'application/msword' => ['doc', 'dot'],
			'application/xml-dtd' => 'dtd',
			'application/ecmascript' => ['ecma', 'ecmascript'],
			'application/octet-stream' => 'exe',
			'application/x-www-form-urlencoded' => 'form',
			'application/x-gzip' => ['gz', 'gzip'],
			'application/json' => 'json',
			'application/ogg' => 'ogg',
			'application/pdf' => 'pdf',
			'application/x-rar-compressed' => 'rar',
			'application/rdf+xml' => 'rdf',
			'application/rtf' => 'rtf',
			'application/rss+xml' => 'rss',
			'application/soap+xml' => 'soap',
			'application/x-shockwave-flash' => 'swf',
			'application/x-tar' => 'tar',
			'application/x-font-ttf' => 'ttf',
			'application/font-woff' => 'woff',
			'application/xhtml+xml' => 'xhtml',
			'application/xhtml' => 'xhtml',
			'application/vnd.wap.xhtml+xml' => 'xhtml-mobile',
			'application/zip' => 'zip',
			'application/x-zip' => 'zip',
			'audio/mpeg' => ['mp3', 'mpeg'],
			'audio/mp4' => 'mp4',
			'audio/ogg' => 'ogg',
			'audio/vorbis' => 'vorbis',
			'audio/x-wav' => 'wav',
			'audio/vnd.wave' => 'wav',
			'audio/webm' => 'webm',
			'image/bmp' => 'bmp',
			'image/gif' => 'gif',
			'image/jpeg' => ['jpg', 'jpe', 'jpeg'],
			'image/pjpeg' => 'pjpeg',
			'image/x-icon' => 'ico',
			'image/vnd.microsoft.icon' => 'ico',
			'image/png' => 'png',
			'image/svg+xml' => 'svg',
			'image/tiff' => 'tiff',
			'multipart/alternative' => 'alternative',
			'multipart/encrypted' => 'encrypted',
			'multipart/form-data' => 'file',
			'multipart/mixed' => 'mixed',
			'multipart/related' => 'related',
			'multipart/signed' => 'signed',
			'text/css' => 'css',
			'text/csv' => 'csv',
			'text/plain' => ['csv', 'text', 'txt'],
			'text/html' => ['htm', 'html'],
			'*/*' => 'html',
			'text/javascript' => 'javascript',
			'text/vcard' => 'vcard',
			'text/x-vcard' => 'vcf',
			'text/xml' => 'xml',
			'video/x-flv' => 'flv',
			'video/x-matroska' => 'matroska',
			'video/mpeg' => ['mp3', 'mpeg'],
			'video/mp4' => 'mp4',
			'video/quicktime' => ['mov', 'qt'],
			'video/ogg' => 'ogg',
			'video/webm' => 'webm',
			'video/x-ms-wmv' => 'wmv',
		];

		$this->assertEquals($expected, Mime::getTypeToExt());
		$this->assertEquals(['jpg', 'jpe', 'jpeg'], Mime::getTypeToExt('image/jpeg'));
	}

	/**
	 * Test that set() adds new mime types.
	 */
	public function testSet() {
		$expected = Mime::$images;
		$this->assertEquals($expected, Mime::get(Mime::IMAGE));

		$expected['img'] = 'image/fake';
		Mime::set(Mime::IMAGE, 'img', 'image/fake');
		$this->assertEquals($expected, Mime::get(Mime::IMAGE));

		$expected['gif'] = ['image/gif', 'image/x-gif'];
		Mime::set(Mime::IMAGE, 'gif', 'image/x-gif');
		$this->assertEquals($expected, Mime::get(Mime::IMAGE));
	}

}