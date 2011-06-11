<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */
 
namespace titon\system;

use \titon\Titon;
use \titon\base\Prototype;
use \titon\system\Action;
use \titon\system\View;
use \titon\system\SystemException;
use \titon\utility\Inflector;

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
 * @package	titon.system
 * @uses	titon\Titon
 * @uses	titon\utility\Inflector
 */
class Controller extends Prototype {

	/**
	 * View object.
	 *
	 * @access public
	 * @var View
	 */
	public $view;

	/**
	 * Configuration.
	 * 
	 *	module - Current application module.
	 *	controller - Current controller within the module.
	 *	action - Current action within the controller.
	 *	ext - The extension within the address bar, and what content-type to render the page as.
	 *	args - Action arguments.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'module' => '',
		'controller' => '',
		'action' => '',
		'ext' => '',
		'args' => array()
	);

	/**
	 * Trigger a custom Action class.
	 *
	 * @access public
	 * @param Action $Action
	 * @return void
	 * @final
	 */
	final public function action(Action $action) {
		$action->setController($this);
		$action->run();

		return;
	}

	/**
	 * Dispatch the request to the correct controller action. Checks to see if the action exists and is not protected.
	 *
	 * @access public
	 * @param string $action
	 * @param array $args
	 * @return mixed
	 * @final
	 */
	final public function dispatch($action = null, array $args = array()) {
		if (empty($action)) {
			$action = $this->_config['action'];
		}

		if (empty($args)) {
			$args = $this->_config['args'];
		}
		
		// Do not include the base controller methods
		$methods = array_diff(get_class_methods($this), get_class_methods(__CLASS__));

		if (!in_array($action, $methods)) {
			throw new SystemException('Your action does not exist, or is not public, or is found within the parent Controller.');
		}

		return call_user_func_array(array($this, $action), $args);
	}

	/**
	 * Functionality to throw up an error page (like a 404). The error template is derived from the $action passed.
	 *
	 * @access public
	 * @param string $action
	 * @param array $args
	 * @return void
	 */
	public function error($action, array $args = array()) {
		if (empty($args['pageTitle'])) {
			if (is_numeric($action)) {
				$args['pageTitle'] = $action;

				$title = $this->response->statusCodes($action);

				if ($title !== null) {
					$args['pageTitle'] .= ' - '. $title;

					$this->response->status($action);
				}
			} else {
				$args['pageTitle'] = Inflector::normalize($action);
			}
		}

		$args['referrer'] = $this->request->referrer();
		$args['url'] = Titon::router()->segment(true);

		$this->view->set($args);
		$this->view->render(array(
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
	 * @final
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
	 * @final
	 */
	final public function forward($action, array $args = array()) {
		$this->view->render($action);
		$this->configure(array('action' => $action));
		$this->dispatch($action, $args);
	}

	/**
	 * Triggered before the Controller processes the requested Action.
	 *
	 * @access public
	 * @return void
	 */
	public function preProcess() {
		$this->triggerObjects('preProcess');
	}

	/**
	 * Triggered after the Action processes, but before the View renders.
	 *
	 * @access public
	 * @return void
	 */
	public function postProcess() {
		$this->triggerObjects('postProcess');
	}

	/**
	 * Configure the Controller and store the View object.
	 *
	 * @access public
	 * @param View $view
	 * @return void
	 * @final
	 */
	final public function setView(View $view) {
		$config = $this->config();
		$template = array(
			'module' => $config['module'],
			'controller' => $config['controller'],
			'action' => $config['action']
		);

		if (!empty($config['ext'])) {
			$template['ext'] = $config['ext'];
		}

		$this->view = $view;
		$this->view->render(array('template' => $template));
	}

}
