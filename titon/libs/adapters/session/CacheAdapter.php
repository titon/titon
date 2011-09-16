<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\adapters\session;

use \titon\Titon;
use \titon\libs\adapters\SessionAdapterAbstract;

/**
 * Caches session data using one of the built in cache storage engines.
 * A storage engine can be setup using Titon::cache()->setup().
 * 
 * @package	titon.libs.adapters.session
 * @uses	titon\Titon
 */
class CacheAdapter extends SessionAdapterAbstract {
	
	/**
	 * Configuration.
	 * 
	 *	storage - The key of the cache storage engine to use.
	 *	expires - When the cache should expire.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'storage' => 'session',
		'expires' => ''
	);

	/**
	 * Triggered when a session is destroyed.
	 * 
	 * @access public
	 * @param string $key
	 * @return void
	 */
	public function destroy($key) {
		return Titon::cache()->storage($this->config('storage'))->remove($key);
	}

	/**
	 * Triggered when the sessions garbage collector activates.
	 * 
	 * @access public
	 * @param int $maxLifetime
	 * @return void
	 */
	public function gc($maxLifetime) {
		return Titon::cache()->storage($this->config('storage'))->flush(time() - $maxLifetime);
	}
	
	/**
	 * Validate the cache lifetime.
	 * 
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$expires = $this->config('expires');
		
		if (empty($expires)) {
			$this->configure('expires', time() + ini_get('session.gc_maxlifetime'));
		}
	}

	/**
	 * Read value from the session handler.
	 * 
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function read($key) {
		return (string) Titon::cache()->storage($this->config('storage'))->get($key);
	}

	/**
	 * Write data to the session handler.
	 * 
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return boolean
	 */
	public function write($key, $value) {
		return Titon::cache()->storage($this->config('storage'))->set($key, $value);
	}	
	
}
