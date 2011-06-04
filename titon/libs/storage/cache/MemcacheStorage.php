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
use \titon\log\Exception;

/**
 * @todo
 *
 * @package	titon.libs.storage.cache
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
			throw new Exception('Memcache extension does not exist.');
		}
	}
	
}