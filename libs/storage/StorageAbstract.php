<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\storage;

use titon\base\Base;
use titon\libs\storage\Storage;
use titon\libs\traits\Cacheable;
use titon\utility\Time;

/**
 * Primary class for all storage engines to extend. Provides functionality from the Base class and the Storage interface.
 *
 * @package	titon.libs.storage
 * @abstract
 */
abstract class StorageAbstract extends Base implements Storage {
	use Cacheable;

	/**
	 * Configuration.
	 *
	 *	id 			- Unique ID for specific engines
	 * 	server		- Server(s) to connect and store data in
	 *	serialize 	- Toggle data serialization
	 *	compress 	- Toggle data compression
	 *	persistent 	- Toggle persistent server connections
	 *	expires 	- Global expiration timer
	 *	prefix 		- String to prefix before each cache key
	 *	username 	- Username used for HTTP auth
	 *	password 	- Password used for HTTP auth
	 *	storage 	- The alias for the current storage engine
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'id' => '',
		'server' => '',
		'serialize' => false,
		'compress' => false,
		'persistent' => true,
		'expires' => '+1 day',
		'prefix' => '',
		'username' => '',
		'password' => '',
		'storage' => '',
		'initialize' => true
	];

	/**
	 * Convert the expires date into a valid UNIX timestamp.
	 *
	 * @access public
	 * @param mixed $timestamp
	 * @return int
	 */
	public function expires($timestamp) {
		if ($timestamp === null) {
			$timestamp = strtotime($this->config->expires);

		} else {
			$timestamp = Time::toUnix($timestamp);
		}

		return $timestamp;
	}

	/**
	 * Rewrite the key to use a specific format.
	 *
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function key($key) {
		return $this->cache([__METHOD__, $key], function() use ($key) {
			return $this->config->prefix . trim(preg_replace('/[^a-z0-9\.]+/is', '', str_replace(['\\', '::', '/', '-', '_'], '.', $this->createCacheKey($key))), '.');
		});
	}

	/**
	 * Serialize the data if the configuration is true.
	 *
	 * @access public
	 * @param mixed $value
	 * @return string
	 */
	public function encode($value) {
		if ($this->config->serialize) {
			if (function_exists('igbinary_serialize')) {
				$value = igbinary_serialize($value);
			} else {
				$value = serialize($value);
			}
		}

		return $value;
	}

	/**
	 * Unserialize the data if it is valid and the configuration is true.
	 *
	 * @access public
	 * @param mixed $value
	 * @return mixed
	 */
	public function decode($value) {
		if ($value && $this->config->serialize) {
			if (function_exists('igbinary_unserialize')) {
				$value = @igbinary_unserialize($value);
			} else {
				$value = @unserialize($value);
			}
		}

		return $value;
	}

}