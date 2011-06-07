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
	 *
	 * @access protected
	 * @var string
	 */
	protected $_extension = 'yaml';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 */
	public function read($path) {
		if (!extension_loaded('yaml')) {
			throw new ReaderException('YAML PECL extension must be installed to use the YamlReader.');
		}
		
		$data = yaml_parse_file($path);

		if (is_array($data)) {
			$this->configure($data);
		} else {
			throw new ReaderException('Reader failed to parse YAML configuration.');
		}
	}

}