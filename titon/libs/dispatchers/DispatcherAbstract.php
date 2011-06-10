<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\dispatchers;

use \titon\Titon;
use \titon\base\Prototype;
use \titon\libs\dispatchers\Dispatcher;
use \titon\libs\dispatchers\DispatcherException;
use \titon\utility\Inflector;

/**
 * The Dispatcher acts as the base for all child dispatchers. The Dispatcher should not be confused with Dispatch.
 * Dispatch determines the current request and then calls the Dispatcher to output the current request.
 * The Dispatcher has many default methods for locating and validating objects within the MVC paradigm.
 *
 * @package	titon.libs.dispatchers
 */
abstract class DispatcherAbstract extends Prototype implements Dispatcher {

	/**
	 * Lazy load the view and controller objects.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->attachObject('view', function() {
			return new \titon\system\View();
		});

		// @todo
		// Do some magic to find the correct controller
		$this->attachObject('event', function() {
			return new \titon\system\Controller();
		});
	}

	/**
	 * Primary method to run the dispatcher and its process its logic.
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		throw new Exception('You must define your own run() method to dispatch the current request.');
	}

}
