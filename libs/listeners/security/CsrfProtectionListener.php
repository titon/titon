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
use titon\libs\dispatchers\Dispatcher;
use titon\libs\exceptions\http\ForbiddenException;
use titon\libs\listeners\ListenerAbstract;
use titon\libs\traits\Attachable;
use titon\utility\Time;

/**
 * Attempts to protect against CSRF (cross-site request forgery) attacks by validating
 * tokens within POST/GET data against generated tokens in the session.
 *
 * @package	titon.libs.listeners.security
 */
class CsrfProtectionListener extends ListenerAbstract {
	use Attachable;

	/**
	 * Configuration.
	 *
	 * 	field			- The name of the form input field
	 * 	expires			- Length of tokens before they are considered invalid
	 * 	validatePost	- Validate the token using POST data
	 * 	validateGet		- Validate the token using GET data
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'field' => 'csrf',
		'expires' => '+5 minutes',
		'validatePost' => true,
		'validateGet' => true,
	];

	/**
	 * The current (and next) request token.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_currentToken;

	/**
	 * Expiration time of the previous token.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_expires;

	/**
	 * The previous request token.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_previousToken;

	/**
	 * Initialize dependencies and session.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('session', function() {
			return Titon::registry()->factory('titon\net\Session');
		});

		$this->attachObject('request', function() {
			return Titon::registry()->factory('titon\net\Request');
		});
	}

	/**
	 * Executed after kernel startup.
	 * Store the previous token and generate a new token.
	 *
	 * @access public
	 * @return void
	 */
	public function startup() {
		$this->_previousToken = $this->session->get('Security.csrf.token');
		$this->_expires = $this->session->get('Security.csrf.expires');

		$token = sha1(microtime(true) . mt_rand());

		$this->session->set('Security.csrf', [
			'token' => $token,
			'field' => $this->config->field,
			'expires' => Time::toUnix($this->config->expires)
		]);

		$this->_currentToken = $token;
	}

	/**
	 * Executed at the beginning of the dispatch cycle.
	 * Validate the token in the POST/GET against the token in the session.
	 *
	 * @access public
	 * @param \titon\libs\dispatchers\Dispatcher $dispatcher
	 * @return void
	 * @throws \titon\libs\exceptions\http\ForbiddenException
	 */
	public function preDispatch(Dispatcher $dispatcher) {
		$field = $this->config->field;
		$request = $this->request;

		if ($request->isPost() && $this->config->validatePost) {
			$token = isset($request->post[$field]) ? $request->post[$field] : null;

		} else if ($request->isGet() && $this->config->validateGet) {
			$token = isset($request->get[$field]) ? $request->get[$field] : null;

		} else {
			return;
		}

		if (!$this->_previousToken) {
			throw new ForbiddenException('CSRF attack detected; token does not exist.');

		} else if ($token !== $this->_previousToken) {
			throw new ForbiddenException('CSRF attack detected; tokens do not match.');

		} else if (time() > $this->_expires) {
			throw new ForbiddenException('CSRF attack detected; token lifetime longer than expected.');
		}
	}

}