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
use titon\libs\storage\cache\MemcacheStorage;
use titon\libs\storage\StorageException;
use \Memcached;

/**
 * A storage engine for the Memcache key-value store; requires pecl/memcached module.
 * This engine can be installed using the Cache::setup() method.
 *
 * {{{
 *		new MemcachedStorage(array(
 *			'id' => 'default',
 *			'server' => 'localhost:11211',
 *			'compress' => true
 *		));
 * }}}
 *
 * A sample configuration can be found above, and the following options are available:
 * id, server (array or string), compress, serialize, prefix, expires.
 *
 * @package	titon.libs.storage.cache
 *
 * @link	http://pecl.php.net/package/memcached
 */
class MemcachedStorage extends MemcacheStorage {

	/**
	 * Check if the item exists within the cache.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return (bool) ($this->get($this->key($key)) && $this->connection->getResultCode() === Memcached::RES_SUCCESS);
	}

	/**
	 * Initialize the Memcached instance and set all relevant options.
	 *
	 * @access public
	 * @return void
	 * @throws \titon\libs\storage\StorageException
	 */
	public function initialize() {
		if (!Titon::load('memcache')) {
			throw new StorageException('Memcache extension does not exist.');
		}

		$config = $this->config->get();

		if (!$config['server']) {
			throw new StorageException(sprintf('No server has been defined for %s.', $this->info->className()));
		}

		$this->connection = $this->connection ?: new Memcached($config['id']);
		$this->connection->setOption(Memcached::OPT_COMPRESSION, (bool) $config['compress']);
		$this->connection->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
		$this->connection->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
		$this->connection->setOption(Memcached::OPT_BUFFER_WRITES, true);

		if (Memcached::HAVE_IGBINARY) {
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