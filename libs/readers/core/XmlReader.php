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
		if ($xml === false) {
			return false;
		}

		if (is_string($xml)) {
			$xml = @simplexml_load_string($xml);
		}

		if ($xml->count() <= 0) {
			return (string) $xml;
		}

		$array = [];

		foreach ($xml->children() as $element => $node) {
			$data = [];

			if (!isset($array[$element])) {
				$array[$element] = "";
			}

			if (!$node->attributes()) {
				$data = $this->toArray($node);

			} else {
				if ($node->count() > 0) {
					$data += $this->toArray($node);
					$children = true;
				} else {
					$data['value'] = trim((string) $node);
					$children = false;
				}

				$attributes = array();

				foreach ($node->attributes() as $attr => $value) {
					$attributes[$attr] = (string) $value;
				}

				// Autobox value if only the type attribute exists
				if (count($attributes) === 1 && isset($attributes['type'])) {
					switch ($attributes['type']) {
						case 'boolean':
							if ($data['value'] === 'true') {
								$data = true;
							} else if ($data['value'] === 'false') {
								$data = false;
							} else {
								$data = (bool) $data['value'];
							}
						break;
						case 'integer':
							$data = (int) $data['value'];
						break;
						case 'array':
							if (!$children && empty($data['value'])) {
								$data = [];
							}
						break;
					}

				} else {
					$data += $attributes;
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