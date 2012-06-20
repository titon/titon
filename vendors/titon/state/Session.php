<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\state;

use titon\Titon;
use titon\base\Base;
use titon\libs\adapters\SessionAdapter;
use titon\utility\Set;

/**
 * Primary library class to manage all session data. Applies appropriate ini settings depending on the environment setting.
 * Implements security walls to check for session hi-jacking and defines adapters for different save handlers.
 *
 * @package	titon.state
 * @uses	titon\Titon
 * @uses	titon\utility\Set
 */
class Session extends Base {

	/**
	 * Storage adapter instance.
	 *
	 * @access protected
	 * @var SessionAdapter
	 */
	protected $_adapter;

	/**
	 * Configuration.
	 *
	 *	checkUserAgent 			- Validate the user agent hasn't changed between requests
	 *	checkInactivity 		- Regenerate the client session if they are idle
	 *	checkReferrer 			- Validate the host in the referrer
	 *	inactivityThreshold 	- The allotted time the client can be inactive
	 *	sessionLifetime 		- Lifetime of the session cookie
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'checkUserAgent' => true,
		'checkInactivity' => true,
		'checkReferrer' => true,
		'inactivityThreshold' => '+5 minutes',
		'sessionLifetime' => '+10 minutes'
	);

	/**
	 * The current session id.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_id;

	/**
	 * Destroy the current sesssion and all values, the session id and remove the session specific cookie.
	 *
	 * @access public
	 * @return void
	 */
	public function destroy() {
		$_SESSION = array();

		if (isset($_COOKIE[session_name()])) {
			$params = session_get_cookie_params();

			setcookie(session_name(), '', time(), $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}

		session_destroy();

		if (!headers_sent()) {
			$this->regenerate();
		}

		if ($this->_adapter instanceof SessionAdapter) {
			$this->_adapter->register();
		}
	}

	/**
	 * Get a certain value from the session based on the key/path.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key = null) {
		return Set::extract($_SESSION, $key);
	}

	/**
	 * Check to see if a certain key/path exist in the session.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return Set::exists($_SESSION, $key);
	}

	/**
	 * Returns the current session ID. If no ID is found, regenerate one.
	 *
	 * @access public
	 * @return int
	 */
	public function id() {
		if (!empty($this->_id)) {
			return $this->_id;

		} else if ($id = session_id()) {
			$this->_id = $id;

			return $id;
		}

		return $this->regenerate();
	}

	/**
	 * Initialize the session by applying all ini settings dependent on security level.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$segments = Titon::router()->segments();

		ini_set('url_rewriter.tags', '');
		ini_set('session.name', 'TitonSession');
		ini_set('session.use_trans_sid', false);
		ini_set('session.use_cookies', true);
		ini_set('session.use_only_cookies', true);
		ini_set('session.cookie_domain', $segments['host']);

		if ($segments['scheme'] === 'https') {
			ini_set('session.cookie_secure', true);
		}

		if ($this->config->checkReferrer) {
			ini_set('session.referer_check', $segments['host']);
		}

		$lifetime = $this->config->sessionLifetime;

		if (is_int($lifetime)) {
			$timeout = $lifetime;
		} else {
			$timeout = strtotime($lifetime) - time();
		}

		ini_set('session.cookie_lifetime', $timeout);

		if (headers_sent()) {
			$_SESSION = array();
		} else {
			session_start();
		}

		$this->_id = session_id();
		$this->_validate();
	}

	/**
	 * Regenerate the current session and apply a new session ID.
	 *
	 * @access public
	 * @param boolean $delete
	 * @return int
	 */
	public function regenerate($delete = true) {
		session_regenerate_id($delete);

		$this->_id = session_id();

		return $this->_id;
	}

	/**
	 * Remove a certain key from the session.
	 *
	 * @access public
	 * @param string $key
	 * @return titon\state\Session
	 * @chainable
	 */
	public function remove($key) {
		$_SESSION = Set::remove($_SESSION, $key);

		return $this;
	}

	/**
	 * Add a value into the session.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return titon\state\Session
	 * @chainable
	 */
	public function set($key, $value) {
		$_SESSION = Set::insert($_SESSION, $key, $value);

		return $this;
	}

	/**
	 * Set the SessionAdapter to use.
	 *
	 * @access public
	 * @param titon\libs\adapters\SessionAdapter $adapter
	 * @return titon\state\Session
	 * @chainable
	 */
	public function setAdapter(SessionAdapter $adapter) {
		$this->_adapter = $adapter;

		return $this;
	}

	/**
	 * Startup and save the session security params.
	 *
	 * @access protected
	 * @return void
	 */
	protected function _startup() {
		$this->set('Security', array(
			'time' => strtotime($this->config->inactivityThreshold),
			'host' => Titon::router()->segments('host'),
			'agent' => md5(Titon::config()->salt() . $_SERVER['HTTP_USER_AGENT'])
		));
	}

	/**
	 * Validate the session and regenerate or destroy if necessary.
	 *
	 * @access protected
	 * @return void
	 */
	protected function _validate() {
		if ($this->has('Security')) {
			$session = $this->get('Security');

			if ($this->config->checkUserAgent && $session['agent'] !== md5(Titon::config()->salt() . $_SERVER['HTTP_USER_AGENT'])) {
				$this->destroy();
				$this->_startup();
			}

			if ($this->config->checkInactivity && $session['time'] <= time()) {
				$this->regenerate();
				$this->_startup();
			}

		} else {
			$this->_startup();
		}
	}

}