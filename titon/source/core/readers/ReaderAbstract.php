<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core\readers;

use \titon\source\core\readers\ReaderInterface;

/**
 * Interface for all Config Readers.
 *
 * @package		Titon
 * @subpackage	Core.Readers
 */
abstract class ReaderAbstract implements ReaderInterface {

	/**
	 * Array of loaded configurations.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array();

	/**
	 * The reader must return the loaded config file as an array.
	 *
	 * @access public
	 * @return array
	 */
	public function toArray() {
		return $this->_config;
	}

}