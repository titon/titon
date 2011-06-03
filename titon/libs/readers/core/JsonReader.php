<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\library\readers\core;

use \titon\source\library\readers\ReaderAbstract;
use \titon\source\log\Exception;

/**
 * A reader that loads its configuration from a JSON file.
 * Must have the JSON module installed.
 *
 * @package	titon.source.core.readers
 * @uses	titon\source\log\Exception
 * 
 * @link	http://php.net/json_decode
 */
class JsonReader extends ReaderAbstract {

	/**
	 * File type extension.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_extension = 'json';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return void
	 */
	public function read() {
		$data = json_decode(file_get_contents($this->_path), true);

		if (is_array($data)) {
			$this->configure($data);
		} else {
			throw new Exception('Reader failed to decode JSON configuration.');
		}
	}

}