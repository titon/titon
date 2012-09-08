<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\identifiers\http;

use titon\libs\identifiers\IdentifierAbstract;
use titon\utility\Crypt;

/**
 * BasicIdentifier uses HTTP Basic Authentication to login and authenticate a user.
 *
 * @package	titon.libs.identifiers.http
 */
class BasicIdentifier extends IdentifierAbstract {

	/**
	 * Configuration.
	 *
	 * 	realm	- (string) The realm name to display within the login prompt
	 * 	secure	- (boolean) Will hash all passwords while validating
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'realm' => 'Authentication Login',
		'secure' => true
	];

	/**
	 * List of valid username and password logins.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_logins = [];

	/**
	 * Save the logins through the constructor.
	 *
	 * @param array $config
	 * @param array $logins
	 */
	public function __construct(array $config = [], array $logins) {
		parent::__construct($config);

		if ($this->config->secure) {
			foreach ($logins as $user => $pass) {
				$this->_logins[$user] = Crypt::hash('md5', $pass);
			}
		} else {
			$this->_logins = $logins;
		}
	}

	/**
	 * Authenticate a user using HTTP Basic authentication.
	 *
	 * @access public
	 * @return boolean
	 */
	public function authenticate() {
		$user = $this->request->env('PHP_AUTH_USER');
		$pass = $this->request->env('PHP_AUTH_PW');

		if (!$user || !$pass || empty($this->_logins[$user])) {
			return $this->login();
		}

		if ($this->config->secure) {
			$pass = Crypt::hash('md5', $pass);
		}

		if ($this->_logins[$user] !== $pass) {
			return $this->login();
		}

		return true;
	}

	/**
	 * Throw up a login prompt if authentication fails.
	 *
	 * @access public
	 * @return boolean
	 */
	public function login() {
		$this->response
			->statusCode(401)
			->wwwAuthenticate(sprintf('Basic realm="%s"', $this->config->realm))
			->respond();

		return false;
	}

	/**
	 * Unset the environment user and pass variables.
	 *
	 * @access public
	 * @return boolean
	 */
	public function logout() {
		unset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

		return true;
	}

}