<?php

namespace titon\libs\adapters;

use \titon\libs\adapters\SessionAdapter;

abstract class SessionAdapterAbstract implements SessionAdapter {
	
	/**
	 * Set the handler on instantiation.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
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
	 * Close the session handler.
	 * 
	 * @access public
	 * @return void
	 */
	abstract public function close();

	/**
	 * Triggered when a session is destroyed.
	 * 
	 * @access public
	 * @param string $id
	 * @return void
	 */
	abstract public function destroy($id);

	/**
	 * Triggered when the sessions garbage collector activates.
	 * 
	 * @access public
	 * @param int $maxLifetime
	 * @return void
	 */
	abstract public function gc($maxLifetime);
		
	/**
	 * Open the session handler.
	 * 
	 * @access public
	 * @param string $savePath
	 * @param string $sessionName
	 * @return void
	 */
	abstract public function open($savePath, $sessionName);

	/**
	 * Read value from the session handler.
	 * 
	 * @access public
	 * @param string $id
	 * @return string
	 */
	abstract public function read($id);

	/**
	 * Write data to the session handler.
	 * 
	 * @access public
	 * @param string $id
	 * @param mixed $data
	 * @return string
	 */
	abstract public function write($id, $data);
	
}