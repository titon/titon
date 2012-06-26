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

/**
 * A file reader that parses gettext PO files.
 *
 * @package	titon.libs.readers.gettext
 * @uses	titon\libs\readers\ReaderException
 */
class PoReader extends ReaderAbstract {

	/**
	 * File type extension.
	 */
	const EXT = 'po';

	/**
	 * Parse the file contents.
	 *
	 * @access public
	 * @return array
	 */
	public function parse() {
		$lines = file($this->_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$data = [];
		$key = '';
		$value = '';
		$plural = false;

		foreach ($lines as $line) {
			// Comment or empty line
			if ($line[0] === '#' || empty($line)) {
				continue;

			// Multiline value
			} else if ($line[0] === '"') {
				$value .= "\n" . self::dequote($line);

			// Key
			} else if (strpos($line, 'msgid') === 0) {
				// Save the previous value
				if ($key !== '' && !empty($value)) {
					$data[$key] = $value;
					$value = '';
				}

				$key = self::dequote($line);

			// Message
			} else if (strpos($line, 'msgstr') === 0) {
				// msgstr[n]
				if ($line[6] === '[') {
					$val = self::dequote($line);

					if ($plural) {
						$value[] = $val;
					} else {
						$value = [$val];
						$plural = true;
					}

				// msgstr
				} else {
					$value = self::dequote($line);
					$plural = false;
				}
			}
		}

		// Grab the last value
		if ($key !== '' && !empty($value)) {
			$data[$key] = $value;
		}

		return $data;
	}

	/**
	 * Remove the quotes from a message string.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function dequote($string) {
		return substr(substr($string, strpos($string, '"')), 1, -1);
	}

}