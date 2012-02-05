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
	 *
	 * @access protected
	 * @var string
	 */
	protected $_extension = 'xml';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 * @throws ReaderException
	 */
	public function read($path) {
		$data = @simplexml_load_file($path);

		if ($data !== false) {
			$this->configure($this->toArray($data));
		} else {
			throw new ReaderException('Reader failed to parse XML configuration.');
		}
	}

	/**
	 * Convert a SimpleXML object into an array.
	 *
	 * @access public
	 * @param object $xml
	 * @return array
	 */
	public function toArray($xml) {
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