<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\engines\core;

use \titon\libs\engines\EngineAbstract;
use \titon\libs\engines\EngineException;

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
	*/
	public function open($path, array $variables = array()) {
		$path = $this->buildPath(self::TYPE_INCLUDE, $path);
		$variables = $variables + $this->data();

		if (!$path) {
			throw new EngineException(sprintf('The include template %s does not exist.', basename($path)));
		}

		return $this->render($path, $variables);
	}

	/**
	* Primary method to render a single view template.
	*
	* @access public
	* @param string $path
	* @param array $variables
	* @return void
	*/
	public function render($path, array $variables = array()) {
		if (!empty($variables)) {
			extract($variables, EXTR_SKIP);	
		}

		ob_start();

		include $path;

		return ob_get_clean();
	}

	/**
	* Begins the staged rendering process. First stage, the system must render the template based on the module, 
	* controller and action path. Second stage, wrap the first template in any wrappers. Third stage, 
	* wrap the current template ouput with the layout. Return the final result.
	*
	* @access public
	* @return void
	*/
	public function run() {
		$config = $this->config();

		if (!$config['render']) {
			return;

		} else if ($this->_rendered) {
			return $this->content();
		}

		// Render the template, layout and wrappers
		$data = $this->data();

		if ($path = $this->buildPath(self::TYPE_TPL)) {
			$this->_content = $this->render($path, $data);
		} else {
			throw new EngineException(sprintf('View template %s does not exist.', str_replace(ROOT, '', $path)));
		}

		if (!empty($config['wrapper'])) {
			if ($wrapper = $this->buildPath(self::TYPE_WRAPPER)) {
				$this->_content = $this->render($wrapper, $data);
			}
		}

		// Render layout
		if (!empty($config['layout'])) {
			if ($layout = $this->buildPath(self::TYPE_LAYOUT)) {
				$this->_content = $this->render($layout, $data);
			}
		}

		$this->_rendered = true;

		return $this->content();
	}

}