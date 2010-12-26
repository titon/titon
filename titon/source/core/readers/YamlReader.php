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
 * A reader that loads its configuration from an YAML file.
 * Must have the PECL YAML module installed.
 *
 * @package	titon.source.core.readers
 * @link	http://php.net/yaml
 */
class YamlReader extends ReaderAbstract {

	/**
	 * Include the file and parse.
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 */
	public function __construct($path) {
		if (!function_exists('yaml_parse_file')) {
			throw new Exception('YAML PECL extension must be installed to use the YamlReader.');

		} else {
			$data = yaml_parse_file($path);

			if (is_array($data)) {
				$this->_config = $data;
			} else {
				throw new Exception('Reader failed to parse YAML configuration.');
			}
		}
	}

}