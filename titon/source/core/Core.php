<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\source\core;

use \titon\source\core\Application;

/**
 * The core class is strictly used as a base for all the core package classes to extend.
 * It allows inheritance of the Application instance through the constructor.
 *
 * @package	titon.source.core
 * @uses	titon\source\core\Application
 */
abstract class Core {

	/**
	 * Application class instance.
	 *
	 * @see titon\source\core\Application
	 * @access public
	 * @var object
	 */
	public $app;

	/**
	 * Store the primary application class.
	 *
	 * @access public
	 * @param Application $app
	 * @return void
	 */
	public function __construct(Application $app) {
		$this->app = $app;
	}
	
}