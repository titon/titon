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
use titon\libs\traits\Attachable;
use titon\utility\Crypt;
use titon\utility\Time;
use \Closure;

/**
 * Primary class to manage and produce cookies with customizable encryption and decryption.
 *
 * @package	titon.net
 */
class Cookie extends Base {
	use Attachable;

	/**
	 * Configuration.
	 *
	 *	domain 		- What domain the cookie should be usable on
	 *	expires 	- How much time until the cookie expires
	 *	path 		- Which path should the cookie only be accessible to
	 *	secure 		- Should the cookie only be usable across an HTTPS connection
	 *	httpOnly 	- Should the cookie only be accessible through PHP and not the Javascript layer
	 *	encrypt 	- Supply the Crypt cipher that you would like to use for encryption and decryption
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'domain' => '',
		'expires' => '+1 week',
		'path' => '/',
		'secure' => false,
		'httpOnly' => true,
		'encrypt' => Crypt::RIJNDAEL
	];

	/**
	 * Attach the Response object.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('response', function() {
			return Titon::registry()->factory('titon\net\Response');
		});
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

			if ($cipher = $this->config->encrypt) {
				$value = Crypt::decrypt($value, $key, $cipher);
			}

			return unserialize(base64_decode($value));
		}

		return null;
	}

	/**
	 * Set a cookie. Can take an optional 3rd argument to overwrite the default cookie parameters.
	 *
	 * @access public
	 * @param string $key
	 * @param string $value
	 * @param array $config
	 * @return titon\net\Cookie
	 * @chainable
	 */
	public function set($key, $value, array $config = []) {
		$config = $config + $this->config->get();
		$config['expires'] = Time::toUnix($config['expires']);

		$value = base64_encode(serialize($value));

		if ($cipher = $this->config->encrypt) {
			$value = Crypt::encrypt($value, $key, $cipher);
		}

		$this->response->cookie($key, $value, $config);

		$_COOKIE[$key] = $value;

		return $this;
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
	 * @return titon\net\Cookie
	 * @chainable
	 */
	public function remove($key) {
		$config = $this->config->get();
		$config['expires'] = time();

		$this->response->cookie($key, '', $config);

		unset($_COOKIE[$key]);

		return $this;
	}

}