<?php
/**
 * Primary library class to manage all session data. Applies appropriate ini settings depending on the environment setting.
 * Implements security walls to check for session highjacking and fixation. Lastly, defines different save handlers.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\state;

use \titon\core\Config;
use \titon\utility\Set;

/**
 * Session Class
 *
 * @package		Titon
 * @subpackage	Titon.State
 */
class Session {

    /**
     * Session security level constant: low.
     *
     * @var string
     */
    const SECURITY_LOW = 1;

    /**
     * Session security level constant: medium.
     *
     * @var string
     */
    const SECURITY_MEDIUM = 2;

    /**
     * Session security level constant: high.
     *
     * @var string
     */
    const SECURITY_HIGH = 3;

    /**
     * Storage engine setting: php.
     *
     * @var string
     */
    const STORAGE_PHP = 'php';

    /**
     * Storage engine setting: cache.
     *
     * @var string
     */
    const STORAGE_CACHE = 'cache';

    /**
     * Storage engine setting: database.
     *
     * @var string
     */
    const STORAGE_DATABASE = 'database';

	/**
	 * How long (in minutes) until the session should be regenerated.
	 *
	 * @access public
	 * @var int
	 */
	public $inactivity = 10; // Minutes

	/**
	 * The current level of session security defined by the environment.
	 *
	 * @access public
	 * @var string
	 */
	public $security;

	/**
     * Which storage adapter should be used. Possible values are: php, database, cache.
     *
     * @access public
     * @var string
     */
    public $storage;
	
	/**
	 * The users browser / user agent used during security session validation.
	 *
	 * @access private
	 * @var string
	 */
	private $__agent;
	
	/**
	 * The current HTTP host. Used for cookie and session ini settings.
	 *
	 * @access private
	 * @var string
	 */
	private $__host;

	/**
	 * The current users session id.
	 *
	 * @access private
	 * @var string
	 */
	private $__id;

    /**
     * Has the session been started.
     *
     * @access private
     * @var string
     */
    private $__started = false;
	
	/**
	 * The current timestamp used in session security timeouts.
	 *
	 * @access private
	 * @var string
	 */
	private $__time;
	
	/**
	 * Initialize the session settings and security. Do some validation as well.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		if ($this->__started == false) {
			$this->__time	= time();
			$this->__host	= trim(str_replace('http://', '', $_SERVER['HTTP_HOST']), '/');
			$this->__agent	= md5(Config::get('App.salt') . $_SERVER['HTTP_USER_AGENT']);
		}
		
		// Validate on each request
		if ($this->__started === true) {
			$settings = $this->get('Security');

			if (!empty($settings)) {
				if (($this->security == self::SECURITY_HIGH) || ($this->security == self::SECURITY_MEDIUM)) {
					if ($settings['agent'] != md5(Config::get('App.salt') . $_SERVER['HTTP_USER_AGENT'])) {
						$this->destroy();
					}
				}

				if ($this->security == self::SECURITY_HIGH) {
					$timeout = strtotime('-'. $this->inactivity .' minutes');

					if ($settings['time'] <= $timeout) {
						$this->regenerate();
					}
				}
			}
		}
	}
	
	/**
	 * Check to see if a certain key/path exist in the session.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function check($key) {
		return Set::exists($_SESSION, $key);
	}
	
	/**
	 * Destroy the current sesssion and all values, the session id and remove the session specific cookie.
	 *
	 * @access public
	 * @return void
	 */
	public function destroy() {
		$this->__id = null;
		$this->__agent = null;
		$this->__time = null;
		$_SESSION = array();
		
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time(), '/');
		}
		
		session_destroy();
        
        if (!headers_sent()) {
            $this->regenerate();
        }

        $this->__started = true;
		$this->__construct();
	}

	/**
	 * Get a certain value from the session based on the key/path.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		return Set::extract($_SESSION, $key);
	}
	
	/**
	 * Returns the current session ID. If no ID is found, regenerate one.
	 *
	 * @access public
	 * @return int
	 */
	public function id() {
		if (isset($this->__id)) {
			return $this->__id;
		} else if ($id = session_id()) {
			return $id;
		}
		
		return $this->regenerate();
	}

    /**
	 * Initialize the session by applying all ini settings depending on security level.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
        if ($this->__started == true) {
            return;
        }

		if (!in_array($this->storage, array(self::STORAGE_PHP, self::STORAGE_CACHE, self::STORAGE_DATABASE))) {
            $this->storage = self::STORAGE_PHP;
        }

        if (!in_array($this->security, array(self::SECURITY_LOW, self::SECURITY_MEDIUM, self::SECURITY_HIGH))) {
            $this->security = self::SECURITY_MEDIUM;
        }

        // Ini Settings
		ini_set('session.name', Config::get('App.name') .'[Session]');
		ini_set('session.use_trans_sid', false);
		ini_set('url_rewriter.tags', '');
		ini_set('session.use_cookies', true);
		ini_set('session.use_only_cookies', true);
        ini_set('session.auto_start', true);

        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
			ini_set('session.cookie_secure', true);
		}

        // Security Settings
		switch ($this->security) {
			case self::SECURITY_HIGH:
			case self::SECURITY_MEDIUM:
			default:
				$timeout = ($this->security == self::SECURITY_HIGH) ? 10 : 25;
				$lifetime = (60 * $timeout); // seconds * length = minutes

				ini_set('session.referer_check', $this->__host);
                ini_set('session.cookie_domain', $this->__host);
				ini_set('session.cookie_lifetime', $lifetime);
			break;
			case self::SECURITY_LOW:
				$timeout = 45;

				ini_set('session.cookie_domain', $this->__host);
				ini_set('session.cookie_lifetime', 0);
			break;
		}

        // Storage Settings
        switch ($this->storage) {
            case self::STORAGE_CACHE:

            break;
            case self::STORAGE_DATABASE:

            break;
            case self::STORAGE_PHP:
            default:
                
            break;
        }

        // Start Session
		if (headers_sent()) {
            $_SESSION = array();
		} else {
			session_start();
		}

		$this->__id = session_id();
        $this->__started = true;

		// Store settings
		if ($this->check('Security') == false) {
			$this->set('Security', array(
				'time' => $this->__time,
				'host' => $this->__host,
				'agent' => $this->__agent,
				'storage' => $this->storage,
				'inactivity' => $this->inactivity
			));
		}
		
        return $this->__started;
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
		$this->__id = session_id();
        
		return $this->__id;
	}

    /**
	 * Remove a certain key/path from the session.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		$_SESSION = Set::remove($_SESSION, $key);
	}
	
	/**
	 * Add a value into the session based on the key/path.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return boolean
	 */
	public function set($key, $value) {
		$_SESSION = Set::insert($_SESSION, $key, $value);
	}
	
}