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
 * A reader that loads its configuration from an YAML file.
 * Must have the PECL YAML module installed.
 *
 * @package	titon.source.core.readers
 * @uses	titon\source\log\Exception
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
	 * @return void
	 */
	public function read() {
		if (!function_exists('yaml_parse_file')) {
			throw new Exception('YAML PECL extension must be installed to use the YamlReader.');

		} else {
			$data = yaml_parse_file($this->_path);

			if (is_array($data)) {
				$this->configure($data);
			} else {
				throw new Exception('Reader failed to parse YAML configuration.');
			}
		}
	}

}