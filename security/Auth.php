<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\security;

use titon\Titon;
use titon\base\Base;
use titon\libs\controllers\Controller;
use titon\libs\exceptions\http\ForbiddenException;
use titon\libs\exceptions\http\UnauthorizedException;
use titon\libs\identifiers\Identifier;
use titon\libs\traits\Attachable;
use titon\net\Session;

/**
 * Allows for user authentication and authorization through the use of identifiers.
 *
 * @package	titon.security
 */
class Auth extends Base {
	use Attachable;

	/**
	 * Configuration.
	 *
	 * 	autoValidate	- (boolean) Will automatically authorize and authenticate a user in Controller.preProcess()
	 * 	autoRedirect	- (boolean) Will automatically redirect to the login page if auth fails, instead of throwing exceptions
	 * 	loginRedirect	- (array) The login URL route to redirect to
	 * 	logoutRedirect	- (array) The logout URL route to redirect to
	 * 	sessionKey		- (string) The session namespace
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'autoValidate' => true,
		'autoRedirect' => false,
		'loginRedirect' => array('module' => 'users', 'controller' => 'dashboard', 'action' => 'login'),
		'logoutRedirect' => array('module' => 'users', 'controller' => 'dashboard', 'action' => 'logout'),
		'sessionKey' => 'User'
	];

	/**
	 * Identifier instance.
	 *
	 * @access protected
	 * @var \titon\libs\identifiers\Identifier
	 */
	protected $_identifier;

	/**
	 * Session instance.
	 *
	 * @access protected
	 * @var \titon\net\Session
	 */
	protected $_session;

	/**
	 * Return a value from the auth session.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		if ($this->isAuthenticated()) {
			return $this->getSession()->get($this->config->sessionKey . '.' . $key);
		}

		return null;
	}

	/**
	 * Return the Identifier instance. If no instance exists, throw an exception.
	 *
	 * @access public
	 * @return \titon\libs\identifiers\Identifier
	 * @throws \titon\security\SecurityException
	 */
	public function getIdentifier() {
		if (!$this->_identifier) {
			throw new SecurityException('An identifier is required for authorization.');
		}

		return $this->_identifier;
	}

	/**
	 * Return the Session instance. If no instance exists, throw an exception.
	 *
	 * @access public
	 * @return \titon\net\Session
	 * @throws \titon\security\SecurityException
	 */
	public function getSession() {
		if (!$this->_session) {
			throw new SecurityException('A session instance is required for authorization.');
		}

		return $this->_session;
	}

	/**
	 * Attach the Request and Response objects.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('request', function() {
			return Titon::registry()->factory('titon\net\Request');
		});

		$this->attachObject('response', function() {
			return Titon::registry()->factory('titon\net\Response');
		});
	}

	/**
	 * Check to see if the user is authenticated.
	 *
	 * @access public
	 * @return boolean
	 * @throws \titon\security\SecurityException
	 */
	public function isAuthenticated() {
		return $this->getSession()->has($this->config->sessionKey);
	}

	/**
	 * Check to see if the user is authorized by validating against the passed object.
	 *
	 * @access public
	 * @param object $object
	 * @return boolean
	 */
	public function isAuthorized($object = null) {
		if (is_object($object) && method_exists($object, 'isAuthorized')) {
			return call_user_func(array($object, 'isAuthorized'), $this);
		}

		return true;
	}

	/**
	 * Authenticate the user using the Identifier. If successful, store the record in storage.
	 *
	 * @access public
	 * @return array
	 */
	public function login() {
		if ($user = $this->getIdentifier()->authenticate()) {
			$this->getSession()->set($this->config->sessionKey, $user);

			return $user;
		} else {
			$this->getIdentifier()->login();
		}

		return null;
	}

	/**
	 * Logout the user and redirect to the login action.
	 *
	 * @access public
	 * @return void
	 */
	public function logout() {
		$this->getSession()->remove($this->config->sessionKey);
		$this->getIdentifier()->logout();

		if ($this->config->autoRedirect) {
			$this->response->redirect($this->config->loginAction);
		}
	}

	/**
	 * Automatically authenticate and authorize from within a Controller.
	 *
	 * @access public
	 * @param \titon\libs\controllers\Controller $controller
	 * @return void
	 * @throws \titon\libs\exceptions\http\ForbiddenException
	 * @throws \titon\libs\exceptions\http\UnauthorizedException
	 */
	public function preProcess(Controller $controller) {
		if (!$this->config->autoValidate) {
			return;
		}

		if (!$this->isAuthenticated()) {
			if ($this->config->autoRedirect) {
				$this->response->redirect($this->config->loginRedirect, 403);
			} else {
				throw new ForbiddenException('Insufficient Access');
			}
		}

		if (!$this->isAuthorized($controller)) {
			if ($this->config->autoRedirect) {
				$this->response->redirect($this->config->loginRedirect, 401);
			} else {
				throw new UnauthorizedException('Unauthorized Access');
			}
		}
	}

	/**
	 * Set a value into the auth session.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return \titon\security\Auth
	 * @chainable
	 */
	public function set($key, $value) {
		if ($this->isAuthenticated()) {
			$this->getSession()->set($this->config->sessionKey . '.' . $key, $value);
		}

		return $this;
	}

	/**
	 * Set the Identifier instance.
	 *
	 * @access public
	 * @param \titon\libs\identifiers\Identifier $identifier
	 * @return \titon\security\Auth
	 * @chainable
	 */
	public function setIdentifier(Identifier $identifier) {
		$this->_identifier = $identifier;

		return $this;
	}

	/**
	 * Set the Session object.
	 *
	 * @access public
	 * @param \titon\net\Session $session
	 * @return \titon\security\Auth
	 * @chainable
	 */
	public function setSession(Session $session) {
		$this->_session = $session;

		return $this;
	}

	/**
	 * Return the currently authenticated user.
	 *
	 * @access public
	 * @return array
	 */
	public function user() {
		return $this->getSession()->get($this->config->sessionKey);
	}

}