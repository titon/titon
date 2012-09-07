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
use titon\libs\engines\Engine;
use titon\libs\listeners\ListenerAbstract;
use titon\utility\Sanitize;

/**
 * OutputFilter is an event listener that cleans view data before it is rendered in the template.
 * Provides filters for HTML/entity escaping and XSS prevention (which also escapes).
 *
 * @package	titon.libs.listeners.security
 */
class OutputFilterListener extends ListenerAbstract {

	/**
	 * Configuration.
	 *
	 * 	escape		- (bool) Will escape HTML and entities
	 *	xss			- (bool) Will clean data to prevent XSS attacks
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'escape' => true,
		'xss' => true
	];

	/**
	 * Executed before the template gets rendered.
	 * Clean the engine data.
	 *
	 * @access public
	 * @param \titon\libs\engines\Engine $engine
	 * @return void
	 */
	public function preRender(Engine $engine) {
		$engine->set($this->clean($engine->get()));
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
				if ($this->config->xss) {
					$data[$key] = Sanitize::xss($value, ['strip' => false]);

				} else if ($this->config->escape) {
					$data[$key] = Sanitize::escape($value);
				}
			}
		}

		return $data;
	}

}