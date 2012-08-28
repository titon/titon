<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\storage\cache;

use titon\Titon;
use titon\libs\storage\StorageAbstract;
use titon\libs\storage\StorageException;
use \Redis;

/**
 * A storage engine for the Redis key-value store; requires the redis modules.
 * This engine can be installed using the Cache::setup() method.
 *
 * {{{
 *		new RedisStorage(array(
 *			'server' => 'localhost:11211',
 *			'persistent' => true
 *		));
 * }}}
 *
 * A sample configuration can be found above, and the following options are available:
 * server, persistent, serialize, expires.
 *
 * @package	titon.libs.storage.cache
 *
 * @link	https://github.com/nicolasff/phpredis
 */
class RedisStorage extends StorageAbstract {

	/**
	 * Default Redis server port.
	 */
	const PORT = 6379;

	/**
	 * Decrement a value within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @param int $step
	 * @return boolean
	 */
	public function decrement($key, $step = 1) {
		return $this->connection->decrBy($this->key($key), $step);
	}

	/**
	 * Empty the cache.
	 *
	 * @access public
	 * @return boolean
	 */
	public function flush() {
		return $this->connection->flushDB();
	}

	/**
	 * Get data from the cache if it exists.
	 *
	 * @access public
	 * @param string|array $key
	 * @return mixed
	 */
	public function get($key) {
		return $this->decode($this->connection->get($this->key($key)));
	}

	/**
	 * Check if the item exists within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return $this->connection->exists($this->key($key));
	}

	/**
	 * Increment a value within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @param int $step
	 * @return boolean
	 */
	public function increment($key, $step = 1) {
		return $this->connection->incrBy($this->key($key), $step);
	}

	/**
	 * Initialize the Redis instance and set all relevant options.
	 *
	 * @access public
	 * @return void
	 * @throws \titon\libs\storage\StorageException
	 */
	public function initialize() {
		if (!Titon::load('redis')) {
			throw new StorageException('Redis extension does not exist.');
		}

		$config = $this->config->get();

		if (!$config['server']) {
			throw new StorageException(sprintf('No server has been defined for %s.', $this->info->className()));
		}

		$this->connection = $this->connection ?: new Redis();
		$this->connection->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);

		list($host, $port, $timeout) = explode(':', $config['server']);

		if ($config['persistent']) {
			$this->connection->pconnect($host, $port ?: self::PORT, $timeout ?: 0);
		} else {
			$this->connection->connect($host, $port ?: self::PORT, $timeout ?: 0);
		}
	}

	/**
	 * Remove the item if it exists and return true, else return false.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		return $this->connection->delete($this->key($key));
	}

	/**
	 * Set data to the cache.
	 *
	 * @access public
	 * @param string|array $key
	 * @param mixed $value
	 * @param mixed $expires
	 * @return boolean
	 */
	public function set($key, $value, $expires = null) {
		return $this->connection->setex($this->key($key), $this->encode($value), $this->expires($expires) - time());
	}

}