<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */
 
namespace titon\libs\bundles\messages\gettext;

use titon\libs\bundles\messages\MessageBundleAbstract;

/**
 * Bundle used for loading gettext PO files.
 *
 * @package	titon.libs.bundles.messages.gettext
 */
class PoMessageBundle extends MessageBundleAbstract {

	/**
	 * Bundle file extension.
	 */
	const EXT = 'po';

	/**
	 * Define the locations for the message resources.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->findBundle(array(
			APP_MODULES . '{module}/resources/messages/{bundle}/LC_MESSAGES/',
			APP_RESOURCES . 'messages/{bundle}/LC_MESSAGES/'
		));
	}

	/**
	 * Parse the file at the given path and return the result.
	 *
	 * @access public
	 * @param string $filename
	 * @return array
	 */
	public function parseFile($filename) {
		$lines = file($this->getPath() . $filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$data = array();
		$key = '';
		$value = '';
		$plural = false;

		foreach ($lines as $line) {
			// Comment or empty line
			if ($line[0] == '#' || empty($line)) {
				continue;

			// Multiline value
			} else if ($line[0] == '"') {
				$value .= "\n" . $this->_dequote($line);

			// Key
			} else if (strpos($line, 'msgid') === 0) {
				// Save the previous value
				if ($key != '' && !empty($value)) {
					$data[$key] = $value;
					$value = '';
				}

				$key = $this->_dequote($line);

			// Message
			} else if (strpos($line, 'msgstr') === 0) {
				// msgstr[n]
				if ($line[6] == '[') {
					$val = $this->_dequote($line);

					if ($plural) {
						$value[] = $val;
					} else {
						$value = array($val);
						$plural = true;
					}
				// msgstr
				} else {
					$value = $this->_dequote($line);
					$plural = false;
				}
			}
		}

		// Grab the last value
		if ($key != '' && !empty($value)) {
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
	 */
	protected function _dequote($string) {
		return substr(substr($string, strpos($string, '"')), 1, -1);
	}

}
