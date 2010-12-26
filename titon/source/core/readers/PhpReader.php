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
 * @link	http://php.net/manual/en/function.include.php
 */
class PhpReader extends ReaderAbstract {

	/**
	 * Include the file directly into the configuration.
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 */
	public function __construct($path) {
		$data = include_once $path;
		
		if (is_array($data)) {
			$this->_config = $data;
		} else {
			throw new Exception('Reader failed to import PHP configuration.');
		}
	}

}