<?php
/**
 * Front is used as the default dispatching mechanism, sometimes referred to as a Front Controller.
 * It implements the base Dispatcher class to inherit the methods for locating Controllers, Views, etc.
 * Once located, it dispatches the current request, all the while triggering hooks.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\dispatchers\front;

use \titon\modules\dispatchers\DispatcherAbstract;
use \titon\system\Hook;

/**
 * Front Dispatcher Class
 *
 * @package     Titon
 * @subpackage	Titon.Modules.Dispatchers.Front
 */
class Front extends DispatcherAbstract {

    /**
	 * Dispatches the request internally with magic!
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
        Hook::execute('preDispatch');

		$this->Controller->initialize();
		$this->Controller->preProcess();
		Hook::execute('preProcess', $this->Controller);

		$this->Controller->dispatch();
		$this->Controller->postProcess();
		Hook::execute('postProcess', $this->Controller);

		if ($this->View->getConfig('render') === true) {
			$this->View->initialize();
			$this->View->preRender();
			Hook::execute('preRender', $this->View);

			$this->View->run();
			$this->View->postRender();
			Hook::execute('postRender', $this->View);
		}

        Hook::execute('postDispatch');
	}

}