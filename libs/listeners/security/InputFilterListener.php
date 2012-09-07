<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\listeners\security;

use titon\Titon;
use titon\libs\listeners\ListenerAbstract;
use titon\utility\Sanitize;

/**
 * InputFilter is an event listener that cleans $_GET, $_POST or $_COOKIE globals before the primary startup of the framework.
 * Provides filters for HTML/entity escaping, newline and whitespace truncation, as well as support for custom callbacks.
 *
 * @package	titon.libs.listeners.security
 */
class InputFilterListener extends ListenerAbstract {

	/**
	 * Configuration.
	 *
	 * 	clean		- (array) List of globals to clean
	 * 	escape		- (bool) Will escape HTML and entities
	 * 	newlines	- (bool) Will remove extraneous CR and LF
	 * 	whitespace	- (bool) Will remove extraneous spaces and tabs
	 * 	callback	- (callable) Custom callback function to execute
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'clean' => ['get', 'post'],
		'escape' => true,
		'newlines' => false,
		'whitespace' => false,
		'callback' => null
	];

	/**
	 * Executed after kernel startup.
	 * Loop over each global and clean it.
	 *
	 * @access public
	 * @return void
	 */
	public function startup() {
		if ($clean = $this->config->clean) {
			if (in_array('get', $clean)) {
				$_GET = $this->clean($_GET);
			}

			if (in_array('post', $clean)) {
				$_POST = $this->clean($_POST);
			}

			if (in_array('cookie', $clean)) {
				$_COOKIE = $this->clean($_COOKIE);
			}
		}
	}

	/**
	 * Loop through the data and apply cleaning filters.
	 *
	 * @access public
	 * @param array $data
	 * @return array
	 */
	public function clean($data) {
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$data[$key] = $this->clean($value);

			} else if (is_string($value)) {
				if ($this->config->escape) {
					$data[$key] = Sanitize::escape($value);
				}

				if ($this->config->newlines) {
					$data[$key] = Sanitize::newlines($value);
				}

				if ($this->config->whitespace) {
					$data[$key] = Sanitize::whitespace($value);
				}

				if ($callback = $this->config->callback) {
					if (is_callable($callback)) {
						$data[$key] = call_user_func($callback, $value);
					}
				}
			}
		}

		return $data;
	}

}