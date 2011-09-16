<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\adapters;

use \titon\base\Base;
use \titon\libs\adapters\SessionAdapter;

/**
 * Primary class for all session adapters extend. Automatically registers the handler when set into the Session class.
 * 
 * @package	titon.libs.adapters
 */
abstract class SessionAdapterAbstract extends Base implements SessionAdapter {

	/**
	 * Close the session handler.
	 * 
	 * @access public
	 * @return void
	 */
	public function close() {
		return;
	}

	/**
	 * Triggered when a session is destroyed.
	 * 
	 * @access public
	 * @param string $key
	 * @return void
	 */
	public function destroy($key) {
		return;
	}

	/**
	 * Triggered when the sessions garbage collector activates.
	 * 
	 * @access public
	 * @param int $maxLifetime
	 * @return void
	 */
	public function gc($maxLifetime) {
		return;
	}

	/**
	 * Open the session handler.
	 * 
	 * @access public
	 * @param string $savePath
	 * @param string $sessionName
	 * @return void
	 */
	public function open($savePath, $sessionName) {
		return;
	}

	/**
	 * Read value from the session handler.
	 * 
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function read($key) {
		return;
	}
	
	/**
	 * Register the session handler.
	 * 
	 * @access public
	 * @return void
	 * @final
	 */
	final public function register() {
		session_set_save_handler(
			array($this, 'open'),
			array($this, 'close'),
			array($this, 'read'),
			array($this, 'write'),
			array($this, 'destroy'),
			array($this, 'gc')
		);
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
		return;
	}

}