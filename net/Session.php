<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\net;

use titon\Titon;
use titon\base\Base;
use titon\libs\adapters\SessionAdapter;
use titon\libs\traits\Attachable;
use titon\utility\Hash;
use titon\utility\Time;

/**
 * Primary library class to manage all session data. Applies appropriate ini settings depending on the environment setting.
 * Implements security walls to check for session hi-jacking and defines adapters for different save handlers.
 *
 * @package	titon.net
 */
class Session extends Base {
	use Attachable;

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
	 * 	name					- The name of the session cookie
	 * 	ini						- Custom ini settings to write
	 *	checkUserAgent 			- Validate the user agent hasn't changed between requests
	 *	checkInactivity 		- Regenerate the client session if they are idle
	 *	checkReferrer 			- Validate the host in the referrer
	 *	inactivityThreshold 	- The allotted time the client can be inactive
	 *	lifetime				- Lifetime of the session cookie
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'name' => 'Titon',
		'ini' => [],
		'checkUserAgent' => true,
		'checkInactivity' => true,
		'checkReferrer' => true,
		'inactivityThreshold' => '+5 minutes',
		'lifetime' => '+10 minutes'
	];

	/**
	 * The current session id.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_id;

	/**
	 * Destroy the current session and all values, the session id and remove the session specific cookie.
	 *
	 * @access public
	 * @return void
	 */
	public function destroy() {
		$_SESSION = [];

		if (isset($_COOKIE[$this->config->name])) {
			$params = session_get_cookie_params();
			$params['httpOnly'] = $params['httponly'];
			$params['expires'] = time();

			$this->response->cookie($this->config->name, '', $params);
		}

		if (session_status() === PHP_SESSION_ACTIVE) {
			session_destroy();
		}

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
		return Hash::get($_SESSION, $key);
	}

	/**
	 * Check to see if a certain key/path exist in the session.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return Hash::has($_SESSION, $key);
	}

	/**
	 * Returns the current session ID. If no ID is found, regenerate one.
	 *
	 * @access public
	 * @return int
	 */
	public function id() {
		if ($this->_id) {
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
		$this->attachObject('response', function() {
			return Titon::registry()->factory('titon\net\Response');
		});

		// Set default ini settings
		$segments = Titon::router()->segments();

		ini_set('url_rewriter.tags', '');
		ini_set('session.name', $this->config->name);
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

		// Lifetime
		$lifetime = $this->config->lifetime;

		if (!is_numeric($lifetime)) {
			$lifetime = Time::toUnix($lifetime) - time();
		}

		ini_set('session.cookie_lifetime', $lifetime);

		// Set custom ini
		if ($ini = $this->config->ini) {
			foreach ($ini as $key => $value) {
				ini_set($key, $value);
			}
		}

		// Start session
		if (headers_sent()) {
			$_SESSION = [];
		} else {
			session_start();
		}

		$this->_id = session_id();
		$this->validate();
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
	 * @return titon\net\Session
	 * @chainable
	 */
	public function remove($key) {
		$_SESSION = Hash::remove($_SESSION, $key);

		return $this;
	}

	/**
	 * Add a value into the session.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return titon\net\Session
	 * @chainable
	 */
	public function set($key, $value) {
		$_SESSION = Hash::set($_SESSION, $key, $value);

		return $this;
	}

	/**
	 * Set the SessionAdapter to use.
	 *
	 * @access public
	 * @param titon\libs\adapters\SessionAdapter $adapter
	 * @return titon\net\Session
	 * @chainable
	 */
	public function setAdapter(SessionAdapter $adapter) {
		$this->_adapter = $adapter;

		return $this;
	}

	/**
	 * Validate the session and regenerate or destroy if necessary.
	 *
	 * @access public
	 * @return titon\net\Session
	 * @chainable
	 */
	public function validate() {
		if ($this->has('Session')) {
			$session = $this->get('Session');

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

		return $this;
	}

	/**
	 * Startup and save the session security params.
	 *
	 * @access protected
	 * @return void
	 */
	protected function _startup() {
		$this->set('Session', [
			'time' => Time::toUnix($this->config->inactivityThreshold),
			'host' => Titon::router()->segments('host'),
			'agent' => md5(Titon::config()->salt() . $_SERVER['HTTP_USER_AGENT'])
		]);
	}

}