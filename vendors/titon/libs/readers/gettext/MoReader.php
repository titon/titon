<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\readers\gettext;

use titon\libs\readers\ReaderAbstract;
use titon\libs\readers\ReaderException;

/**
 * A file reader that parses gettext MO files.
 *
 * @package	titon.libs.readers.gettext
 * @uses	titon\libs\readers\ReaderException
 */
class MoReader extends ReaderAbstract {

	/**
	 * File type extension.
	 */
	const EXT = 'mo';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return array
	 * @throws titon\libs\readers\ReaderException
	 */
	public function parse() {
		throw new ReaderException('MoReader::parse() has not yet been implemented.');
	}

}