<?php
/**
 * Titon: A PHP 5.4 Modular Framework
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
	 *	httponly - Should the cookie only be accessable through PHP and not the Javascript layer
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
		'httponly' => true,
		'encrypt' => true
	);

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

			if ($this->config('encrypt')) {	
				$salt = md5(Titon::config()->salt());
				$value = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($value), MCRYPT_MODE_CBC, $salt), "\0");
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
	 * Remove a specific cookie. Returns true if successful, else false if failure.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		$config = $this->config();

		return setcookie($key, '', time(), $config['path'], $config['domain'], $config['secure'], $config['httponly']);
	}

	/**
	 * Set a cookie. Can take an optional 3rd argument to overwrite the default cookie parameters.
	 *
	 * @access public
	 * @param string $key
	 * @param string $value
	 * @param array $config
	 * @return boolean
	 */
	public function set($key, $value, array $config = array()) {
		$config = $config + $this->config();
		$expires = is_int($config['expires']) ? $config['expires'] : strtotime($config['expires']);

		if ($this->config('encrypt')) {
			$salt = md5(Titon::config()->salt());
			$value = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $value, MCRYPT_MODE_CBC, $salt));
		}

		return setcookie($key, $value, $expires, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
	}

}