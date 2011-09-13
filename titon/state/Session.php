<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\state;

use \titon\Titon;
use \titon\base\Base;
use \titon\utility\Set;

/**
 * Primary library class to manage all session data. Applies appropriate ini settings depending on the environment setting.
 * Implements security walls to check for session highjacking and defines adapters for different save handlers.
 *
 * @package	titon.state
 * @uses	titon\Titon
 * @uses	titon\utility\Set
 */
class Session extends Base {

	/**
	 * Session security levels.
	 */
	const SECURITY_LOW = 1;
	const SECURITY_MEDIUM = 2;
	const SECURITY_HIGH = 3;

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
	 *	inactivity - How long until the session should be regenerated
	 *	security - The current session security level
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'inactivity' => '+10 minutes',
		'security' => self::SECURITY_MEDIUM
	);

	/**
	 * The current users session id.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_id;

	/**
	 * Has the session been started.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_started = false;

	/**
	 * Initialize the session settings and security. Do some validation as well.
	 *
	 * @access public
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config = array()) {
		if (isset($config['security']) && !in_array($config['security'], array(self::SECURITY_LOW, self::SECURITY_MEDIUM, self::SECURITY_HIGH))) {
			throw new StateException('Invalid security type passed to the Session object.');
		}

		parent::__construct($config);

		$this->validate();
	}

	/**
	 * Destroy the current sesssion and all values, the session id and remove the session specific cookie.
	 *
	 * @access public
	 * @return void
	 */
	public function destroy() {
		$_SESSION = array();

		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time(), '/');
		}

		session_destroy();

		if (!headers_sent()) {
			$this->regenerate();
		}

		$this->_started = true;
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
		if ($this->_started) {
			return;
		}

		$config = $this->config();
		$segments = Titon::router()->segments();

		ini_set('session.name', Titon::config()->name() . '[Session]');
		ini_set('session.use_trans_sid', false);
		ini_set('url_rewriter.tags', '');
		ini_set('session.use_cookies', true);
		ini_set('session.use_only_cookies', true);
		ini_set('session.auto_start', true);

		if ($segments['scheme'] == 'https') {
			ini_set('session.cookie_secure', true);
		}

		$time = time();
		$host = str_replace('http://', '', $segments['host']);
		$agent = md5(Titon::config()->salt() . $_SERVER['HTTP_USER_AGENT']);

		switch ($config['security']) {
			case self::SECURITY_HIGH:
			case self::SECURITY_MEDIUM:
			default:
				$timeout = ($config['security'] == self::SECURITY_HIGH) ? 10 : 25;

				ini_set('session.referer_check', $host);
				ini_set('session.cookie_domain', $host);
				ini_set('session.cookie_lifetime', (60 * $timeout));
			break;
			case self::SECURITY_LOW:
				ini_set('session.cookie_domain', $host);
				ini_set('session.cookie_lifetime', (60 * 45));
			break;
		}

		if (headers_sent()) {
			$_SESSION = array();
		} else {
			session_start();
		}

		$this->_id = session_id();
		$this->_started = true;

		if ($this->has('Security') === false) {
			$this->set('Security', array(
				'time' => $time,
				'host' => $host,
				'agent' => $agent
			));
		}

		return $this->_started;
	}

	/**
	 * Regenerate the current session and apply a new session ID.
	 *
	 * @access public
	 * @param bool $delete
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
	 * @return Session
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
	 * @return Session
	 * @chainable
	 */
	public function set($key, $value) {
		$_SESSION = Set::insert($_SESSION, $key, $value);

		return $this;
	}

	/**
	 * Set the session adapter to use.
	 * 
	 * @access public
	 * @param SessionAdapter $adapter 
	 * @return Session
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
	 * @return void
	 */
	public function validate() {
		if (!$this->_started) {
			return;
		}

		$security = $this->get('Security');

		if (!empty($security)) {
			$config = $this->config();
			
			if ($config['security'] == self::SECURITY_HIGH || $config['security'] == self::SECURITY_MEDIUM) {
				if ($security['agent'] != md5(Titon::config()->salt() . $_SERVER['HTTP_USER_AGENT'])) {
					$this->destroy();
				}
			}

			if ($config['security'] == self::SECURITY_HIGH) {
				if ($security['time'] <= strtotime($config['inactivity'])) {
					$this->regenerate();
				}
			}
		}
	}

}