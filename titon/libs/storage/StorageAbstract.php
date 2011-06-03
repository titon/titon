<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\storage;

use \titon\libs\storage\StorageInterface;

/**
 * Primary class for all storage engines to extend. Provides functionality from the Base class and the StorageInterface.
 *
 * @package	titon.libs.storage
 */
abstract class StorageAbstract extends Base implements StorageInterface {
	
	/**
	 * Default storage configuration.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_config = array('serialize' => true);
	
}