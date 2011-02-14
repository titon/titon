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
 * A reader that loads its configuration from an XML file.
 * Must have the SimpleXML module installed.
 *
 * @package	titon.source.core.readers
 * @uses	titon\source\log\Exception
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
	 * @return void
	 */
	public function read() {
		$data = @simplexml_load_file($this->_path);

		if ($data !== false) {
			$this->configure($data);
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
			return (string)$xml;
		}

		$array = array();

		// Has children
		foreach ($xml->children() as $element => $node) {
			$total = count($xml->{$element});
			$attributes = $node->attributes();

			// Has attributes
			if (count($attributes) > 0) {
				$attr = array();
				foreach ($attributes as $key => $value) {
					$attr[$key] = (string)$value;
				}

				// Single node
				if ($node->count() === 0) {
					$array[$element] = array(
						'value' => (string)$node,
						'_attributes' => $attr
					);

				// Multiple nodes
				} else {
					if ($total > 1) {
						$array[$element][] = $this->toArray($node);
					} else {
						$array[$element] = $this->toArray($node);
					}

					$array[$element]['_attributes'] = $attr;
				}

			// Just a value
			} else {
				if ($total > 1) {
					$array[$element][] = $this->toArray($node);
				} else {
					$array[$element] = $this->toArray($node);
				}
			}
		}

		return $array;
	}

}