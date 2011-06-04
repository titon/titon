<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\storage\cache;

use \titon\libs\storage\StorageAbstract;

/**
 * A storage engine for the Memcache module, using the Memcached class; requires pecl/memcached. 
 * This class should be instantiated as follows through the Cache::setup() method.
 * 
 *		new MemcachedStorage(array(
 *			'id' => 'default',
 *			'servers' => 'localhost:11211',
 *			'compress' => true
 *		));
 *
 * @package	titon.libs.storage.cache
 * @link	http://pecl.php.net/package/memcached
 */
class MemcachedStorage extends StorageAbstract {
	
	/**
	 * Default Memcache server port.
	 */
	const PORT = 11211;
	
	/**
	 * Default Memcache server weight.
	 */
	const WEIGHT = 0;
	
	/**
	 * Initialize the Memcached instance and set all relevant options.
	 * 
	 * @access public
	 * @return void
	 */
	public function initialize() {
		if (!class_exists('\Memcached')) {
			throw new Exception('Memcached module does not exist.');
		}
		
		$config = $this->config();
		
		if (empty($config['servers'])) {
			return;
		}
		
		$this->connection = $this->connection ?: new \Memcached($config['id']);
		$this->connection->setOption(Memcached::OPT_COMPRESSION, (bool) $config['compress']);
		$this->connection->setOption(Memcached::OPT_PREFIX_KEY, (string) $config['prefix']);
		$this->connection->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
		$this->connection->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
		
		if (Memcached::HAVE_IGBINARY) {
			$this->connection->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
		}

		if (is_array($config['servers'])) {
			$serverList = $this->connection->getServerList();

			if (empty($serverList)) {
				$this->connection->addServers($config['servers']);
			}
		} else {
			list($host, $port, $weight) = explode(':', $config['servers']);

			if (empty($port)) {
				$port = self::PORT;
			}

			if (empty($weight)) {
				$weight = self::WEIGHT;
			}

			$this->connection->addServer($host, (int) $port, (int) $weight);
		}
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
	 * Get data from the cache if it exists. If serialize is true, the data will be unserialized.
	 * 
	 * @access public
	 * @param string|array $key
	 * @return mixed
	 */
	public function get($key) {
		if (is_array($key)) {
			return $this->connection->getMulti($key, null, Memcached::GET_PRESERVE_ORDER);
		}
		
		$value = $this->connection->get($key);
		
		if ($value && $this->config('serialize')) {
			$value = unserialize($value);
		}
		
		return $value;
	}
	
	/**
	 * Check if the item exists within the cache.
	 * 
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return $this->get($key);
	}
	
	/**
	 * Remove the item if it exists and return true, else return false.
	 * 
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		return $this->connection->delete($key, 0);
	}
	
	/**
	 * Set data to the cache. If serialize is true, the data will be serialized.
	 * 
	 * @access public
	 * @param string|array $key
	 * @param mixed $value 
	 * @param mixed $expires
	 * @return boolean
	 */
	public function set($key, $value = null, $expires = null) {
		if (is_array($key)) {
			return $this->connection->setMulti($key, $this->expires($expires));
		}
		
		if ($this->config('serialize')) {
			$value = serialize($value);
		}
		
		return $this->connection->set($key, $value, $this->expires($expires));
	}
	
}