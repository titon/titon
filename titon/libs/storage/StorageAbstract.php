<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\storage;

use \titon\base\Base;
use \titon\libs\storage\Storage;

/**
 * Primary class for all storage engines to extend. Provides functionality from the Base class and the Storage interface.
 *
 * @package	titon.libs.storage
 */
abstract class StorageAbstract extends Base implements Storage {
	
	/**
	 * The third-party class instance.
	 * 
	 * @access public
	 * @var object
	 */
	public $connection;
	
	/**
	 * Default storage configuration.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'id' => '',
		'servers' => array(),
		'serialize' => false,
		'compress' => false,
		'persistent' => true,
		'expires' => '+1 day',
		'prefix' => '',
		'username' => '',
		'password' => ''
	);
	
	/**
	 * Convert the expires date into a valid UNIX timestamp.
	 * 
	 * @access public
	 * @param mixed $timestamp
	 * @return int 
	 */
	public function expires($timestamp) {
		if ($timestamp === null) {
			$timestamp = strtotime($this->config('expires'));
			
		} else if (is_string($timestamp)) {
			$timestamp = strtotime($timestamp);
		}
		
		return (int) $timestamp;
	}
	
	/**
	 * Serialize the data if the configuration is true.
	 * 
	 * @access public
	 * @param mixed $value
	 * @return string
	 */
	public function serialize($value) {
		if ($this->config('serialize')) {
			$value = serialize($value);
		}
		
		return $value;
	}
	
	/**
	 * Unerialize the data if it is valid and the configuration is true.
	 * 
	 * @access public
	 * @param mixed $value
	 * @return mixed
	 */
	public function unserialize($value) {
		if ($value && $this->config('serialize')) {
			$value = unserialize($value);
		}
		
		return $value;
	}
	
}