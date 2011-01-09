<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core\readers;

use \titon\source\core\readers\ReaderAbstract;
use \titon\source\log\Exception;

/**
 * A reader that loads its configuration from a PHP file.
 * The PHP file must contain a return statement that returns an array.
 *
 * @package	titon.source.core.readers
 * @uses	titon\source\log\Exception
 * 
 * @link	http://php.net/manual/en/function.include.php
 */
class PhpReader extends ReaderAbstract {

	/**
	 * File type extension.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_extension = 'php';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return void
	 */
	public function read() {
		$data = include_once $this->_path;
		
		if (is_array($data)) {
			$this->_config = $data;
		} else {
			throw new Exception('Reader failed to import PHP configuration.');
		}
	}

}