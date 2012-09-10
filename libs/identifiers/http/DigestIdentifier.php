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

/**
 * DigestIdentifier uses HTTP Digest Authentication to login and authenticate a user.
 *
 * @package	titon.libs.identifiers.http
 */
class DigestIdentifier extends IdentifierAbstract {

	/**
	 * Configuration.
	 *
	 * 	realm	- (string) The authentication realm name
	 * 	qop		- (string) Quality of protection; defaults to "auth"
	 * 	nonce	- (string) Custom server nonce; defaults to uniqid()
	 *	opaque	- (string) Unchangeable token to be sent between requests
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'realm' => '',
		'qop' => 'auth',
		'nonce' => '',
		'opaque' => ''
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

		if (!$this->config->realm) {
			$this->config->realm = $this->request->env('SERVER_NAME');
		}
	}

	/**
	 * Authenticate a user using HTTP Digest authentication.
	 *
	 * @access public
	 * @return boolean
	 */
	public function authenticate() {
		$digest = $this->request->env('PHP_AUTH_DIGEST');

		if (!$digest) {
			return $this->login();
		}

		$digest = $this->parseDigest($digest);

		if (!$digest || empty($this->_logins[$digest['username']])) {
			return $this->login();
		}

		$ha1 = md5($digest['username'] . ':' . $this->config->realm . ':' . $this->_logins[$digest['username']]);
		$ha2 = md5($this->request->env('REQUEST_METHOD') . ':' . $digest['uri']);
		$response = md5($ha1 . ':' . $digest['nonce'] . ':' . $digest['nc'] . ':' . $digest['cnonce'] . ':' . $digest['qop'] . ':' . $ha2);

		if ($response !== $digest['response']) {
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
		$realm = $this->config->realm;
		$qop = $this->config->qop;
		$nonce = $this->config->nonce;
		$opaque = $this->config->opaque;

		if (!$nonce) {
			$nonce = uniqid();
		}

		if (!$opaque) {
			$opaque = md5($realm);
		}

		$this->response
			->statusCode(401)
			->wwwAuthenticate(sprintf('Digest realm="%s",qop="%s",nonce="%s",opaque="%s"', $realm, $qop, $nonce, $opaque))
			->respond();

		return false;
	}

	/**
	 * Unset the environment digest variable.
	 *
	 * @access public
	 * @return boolean
	 */
	public function logout() {
		unset($_SERVER['PHP_AUTH_DIGEST']);

		return true;
	}

	/**
	 * Parse the HTTP digest auth header and return its values.
	 *
	 * @access public
	 * @param string $string
	 * @return array|boolean
	 */
	public function parseDigest($string) {
		$parts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
		$data = array();
		$keys = implode('|', array_keys($parts));

		preg_match_all('/(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))/', $string, $matches, PREG_SET_ORDER);

		foreach ($matches as $m) {
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($parts[$m[1]]);
		}

		return $parts ? false : $data;
	}

}