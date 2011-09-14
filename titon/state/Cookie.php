<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\state;

use \titon\Titon;
use \titon\base\Base;
use \Closure;

/**
 * Primary library class to manage and produce cookies with customizable encryption and decryption.
 *
 * @package	titon.state
 * @uses	titon\Titon
 */
class Cookie extends Base {

	/**
	 * Configuration.
	 * 
	 *	domain - What domain the cookie should be useable on
	 *	expires - How much time until the cookie expires
	 *	path - Which path should the cookie only be accessible to
	 *	secure - Should the cookie only be useable across an HTTPS connection
	 *	encrypt - Should the cookies be encrypted and decrypted
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'domain' => '',
		'expires' => '+1 week',
		'path' => '/',
		'secure' => false,
		'encrypt' => true
	);

	/**
	 * Callback to decrypt the data.
	 * 
	 * @access protected
	 * @var Closure
	 */
	protected $_decrypt = null;

	/**
	 * Callback to encrypt the data.
	 * 
	 * @access protected
	 * @var Closure
	 */
	protected $_encrypt = null;

	/**
	 * Destroy a specific cookie. Returns true if successful, else false if failure.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function delete($key) {
		$config = $this->config();

		return setcookie($key, '', time(), $config['path'], $config['domain'], $config['secure']);
	}

	/**
	 * Get a value from a cookie depending on the given key. Will decrypt the cookie if necessary.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		if ($this->has($key)) {
			$value = $_COOKIE[$key];

			// Assign closure to a variable as it will throw an undefined method error
			if ($this->config('encrypt')) {
				$decrypt = $this->_decrypt;
				$value = $decrypt($value);
			}

			return $value;
		}

		return null;
	}

	/**
	 * Check to see if a certain key exists.
	 * 
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return isset($_COOKIE[$key]);
	}

	/**
	 * If no encryption or decryption callbacks are present, define them.
	 * 
	 * @access public
	 * @return void
	 */
	public function initialize() {
		if ($this->_encrypt === null) {
			$this->setEncrypter(function($value) {
				$salt = md5(Titon::config()->salt());

				return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $value, MCRYPT_MODE_CBC, $salt));
			});
		}

		if ($this->_decrypt === null) {
			$this->setDecrypter(function($value) {
				$salt = md5(Titon::config()->salt());

				return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($value), MCRYPT_MODE_CBC, $salt), "\0");
			});
		}
	}

	/**
	 * Set a cookie. Can take an optional 3rd argument to overwrite the default cookie parameters.
	 *
	 * @access public
	 * @param string $key
	 * @param string $value
	 * @param array $config
	 * @return bool
	 */
	public function set($key, $value, array $config = array()) {
		$config = $config + $this->config();
		$expires = is_int($config['expires']) ? $config['expires'] : strtotime($config['expires']);

		// Assign closure to a variable as it will throw an undefined method error
		if ($this->config('encrypt')) {
			$encrypt = $this->_encrypt;
			$value = $encrypt($value);
		}

		return setcookie($key, $value, $expires, $config['path'], $config['domain'], $config['secure']);
	}

	/**
	 * Set the encryption method.
	 * 
	 * @access public
	 * @param Closure $encrypt
	 * @return Cookie
	 * @chainable 
	 */
	public function setEncrypter(Closure $encrypt) {
		$this->_encrypt = $encrypt;

		return $this;
	}

	/**
	 * Set the decryption method.
	 * 
	 * @access public
	 * @param Closure $encrypt
	 * @return Cookie
	 * @chainable 
	 */
	public function setDecrypter(Closure $decrypt) {
		$this->_decrypt = $decrypt;

		return $this;
	}

}