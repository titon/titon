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
	 */
	const EXT = 'json';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return void
	 * @throws \titon\libs\readers\ReaderException
	 */
	public function parseFile() {
		$data = @json_decode(file_get_contents($this->getPath()), true);

		if (is_array($data)) {
			$this->configure($data);
		} else {
			throw new ReaderException('Reader failed to decode JSON configuration.');
		}
	}

}