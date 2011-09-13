<?php

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
	 * @param string $id
	 * @return void
	 */
	public function destroy($id);

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
	 * @param string $id
	 * @return string
	 */
	public function read($id);

	/**
	 * Write data to the session handler.
	 * 
	 * @access public
	 * @param string $id
	 * @param mixed $data
	 * @return string
	 */
	public function write($id, $data);
	
}