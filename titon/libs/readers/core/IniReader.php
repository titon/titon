<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\readers\core;

use \titon\libs\readers\ReaderAbstract;
use \titon\libs\readers\ReaderException;
use \titon\utility\Set;

/**
 * A reader that loads its configuration from an INI file.
 *
 * @package	titon.libs.readers.core
 * @uses	titon\libs\readers\ReaderException
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
	 * @param string $path
	 * @return void
	 */
	public function read($path) {
		$data = parse_ini_file($path, true, INI_SCANNER_NORMAL);

		if (is_array($data)) {
			$this->configure(Set::expand($data));
		} else {
			throw new ReaderException('Reader failed to parse INI configuration.');
		}
	}

}