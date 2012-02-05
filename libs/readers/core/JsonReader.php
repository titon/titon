<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\readers\core;

use \titon\libs\readers\ReaderAbstract;
use \titon\libs\readers\ReaderException;

/**
 * A reader that loads its configuration from a JSON file.
 * Must have the JSON module installed.
 *
 * @package	titon.libs.readers.core
 * @uses	titon\libs\readers\ReaderException
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
	 * @param string $path
	 * @return void
	 * @throws ReaderException
	 */
	public function read($path) {
		$data = @json_decode(file_get_contents($path), true);

		if (is_array($data)) {
			$this->configure($data);
		} else {
			throw new ReaderException('Reader failed to decode JSON configuration.');
		}
	}

}