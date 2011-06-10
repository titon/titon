<?php
/**
 * Primary library class to manage and produce cookies. Can encrypt and decrypt cookies according to $encrypt,
 * as well as packaging all cookies within a namespace architecture.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\state;

use \titon\core\Config;
use \titon\utility\Set;

/**
 * Cookie Class
 *
 * @package		Titon
 * @subpackage	Titon.State
 */
class Cookie {

    /**
     * What domain the cookie should be useable on.
     *
     * @access public
     * @var string
     */
    public $domain = '';

    /**
     * Should the cookies be encrypted and decrypted.
     *
     * @access public
     * @var boolean
     */
    public $encrypt = true;

    /**
     * How much time until the cookie expires.
     *
     * @access public
     * @var string
     */
    public $expires = '+1 week';

    /**
     * The namespace to wrap all cookies within.
     *
     * @access public
     * @var string
     */
    public $namespace = 'Cookie';

    /**
     * Which path should the cookie only be accessible to
     *
     * @access public
     * @var string
     */
    public $path = '/';

    /**
     * Should the cookie only be useable across an HTTP connection.
     *
     * @access public
     * @var string
     */
    public $secure = false;

    /**
     * Destroy a specific cookie within the namespace. Returns true if successful, else if failure.
     *
     * @access public
     * @param string $key
     * @return bool
     */
	public function delete($key = null) {
        return setcookie($this->namespace .'['. $key .']', '', time(), $this->path, $this->domain, $this->secure);
	}

    /**
     * Get a value or an index from a cookie depending on the given key or path. Will decrypt the cookie if necessary.
     *
     * @access public
     * @param string $key
     * @return string
     */
	public function get($key = null) {
		if (isset($_COOKIE[$this->namespace][$key])) {
            return $this->__decrypt($_COOKIE[$this->namespace][$key]);
		}
        
		return null;
	}

    /**
     * Remove a certain key/path from the cookie. Returns true if successful, else if failure.
     *
     * @access public
     * @param string $key
     * @param array $meta
     * @return bool
     */
	public function remove($key, array $meta = array()) {
        if (!empty($key)) {
            $paths = explode('.', $key);
            $index = array_shift($paths);

            if (isset($_COOKIE[$this->namespace][$index])) {
                $cookie = $this->__decrypt($_COOKIE[$this->namespace][$index]);
                $cookie = Set::remove($cookie, implode('.', $paths));

                return $this->set($index, $cookie, $meta);
            }
        }

        return false;
	}

    /**
     * Set a cookie. Can take an optional 3rd argument to overwrite the default cookie parameters.
     *
     * @access public
     * @param string $key
     * @param string $value
     * @param array $meta
     * @return bool
     */
	public function set($key, $value, array $meta = array()) {
		if (!empty($key) && !empty($value)) {
            $meta = $meta + array(
                'expires' => $this->expires,
                'domain' => $this->domain,
                'secure' => $this->secure,
                'path' => $this->path
            );

            return setcookie($this->namespace .'['. $key .']', $this->__encrypt($value), $this->__expires($meta['expires']), $meta['path'], $meta['domain'], $meta['secure']);
		}

        return false;
	}

    /**
     * Decrypt a cookies value to by usuable by the application.
     *
     * @access public
     * @param string $value
     * @return string
     */
	private function __decrypt($value) {
        if (empty($value) || $this->encrypt === false) {
            return $value;
        }

        $parts = explode(';', $value);
        $decrypted = '';

        foreach ($parts as $part) {
            $decrypted .= chr($part);
        }

        $decrypted = unserialize(base64_decode(Config::get('App.salt') . $decrypted));
        return $decrypted;
	}

    /**
     * Encrypt a cookies value to offer more security and protection.
     *
     * @access private
     * @param string $value
     * @return string
     */
	private function __encrypt($value) {
        if (empty($value) || $this->encrypt === false) {
            return $value;
        }

        if (is_array($value)) {
            $value = serialize($value);
        }

        $value = base64_encode(Config::get('App.salt') . $value);
        $length = strlen($value);
        $encrypted = '';

        for ($i = 0; $i < $length; ++$i) {
            $encrypted .= ';' . ord(substr($value, $i, 1));
        }

        $encrypted = trim($encrypted, ';');
        return $encrypted;
	}

    /**
     * Converts a string to a unix timestamp if it is not one.
     *
     * @access private
     * @param mixed $time
     * @return int
     */
    private function __expires($time) {
		return (is_int($time) ? $time : strtotime($time));
    }
	
}