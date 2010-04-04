<?php
/**
 * The Controller (MVC) acts as the median between the request and response within the dispatch cycle.
 * It splits up its responsibility into multiple Actions, where each Action deals with specific business logic.
 * The logical data is retrieved from a Model (database or logic entity) or a PHP super global (POST, GET).
 *
 * The Controller receives an instance of the View object allowing the Controller to set data to the view,
 * overwrite the View and Engine configuration, attach helpers, etc.
 *
 * Furthermore, the Controller inherits all functionality from the Prototype class, allowing you to attach
 * external classes to use their functionality and trigger specific callbacks.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */
 
namespace titon\system;

use \titon\core\Prototype;
use \titon\log\Exception;
use \titon\router\Router;
use \titon\system\Action;
use \titon\utility\Inflector;
use \titon\utility\Set;

/**
 * Controller Class
 *
 * @package     Titon
 * @subpackage  Titon.System
 */
class Controller extends Prototype {

    /**
     * View object.
     *
     * @access protected
     * @var View
     */
    protected $View;

	/**
	 * Configure the Controller and store the View object.
	 *
	 * @access public
	 * @param array $config
     * @param View $View
	 * @return void
	 */
	final public function __construct(array $config, View $View) {
        parent::__construct($config);

        // Get the routed path and save it to the view
        $template = array(
            'container' => $this->_config['container'],
            'controller' => $this->_config['controller'],
            'action' => $this->_config['action']
        );

        if (!empty($this->_config['ext'])) {
            $template['ext'] = $this->_config['ext'];
        }

        // Setup the View object
        $this->View = $View;
        $this->View->render(array('template' => $template));
	}

	/**
	 * Trigger a custom Action class.
	 *
	 * @access public
	 * @param Action $Action
	 * @return void
	 */
	final public function action(Action $Action) {
		$Action->setController($this);
		$Action->run();

		return;
	}

    /**
     * Dispatch the request to the correct controller action. Checks to see if the action exists and is not protected.
     *
     * @access public
     * @param string $action
     * @param array $args
     * @return mixed
     */
    final public function dispatch($action = null, array $args = array()) {
        if (empty($action)) {
            $action = $this->_config['action'];
        }
        
        if (empty($args)) {
            $args = $this->_config['args'];
        }
        
        if ((!method_exists($this, $action)) || (substr($action, 0, 1) == '_') || (in_array($action, get_class_methods('\app\AppController')))) {
            throw new Exception('Your action does not exist, or is not public, or is found within the parent Controller.');
        }

        return call_user_func_array(array(&$this, $action), $args);
    }

	/**
	 * Allows you to throw up an error page. The error template is derived from the $action passed.
	 *
	 * @access public
	 * @param string $action
	 * @param array $args
	 * @return void
	 */
	public function error($action, array $args = array()) {
		if (!isset($args['pageTitle'])) {
			switch ($action) {
				case is_numeric($action):
					$args['pageTitle'] = $action;

					if ($title = $this->Response->statusCode($action)) {
						$args['pageTitle'] .= ' - '. $title;

						$this->Response->status($action);
					}
				break;
				default:
					$args['pageTitle'] = Inflector::normalize($action);
				break;
			}
		}

		// Build arguments
		$args['referrer'] = $this->Request->referrer();
		$args['url'] = 'todo'; //Router::construct(Router::current());

		$this->View->set($args);
		$this->View->configure(array(
			'error' => true,
			'layout' => 'error',
			'template' => $action
		));

		return;
	}

    /**
     * Set the flash message to be used in the view. Will use the Session class if its loaded.
     *
     * @access public
     * @param mixed $message
     * @return void
     */
    final public function flash($message) {
        if ($this->hasObject('Session')) {
            $this->Session->set('App.flash', $message);
        } else {
            $_SESSION = Set::insert($_SESSION, 'App.flash', $message);
        }
    }

	/**
	 * Forward the current request to a new action, instead of doing an additional HTTP request.
	 *
	 * @access public
	 * @param string $action
     * @param array $args
	 * @return mixed
	 */
	final public function forward($action, array $args = array()) {
        $this->View->render($action);
        $this->configure(array('action' => $action));
        $this->dispatch($action, $args);
	}

    /**
	 * Triggered upon class instantiation, following __construct().
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
        $this->_callback('initialize');
    }

    /**
	 * Triggered before the Controller processes the requested Action.
	 *
	 * @access public
	 * @return void
	 */
	public function preProcess() {
        $this->_callback('preProcess');
    }

	/**
	 * Triggered after the Action processes, but before the View renders.
	 *
	 * @access public
	 * @return void
	 */
	public function postProcess() {
        $this->_callback('postProcess');
    }
	
}
