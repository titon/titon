<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\listeners\optimizer;

use titon\Titon;
use titon\libs\listeners\ListenerAbstract;

/**
 * The Optimizer is an event listener that triggers the garbage collection and gzip compression processes.
 * Extremely powerful for applications with heavy memory use and page loading times.
 *
 * @package	titon.libs.listeners.optimizer
 */
class OptimizerListener extends ListenerAbstract {

	/**
	 * Configuration.
	 *
	 *	gc 			- Toggle garbage collection
	 *	gzip 		- Toggle GZIP compression
	 *	gzipLevel 	- GZIP compression level
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = [
		'gc' => true,
		'gzip' => true,
		'gzipLevel' => 5
	];

	/**
	 * Disable the garbage collection cycle.
	 *
	 * @access public
	 * @return void
	 */
	public function disableGarbageCollection() {
		if (!$this->config->gc || gc_enabled()) {
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
		if ($this->config->gc && !gc_enabled()) {
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
		if ($this->config->gzip && Titon::load('zlib')) {
			ini_set('zlib.output_compression', true);
			ini_set('zlib.output_compression_level', $this->config->gzipLevel);
		}
	}

	/**
	 * Enable Gzip and GC based on parent configuration.
	 *
	 * @access public
	 * @return void
	 */
	public function startup() {
		$this->enableGzipCompression();
		$this->enableGarbageCollection();
	}

	/**
	 * Disable the GC.
	 *
	 * @access public
	 * @return void
	 */
	public function shutdown() {
		$this->disableGarbageCollection();
	}

}