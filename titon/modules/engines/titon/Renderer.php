<?php
/**
 * View in the MVC architecture. This View engine acts as the framework specific, and default rendering engine.
 * Uses simple PHP and output buffering mechanisms to render .tpl files that contain PHP code.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\engines\titon;

use \titon\log\Exception;
use \titon\modules\engines\EngineAbstract;
use \titon\system\View;

/**
 * View Engine
 *
 * @package     Titon
 * @subpackage  Titon.Modules.Engines
 */
class Renderer extends EngineAbstract {

    /**
     * Opens and renders a partial view element within the current document.
     * Can be called within other view templates.
     *
     * @access public
     * @param string $path
     * @param array $variables
     * @return string
     */
    public function open($path, array $variables = array()) {
        $path = trim(str_replace(array('/', '\\'), DS, $path), DS);

        if (substr($path, -4) != '.tpl') {
            $path .= '.tpl';
        }

        $path = $this->View->buildPath(self::TYPE_INCLUDE, true, $path);

        if (!file_exists($path)) {
            throw new Exception(sprintf('The include template %s does not exist.', basename($path)));
        }

        return $this->__render($path, $variables);
    }

    /**
     * Renders the inner content templates, applies a wrapper if it exists and renders the layout.
     * The inner content will be rendered if the content() method exists in the tpl.
     * Finally, it will output the correct HTTP headers depending on the "type" property in the config.
     *
     * @access public
     * @return string
     */
	public function run() {
        if ($this->_rendered === true) {
            return;
        }

        $data = $this->View->getConfig('data');
        $template = $this->View->buildPath(View::TYPE_TPL);

        // Render view
        if ($this->View->checkPath(View::TYPE_TPL)) {
            $this->_content = $this->__render($template, $data);
            $this->_rendered = true;
        } else {
            throw new Exception(sprintf('View template %s does not exist.', str_replace(TEMPLATES, '', $template)));
        }

        // Render wrapper
        if (!empty($this->_wrapper) && $this->_wrapped == false) {
            if ($wrapper = $this->View->checkPath(View::TYPE_WRAPPER)) {
                $this->_content = $this->__render($wrapper, $data);
                $this->_wrapped = true;
            }
        }

        // Render layout
        if (!empty($this->_layout)) {
            if ($layout = $this->View->checkPath(View::TYPE_LAYOUT)) {
                $this->_content = $this->__render($layout, $data);
            }
        }

        return $this->_content;
    }

    /**
     * Primary function to render a template. It enables output buffering, extracts the variables, includes the template, and returns the output.
     *
     * @access private
     * @param string $tplPath
     * @param array $variables
     * @return mixed
     */
    private function __render($tplPath, array $variables = array()) {
        extract($variables, EXTR_SKIP);
        ob_start();

        include $tplPath;
        $output = ob_get_clean();

        return $output;
    }

}
