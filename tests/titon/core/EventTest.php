<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

include_once dirname(dirname(__DIR__)) . '/bootstrap.php';

use \titon\libs\listeners\Listener;
use \titon\libs\controllers\Controller;
use \titon\libs\engines\Engine;

/**
 * Test class for \titon\core\Event.
 */
class EventTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		\titon\Titon::app()->setup('test', '/', array(
			'index' => 'IndexController'
		));
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}

	/**
	 * @todo Implement testExecute().
	 */
	public function testExecute() {
		// test events by name
		$event1 = new \titon\core\Event();
		$event1->registerListener(new EventClass());
		$event1->registerListener(new EventClass());
		$event1->registerCallback(function() {}, array('startup', 'shutdown', 'testEvent1'));
		$event1->registerCallback(function() {}, array('startup', 'testEvent1', 'testEvent2'));
		$event1->execute('startup');
		$event1->execute('shutdown');
		$event1->execute('testEvent1');
		$event1->execute('testEvent2');

		$listeners = $event1->listeners();
		$startup = 0;
		$shutdown = 0;
		$testEvent1 = 0;
		$testEvent2 = 0;

		foreach ($listeners as $listener) {
			if (isset($listener['executed']['startup'])) {
				$startup++;
			}

			if (isset($listener['executed']['shutdown'])) {
				$shutdown++;
			}

			if (isset($listener['executed']['testEvent1'])) {
				$testEvent1++;
			}

			if (isset($listener['executed']['testEvent2'])) {
				$testEvent2++;
			}
		}

		$this->assertEquals(4, $startup);
		$this->assertEquals(3, $shutdown);
		$this->assertEquals(2, $testEvent1);
		$this->assertEquals(1, $testEvent2);

		// test events by scope
		/* @todo
		 * $event2 = new \titon\core\Event();
		$event2->registerListener(new EventClass(), array('controller' => 'test'));
		$event2->registerListener(new EventClass());
		$event2->registerCallback(function() {}, array(), array('action' => 'action'));
		$event2->registerCallback(function() {});

		$_SERVER['PHP_SELF'] = 'index.php/test/index/action/';
		$_SERVER['REQUEST_URI'] = 'http://localhost/test/index/action/';
		\titon\Titon::router()->initialize();

		print_r(\titon\Titon::router()->current()); */
	}

	/**
	 * Test that listeners return the correct data.
	 */
	public function testListeners() {
		$event = new \titon\core\Event();
		$event->registerListener(new EventClass());
		$event->registerCallback(function() {});

		$listeners = $event->listeners();

		$this->assertEquals(2, count($listeners));

		foreach ($listeners as $listener) {
			if ($listener['object'] instanceof EventClass || $listener['object'] instanceof Closure) {
				$this->assertTrue(true);
			} else {
				$this->assertTrue(false);
			}
		}
	}

	/**
	 * Test that registering an event listener works.
	 */
	public function testRegisterListener() {
		$event = new \titon\core\Event();
		$event->registerListener(new EventClass());
		$event->registerListener(new EventClass());

		$listeners = $event->listeners();

		$this->assertEquals(2, count($listeners));

		foreach ($listeners as $listener) {
			if ($listener['object'] instanceof EventClass) {
				$this->assertTrue(true);
			} else {
				$this->assertTrue(false);
			}
		}
	}

	/**
	 * Test that registering an event closure callback works.
	 */
	public function testRegisterCallback() {
		$event = new \titon\core\Event();
		$event->registerCallback(function() {});
		$event->registerCallback(function() {});
		$event->registerCallback(function() {});

		$listeners = $event->listeners();

		$this->assertEquals(3, count($listeners));

		foreach ($listeners as $listener) {
			if ($listener['object'] instanceof Closure) {
				$this->assertTrue(true);
			} else {
				$this->assertTrue(false);
			}
		}
	}

	/**
	 * Test that adding new events work correctly.
	 */
	public function testSetup() {
		$event = new \titon\core\Event();
		$baseEvents = array('startup', 'shutdown', 'preDispatch', 'postDispatch', 'preProcess', 'postProcess', 'preRender', 'postRender');
		$testEvents = array('testEvent1', 'testEvent2');

		$this->assertEquals($baseEvents, $event->events());

		$event->setup($testEvents);

		$this->assertEquals(($testEvents + $baseEvents), $event->events());
	}

}

class EventClass implements Listener {
	public function startup() {}
	public function shutdown() {}
	public function preDispatch() {}
	public function postDispatch() {}
	public function preProcess(Controller $controller) {}
	public function postProcess(Controller $controller) {}
	public function preRender(Engine $engine) {}
	public function postRender(Engine $engine) {}
}
