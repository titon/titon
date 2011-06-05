<?php

namespace titon\system;

use \titon\base\Prototype;
use \titon\utility\Inflector;
use \titon\system\SystemException;

/**
 * View Class
 *
 * @package     Titon
 * @subpackage  Titon.System
 */
class View extends Prototype {

    /**
     * Constants for all the possible types of templates.
     *
     * @constant
     * @var int
     */
    const TYPE_TPL = 1;
    const TYPE_LAYOUT = 2;
    const TYPE_WRAPPER = 3;
    const TYPE_INCLUDE = 4;
    const TYPE_ERROR = 5;

    /**
	 * Default settings for view and template rendering. Can be overwritten in the Controller.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'type'		=> null,
		'data'		=> array(),
		'template'	=> array(),
		'render'	=> true,
		'layout'	=> 'default',
		'wrapper'	=> null,
		'error'		=> false
	);

    /**
     * Get the filepath for a type of template: layout, wrapper, view, error, include
     *
     * @access public
     * @param string $type
     * @param bool $absolute
     * @param string $path
     * @return string
     */
    public function buildPath($type = self::TYPE_TPL, $absolute = true, $path = null) {
        if (isset($this->_config['error']) && $this->_config['error'] == true) {
            $type = self::TYPE_ERROR;
        }
        
        switch ($type) {
            case self::TYPE_LAYOUT:
                $output = 'private'. DS .'layouts'. DS . $this->getConfig('layout') .'.tpl';
            break;

            case self::TYPE_WRAPPER:
                $output = 'private'. DS .'wrappers'. DS . $this->getConfig('wrapper') .'.tpl';
            break;

            case self::TYPE_INCLUDE:
                $output = 'private'. DS .'includes'. DS . $path;
            break;

            case self::TYPE_ERROR:
                $output = 'private'. DS .'errors'. DS . (string)$this->getConfig('template') .'.tpl';
            break;

            case self::TYPE_TPL:
            default:
                $template = $this->getConfig('template');
                $ext = null;

                if (empty($template['container'])) {
                    unset($template['container']);
                }

                if (isset($template['ext'])) {
                    $ext = $template['ext'];
                    unset($template['ext']);
                }
                
                $output  = 'public'. DS . implode(DS, $template);
                $output .= (empty($ext) ? '.tpl' : '.'. $ext .'.tpl');
            break;
        }

        if ($absolute == true) {
            $output = TEMPLATES . $output;
        }

        return $output;
    }

    /**
     * Check to see that the template file exists, else throw an error.
     *
     * @access public
     * @param string $type
     * @return bool
     */
    public function checkPath($type = self::TYPE_TPL) {
        $path = $this->buildPath($type);
        
        if (file_exists($path)) {
            return $path;
        }

        return false;
    }

    /**
	 * Triggered upon class instantiation, following __construct().
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
        $this->triggerCallback('initialize');
    }

    /**
     * Grab the page title if it has been set.
     *
     * @access public
     * @param string|array $separator
     * @return string
     */
	public function pageTitle($separator = ' - ') {
        $pageTitle = $this->getConfig('data.pageTitle');
        
        if (is_array($pageTitle)) {
            return implode($separator, $pageTitle);
        }

        return $pageTitle;
    }

    /**
	 * Triggered before a template is rendered by the engine.
	 *
	 * @access public
	 * @return void
	 */
    public function preRender() {
        $this->triggerHook('preRender');
    }

    /**
	 * Triggered after a template is rendered by the engine.
	 *
	 * @access public
	 * @return void
	 */
    public function postRender() {
        $this->triggerHook('postRender');
    }

    /**
	 * Custom method to overwrite and configure the view engine manually.
	 *
	 * @access public
	 * @param mixed $options
	 * @return void
	 */
	final public function render($options) {
		if (($options === false) || ($options === null)) {
			$this->_config['render'] = false;

		} else if (is_string($options)) {
			$this->_config['template']['action'] = $options;

		} else if (is_array($options)) {
			foreach ($options as $key => $value) {
				if ($key == 'template') {
                    if (is_array($value)) {
                        $this->_config['template'] = $value + $this->_config['template'];
                    } else {
                        $this->_config['template']['action'] = $value;
                    }
				} else if ($key != 'data') {
					$this->_config[$key] = $value;
				}
			}
		}
	}

    /**
     * @todo
     *
     * @access public
     * @return array
     */
    final public function run() {
        if (!$this->_config['render']) {
            return;
        }
        
        // Check for engine
        if (!$this->hasObject('Engine')) {
            throw new SystemException('You must have an Engine Module loaded to render the page.');
        }

        // Get content type automatically
        if (is_array($this->_config['template']) && isset($this->_config['template']['ext'])) {
            $this->_config['type'] = $this->_config['template']['ext'];
        } else if (empty($this->_config['type'])) {
            $this->_config['type'] = 'html';
        }

        // Output the response!
        $this->Response->contentType($this->_config['type']);
        $this->Response->contentBody($this->Engine->run());
        $this->Response->respond();
    }

    /**
	 * Set a variable to the view. The variable name will be inflected if it is invalid.
	 *
	 * @access public
	 * @param string|array $keys
	 * @param mixed $value
	 * @return bool
	 */
	final public function set($keys, $value = null) {
		if (!is_array($keys)) {
			$keys = array($keys => $value);
		}

        foreach ($keys as $key => $value) {
            $this->_config['data'][Inflector::variable($key)] = $value;
        }

		return true;
	}
    
}