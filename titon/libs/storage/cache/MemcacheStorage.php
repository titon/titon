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
use \titon\libs\storage\StorageException;

/**
 * @todo
 *
 * @package	titon.libs.storage.cache
 * @uses	titon\libs\storage\StorageException
 */
class MemcacheStorage extends StorageAbstract {
	
	/**
	 * Initialize the Memcached instance and set all relevant options.
	 * 
	 * @access public
	 * @return void
	 */
	public function initialize() {
		if (!extension_loaded('memcache')) {
			throw new StorageException('Memcache extension does not exist.');
		}
	}
	
}