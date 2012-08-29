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
use \Memcached;

/**
 * A storage engine for the Memcache key-value store; requires pecl/memcached.
 * This engine can be installed using the Cache::setup() method.
 *
 * {{{
 *		new MemcacheStorage(array(
 *			'server' => 'localhost:11211',
 *			'persistent' => true,
 *			'compress' => true
 *		));
 * }}}
 *
 * A sample configuration can be found above, and the following options are available:
 * server (array or string), compress, persistent, serialize, expires, prefix.
 *
 * @package	titon.libs.storage.cache
 *
 * @link	http://pecl.php.net/package/memcached
 */
class MemcacheStorage extends StorageAbstract {

	/**
	 * Default Memcache server port.
	 */
	const PORT = 11211;

	/**
	 * Default Memcache server weight.
	 */
	const WEIGHT = 0;

	/**
	 * The third-party class instance.
	 *
	 * @access public
	 * @var \Memcached
	 */
	public $connection;

	/**
	 * Decrement a value within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @param int $step
	 * @return boolean
	 */
	public function decrement($key, $step = 1) {
		return $this->connection->decrement($this->key($key), $step);
	}

	/**
	 * Empty the cache.
	 *
	 * @access public
	 * @return boolean
	 */
	public function flush() {
		return $this->connection->flush();
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
		return (bool) ($this->get($key) && $this->connection->getResultCode() === Memcached::RES_SUCCESS);
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
		return $this->connection->increment($this->key($key), $step);
	}

	/**
	 * Initialize the Memcached instance and set all relevant options.
	 *
	 * @access public
	 * @return void
	 * @throws \titon\libs\storage\StorageException
	 */
	public function initialize() {
		if (!Titon::load('memcached')) {
			throw new StorageException('Memcache extension does not exist.');
		}

		$config = $this->config->get();

		if (!$config['server']) {
			throw new StorageException(sprintf('No server has been defined for %s.', $this->info->className()));
		}

		$this->connection = new Memcached($config['id']);
		$this->connection->setOption(Memcached::OPT_COMPRESSION, (bool) $config['compress']);
		$this->connection->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
		$this->connection->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
		$this->connection->setOption(Memcached::OPT_BUFFER_WRITES, true);

		if (Titon::load('igbinary')) {
			$this->connection->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
		}

		if (is_array($config['server'])) {
			$serverList = $this->connection->getServerList();

			if (empty($serverList)) {
				$this->connection->addServers($config['server']);
			}
		} else {
			list($host, $port, $weight) = explode(':', $config['server']);

			$this->connection->addServer($host, $port ?: self::PORT, $weight ?: self::WEIGHT);
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
		return $this->connection->delete($this->key($key), 0);
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
		return $this->connection->set($this->key($key), $this->encode($value), $this->expires($expires));
	}

}