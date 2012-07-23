<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\utility;

use titon\tests\TestCase;
use titon\utility\Crypt;

/**
 * Test class for titon\utility\Crypt.
 */
class CryptTest extends TestCase {

	/**
	 * String to crypt.
	 */
	public $string = 'This string will be used to encrypt and decrypt. It contains numbers: 1 2 3 4 5 6 7 8 9 0. It also contains punctuation: ! @ # $ % ^ & * ( ) : [ < ?. You get the picture';

	/**
	 * Test that blowfish() will encrypt and decrypt.
	 */
	public function testBlowfish() {
		$this->assertEquals($this->string, Crypt::blowfish(Crypt::blowfish($this->string, 'blowfish.key'), 'blowfish.key', Crypt::DECRYPT));
	}

	/**
	 * Test that des() will encrypt and decrypt.
	 */
	public function testDes() {
		$this->assertEquals($this->string, Crypt::des(Crypt::des($this->string, 'des.key'), 'des.key', Crypt::DECRYPT));
	}

	/**
	 * Test that encrypt() and decrypt() can utilize other types of ciphers and modes.
	 */
	public function testEncryptDecrypt() {
		$e = Crypt::encrypt($this->string, 'cast.key', MCRYPT_CAST_128, MCRYPT_MODE_ECB);
		$d = Crypt::decrypt($e, 'cast.key', MCRYPT_CAST_128, MCRYPT_MODE_ECB);

		$this->assertEquals($this->string, $d);

		$e = Crypt::encrypt($this->string, 'xtea.key', MCRYPT_XTEA, MCRYPT_MODE_CFB);
		$d = Crypt::decrypt($e, 'xtea.key', MCRYPT_XTEA, MCRYPT_MODE_CFB);

		$this->assertEquals($this->string, $d);
	}

	/**
	 * Test that hash() hashes the string to the cipher and returns the correct length.
	 */
	public function testHash() {
		$md51 = Crypt::hash('md5', $this->string);
		$md52 = Crypt::hash('md5', $this->string, 'md5.salt');

		$this->assertTrue(strlen($md51) === 32);
		$this->assertTrue(strlen($md52) === 32);
		$this->assertNotEquals($md51, $md52);

		$sha1 = Crypt::hash('sha1', $this->string);
		$sha2 = Crypt::hash('sha1', $this->string, 'sha1.salt');

		$this->assertTrue(strlen($sha1) === 40);
		$this->assertTrue(strlen($sha2) === 40);
		$this->assertNotEquals($sha1, $sha2);

		$sha1 = Crypt::hash('sha256', $this->string);
		$sha2 = Crypt::hash('sha256', $this->string, 'sha256.salt');

		$this->assertTrue(strlen($sha1) === 64);
		$this->assertTrue(strlen($sha2) === 64);
		$this->assertNotEquals($sha1, $sha2);
	}

	/**
	 * Test that obfuscate() returns a entity equivalent.
	 */
	public function testObfuscate() {
		$this->assertEquals('&#84;&#105;&#116;&#111;&#110;&#32;&#70;&#114;&#97;&#109;&#101;&#119;&#111;&#114;&#107;', Crypt::obfuscate('Titon Framework'));
	}

	/**
	 * Test that rijndael() will encrypt and decrypt.
	 */
	public function testRijndael() {
		$this->assertEquals($this->string, Crypt::rijndael(Crypt::rijndael($this->string, 'rijndael.key'), 'rijndael.key', Crypt::DECRYPT));
	}

	/**
	 * Test that tripledes() will encrypt and decrypt.
	 */
	public function testTripledes() {
		$this->assertEquals($this->string, Crypt::tripledes(Crypt::tripledes($this->string, '3des.key'), '3des.key', Crypt::DECRYPT));
	}

}