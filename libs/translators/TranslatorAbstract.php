<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\translators;

use \titon\Titon;
use \titon\base\Base;
use \titon\libs\translators\Translator;
use \titon\libs\translators\TranslatorException;
use \titon\utility\Set;

/**
 * @todo
 *
 * @package	titon.libs.translators
 * @abstract
 */
class TranslatorAbstract extends Base implements Translator { 
	
	protected $_cache = array();
	
	public function getMessage($key) {
		$parts = $this->parseKey($key);
		$module = $parts['module'];
		$domain = $parts['domain'];
		$key = $parts['key'];
		
		if (!Set::exists($this->_cache, $module . '.' . $domain)) {
			$this->_cache = Set::insert($this->_cache, $module . '.' . $domain, $this->loadFile($module, $domain));
		}
		
		if (isset($this->_cache[$module][$domain][$key])) {
			return $this->_cache[$module][$domain][$key];
		}
		
		throw new TranslatorException(sprintf('Message key %s does not exist.', $key));
	}
	
	public function hasMessage($key) {
		$parts = $this->parseKey($key);
		$module = $parts['module'];
		$domain = $parts['domain'];
		$key = $parts['key'];
		
		return isset($this->_cache[$module][$domain][$key]);
	}
	
	public function loadFile($module, $domain) {
		throw new TranslatorException(spritnf('You must define the loadFile() method within your %s translator.', get_class($this)));
	}
	
	public function parseKey($key) {
		$parts = explode('.', $key);
		
		if (count($parts) < 3) {
			throw new TranslatorException(sprintf('No module or domain located for %s.', $key));
		}
		
		$module = array_shift($parts);
		$domain = array_shift($parts);
		$key = implode('.', $parts);
		
		return array(
			'module' => $module,
			'domain' => $domain,
			'key' => $key
		);
	}

	public function translate($key, array $params = array()) {	
		$locale = Titon::g11n()->current();
		return $this->getMessage($key);
		//$format = new \MessageFormatter($locale['locale'], $this->getMessage($key));
		
		//return $format->format($params);
	}
	
}
