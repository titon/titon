<?php
/**
 * Front is used as the default dispatching mechanism, sometimes referred to as a Front Controller.
 * It implements the base Dispatcher class to inherit the methods for locating Controllers, Views, etc.
 * Once located, it dispatches the current request, all the while logging benchmarks and triggering hooks.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\dispatchers\front;

use \titon\log\Benchmark;
use \titon\modules\dispatchers\DispatcherAbstract;
use \titon\system\Hook;

/**
 * Front Dispatcher Class
 *
 * @package     Titon
 * @subpackage	Titon.Modules.Dispatchers
 */
class Front extends DispatcherAbstract {

    /**
	 * Dispatches the request internally with magic!
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public function run() {
		Benchmark::start('Dispatcher');
        Hook::execute('preDispatch');

		Benchmark::start('Controller');

            $this->Controller->initialize();
            Hook::execute('initialize', $this->Controller);

		Benchmark::stop('Controller');
		Benchmark::start('Action');

			$this->Controller->preProcess();
            Hook::execute('preProcess', $this->Controller);

			$this->Controller->dispatch();

            $this->Controller->postProcess();
            Hook::execute('postProcess', $this->Controller);

		Benchmark::stop('Action');
		Benchmark::start('View');

			if ($this->View->getConfig('render') === true) {
                $this->View->initialize();
                $this->View->preRender();
                Hook::execute('preRender', $this->View);

                $output = $this->View->run();

                $this->View->postRender();
                Hook::execute('postRender', $this->View);
			}

		Benchmark::stop('View');

        Hook::execute('postDispatch');
		Benchmark::stop('Dispatcher');
		return;
	}

}