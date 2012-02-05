<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\adapters;

/**
 * Interface for all session adapters, based on session_set_save_handler().
 * 
 * @package	titon.libs.adapters
 */
interface SessionAdapter {

	/**
	 * Close the session handler.
	 * 
	 * @access public
	 * @return void
	 */
	public function close();

	/**
	 * Triggered when a session is destroyed.
	 * 
	 * @access public
	 * @param string $key
	 * @return void
	 */
	public function destroy($key);

	/**
	 * Triggered when the sessions garbage collector activates.
	 * 
	 * @access public
	 * @param int $maxLifetime
	 * @return void
	 */
	public function gc($maxLifetime);

	/**
	 * Open the session handler.
	 * 
	 * @access public
	 * @param string $savePath
	 * @param string $sessionName
	 * @return void
	 */
	public function open($savePath, $sessionName);

	/**
	 * Read value from the session handler.
	 * 
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function read($key);
	
	/**
	 * Register the session handler.
	 * 
	 * @access public
	 * @return void
	 */
	public function register();

	/**
	 * Write data to the session handler.
	 * 
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return boolean
	 */
	public function write($key, $value);
	
}