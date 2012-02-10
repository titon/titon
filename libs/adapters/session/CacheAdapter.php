<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\adapters\session;

use \titon\Titon;
use \titon\libs\adapters\SessionAdapterAbstract;
use \titon\libs\storage\Storage;

/**
 * Caches session data using one of the built in cache storage engines.
 * A storage engine can be setup using Titon::cache()->setup().
 * 
 * @package	titon.libs.adapters.session
 * @uses	titon\Titon
 */
class CacheAdapter extends SessionAdapterAbstract {

	/**
	 * Storage engine instance.
	 * 
	 * @access protected
	 * @var Storage
	 */
	protected $_storage;
	
	/**
	 * Inject the storage engine.
	 * 
	 * @access public
	 * @param Storage $storage
	 * @return void
	 */
	final public function __construct(Storage $storage) {
		parent::__construct();
		
		$this->_storage = $storage;
		$this->_storage->configure('storage', 'session');
	}

	/**
	 * Triggered when a session is destroyed.
	 * 
	 * @access public
	 * @param string $key
	 * @return void
	 */
	public function destroy($key) {
		$this->_storage->remove($key);
	}

	/**
	 * Triggered when the sessions garbage collector activates.
	 * 
	 * @access public
	 * @param int $maxLifetime
	 * @return void
	 */
	public function gc($maxLifetime) {
		$this->_storage->flush(time() - $maxLifetime);
	}

	/**
	 * Read value from the session handler.
	 * 
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function read($key) {
		return (string) $this->_storage->get($key);
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
		return $this->_storage->set($key, $value, null);
	}	
	
}
