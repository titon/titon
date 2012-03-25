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
 * A reader that loads its configuration from an YAML file.
 * Must have the PECL YAML module installed.
 *
 * @package	titon.libs.readers.core
 * @uses	titon\libs\readers\ReaderException
 * 
 * @link	http://php.net/yaml
 */
class YamlReader extends ReaderAbstract {

	/**
	 * File type extension.
	 */
	const EXT = 'yaml';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return void
	 * @throws \titon\libs\readers\ReaderException
	 */
	public function parseFile() {
		if (!extension_loaded('yaml')) {
			throw new ReaderException('YAML PECL extension must be installed to use the YamlReader.');
		}
		
		$data = yaml_parse_file($this->getPath());

		if (is_array($data)) {
			$this->configure($data);
		} else {
			throw new ReaderException('Reader failed to parse YAML configuration.');
		}
	}

}