<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\readers\core;

use titon\libs\readers\ReaderAbstract;

/**
 * A reader that loads its configuration from an XML file.
 * Must have the SimpleXML module installed.
 *
 * @package	titon.libs.readers.core
 * @uses	titon\libs\readers\ReaderException
 *
 * @link	http://php.net/simplexml
 */
class XmlReader extends ReaderAbstract {

	/**
	 * File type extension.
	 */
	const EXT = 'xml';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return array
	 */
	public function parse() {
		return $this->toArray(@simplexml_load_file($this->_path));
	}

	/**
	 * Convert a SimpleXML object into an array.
	 *
	 * @access public
	 * @param mixed $xml
	 * @return array
	 */
	public function toArray($xml) {
		if (!$xml) {
			return false;
		}

		if (is_string($xml)) {
			$xml = @simplexml_load_string($xml);
		}

		if ($xml->count() <= 0) {
			return (string) $xml;
		}

		$array = array();

		foreach ($xml->children() as $element => $node) {
			$data = array();

			if (!isset($array[$element])) {
				$array[$element] = "";
			}

			if (!$node->attributes()) {
				$data = $this->toArray($node);

			} else {
				if ($node->count() > 0) {
					$data = $data + $this->toArray($node);
				} else {
					$data['value'] = (string) $node;
				}

				foreach ($node->attributes() as $attr => $value) {
					$data[$attr] = (string) $value;
				}
			}

			if (count($xml->{$element}) > 1) {
				$array[$element][] = $data;
			} else {
				$array[$element] = $data;
			}
		}

		return $array;
	}

}