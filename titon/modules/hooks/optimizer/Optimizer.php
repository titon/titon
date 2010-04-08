<?php
/**
 * The Optimizer is a Hook that triggers the garbage collector and Gzip compression.
 * Extremely powerful for applications with heavy memory use and page loading times.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\hooks\optimizer;

use \titon\modules\hooks\HookAbstract;

/**
 * Optimizer Class
 *
 * @package     Titon
 * @subpackage  Titon.Modules.Hooks.Optimizer
 */
class Optimizer extends HookAbstract {

	/**
	 * Default settings.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'gc' => true,
		'gzip' => true,
		'gzipLevel' => 5
	);

	/**
	 * Disable the garbage collection cycle.
	 *
	 * @access public
	 * @return void
	 */
	public function disableGarbageCollection() {
		if (gc_enabled()) {
			gc_disable();
		}
	}

	/**
	 * Enable the garbage collection cycle if the configuration is true.
	 *
	 * @access public
	 * @return void
	 */
	public function enableGarbageCollection() {
		if ($this->_config['gc'] && !gc_enabled()) {
			gc_enable();
		}
	}

	/**
	 * Enable Gzip compression based on the browsers Accept-Encoding header.
	 *
	 * @access public
	 * @return void
	 */
    public function enableGzipCompression() {
		if ($this->_config['gzip']) {
			$loaded = true;

			if (!extension_loaded('zlib')) {
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
					$extension = 'php_zlib.dll';
				} else {
					$extension = 'zlib.so';
				}

				if (!dl($extension)) {
					$loaded = false;
				}
			}

			if ($loaded) {
				ini_set('zlib.output_compression', true);
				ini_set('zlib.output_compression_level', $this->_config['gzipLevel']);
			}
		}
    }

}