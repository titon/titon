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
 * A reader that loads its configuration from an INI file.
 *
 * @package	titon.source.core.readers
 * @uses	titon\source\log\Exception
 * 
 * @link	http://php.net/parse_ini_file
 */
class IniReader extends ReaderAbstract {

	/**
	 * File type extension.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_extension = 'ini';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return void
	 */
	public function read() {
		$data = parse_ini_file($this->_path, true, INI_SCANNER_NORMAL);

		if (is_array($data)) {
			$this->_config = $data;
		} else {
			throw new Exception('Reader failed to parse INI configuration.');
		}
	}

}