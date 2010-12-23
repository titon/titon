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
 * @package		Titon
 * @subpackage	Core.Readers
 */
class IniReader extends ReaderAbstract {

	/**
	 * Grab the file contents and parse the INI file.
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 */
	public function __construct($path) {
		$data = parse_ini_file($path, true, INI_SCANNER_NORMAL);

		if (is_array($data)) {
			$this->_config = $data;
		} else {
			throw new Exception('Reader failed to parse INI configuration.');
		}
	}

}