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
 * A reader that loads its configuration from an XML file.
 * Must have the SimpleXML module installed.
 *
 * @package		Titon
 * @subpackage	Core.Readers
 */
class XmlReader extends ReaderAbstract {

	/**
	 * Include the file and parse using SimpleXML
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 */
	public function __construct($path) {
		$data = simplexml_load_file($path);

		if ($data !== false) {
			$this->_config = $data;
		} else {
			throw new Exception('Reader failed to parse XML configuration.');
		}
	}

	/**
	 * Convert a SimpleXML object into an array.
	 *
	 * @access public
	 * @param object $xml
	 * @return array
	 */
	public function toArray($xml = null) {
		if ($xml === null) {
			$xml = $this->_config;
		}

		if ($xml->count() === 0) {
			return array((string)$xml);
		}

		$array = array();
		
		foreach ($xml->children() as $element => $node) {
			$totalElement = count($xml->{$element});

			if (!isset($array[$element])) {
				$array[$element] = array();
			}

			// Has attributes
			if ($attributes = $node->attributes()) {
				$data = (count($node) > 0) ? $this->toArray($node) : (string)$node;
				$attr = array();

				foreach ($attributes as $key => $value) {
					$attr[$key] = (string)$value;
				}

				if ($totalElement > 1) {
					$array[$element][] = $data;
				} else {
					$array[$element] = $data;
				}

				$array[$element]['_attributes'] = $attr;

			// Just a value
			} else {
				if ($totalElement > 1) {
					$array[$element][] = $this->toArray($node);
				} else {
					$array[$element] = $this->toArray($node);
				}
			}
		}

		return $array;
	}

}