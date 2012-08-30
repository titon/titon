<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\engines\core;

use titon\libs\engines\EngineAbstract;

/**
 * Standard engine used for rendering views using pure PHP code.
 *
 * @package	titon.libs.engines.core
 */
class ViewEngine extends EngineAbstract {

	/**
	 * Opens and renders a partial view element within the current document.
	 *
	 * @access public
	 * @param string $path
	 * @param array $variables
	 * @return string
	 * @throws \titon\libs\engines\EngineException
	 */
	public function open($path, array $variables = []) {
		return $this->render($this->buildPath(self::ELEMENT, $path), $variables + $this->get());
	}

	/**
	 * Primary method to render a single view template.
	 *
	 * @access public
	 * @param string $path
	 * @param array $variables
	 * @return string
	 */
	public function render($path, array $variables = []) {
		if ($variables) {
			extract($variables, EXTR_SKIP);
		}

		ob_start();

		include $path;

		return ob_get_clean();
	}

	/**
	 * Begins the staged rendering process. First stage, the system must render the template based on the module,
	 * controller and action path. Second stage, wrap the first template in any wrappers. Third stage,
	 * wrap the current template output with the layout. Return the final result.
	 *
	 * @access public
	 * @param boolean $cache
	 * @return string
	 * @throws \titon\libs\engines\EngineException
	 */
	public function run($cache = true) {
		$config = $this->config->get();

		if (!$config['render'] || ($cache && $this->_rendered)) {
			return $this->_content;
		}

		// Render the template, layout and wrappers
		$data = $this->get();
		$renders = [
			self::VIEW => 'template',
			self::WRAPPER => 'wrapper',
			self::LAYOUT => 'layout'
		];

		foreach ($renders as $type => $render) {
			if (empty($config[$render])) {
				continue;
			}

			// Only if the file exists, in case wrapper or layout isn't being used
			if ($path = $this->buildPath($type)) {
				$this->_content = $this->render($path, $data);
			}
		}

		$this->_rendered = true;

		return $this->_content;
	}

}