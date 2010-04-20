<?php
/**
 * FrontDev is used as the default dispatching mechanism, sometimes referred to as a Front Controller.
 * The class is a duplicate of the base Front dispatcher, but is used primarily during development.
 * The major difference is the use of Benchmarking.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\dispatchers\front;

use \titon\log\Benchmark;
use \titon\modules\dispatchers\DispatcherAbstract;
use \titon\system\Event;

/**
 * Front Dispatcher Class
 *
 * @package     Titon
 * @subpackage	Titon.Modules.Dispatchers.Front
 */
class FrontDev extends DispatcherAbstract {

    /**
	 * Dispatches the request internally with magic!
	 *
	 * @access public
	 * @return void
	 */
	public function run() {
		Benchmark::start('Dispatcher');
        Event::execute('preDispatch');

		Benchmark::start('Controller');

            $this->Controller->initialize();

		Benchmark::stop('Controller');
		Benchmark::start('Action');

			$this->Controller->preProcess();
            Event::execute('preProcess', $this->Controller);

			$this->Controller->dispatch();

            $this->Controller->postProcess();
            Event::execute('postProcess', $this->Controller);

		Benchmark::stop('Action');
		Benchmark::start('View');

			if ($this->View->getConfig('render') === true) {
                $this->View->initialize();
                $this->View->preRender();
                Event::execute('preRender', $this->View);

                $this->View->run();
                $this->View->postRender();
                Event::execute('postRender', $this->View);
			}

		Benchmark::stop('View');

        Event::execute('postDispatch');
		Benchmark::stop('Dispatcher');
	}

}