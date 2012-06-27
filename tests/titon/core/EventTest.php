<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\Titon;
use titon\core\Event;
use titon\tests\TestCase;
use titon\libs\controllers\Controller;
use titon\libs\engines\Engine;

/**
 * Test class for titon\core\Event.
 */
class EventTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		Titon::router()->initialize();
		Titon::app()->setup('test', '/', [
			'index' => 'IndexController'
		]);
	}

	/**
	 * Test that execute triggers on the correct objects within the correct scope.
	 */
	public function testNotify() {
		// test events by name
		$event1 = new Event();
		$event1->addListener(new MockListener());
		$event1->addListener(new MockListener());
		$event1->addCallback(function() {}, ['titon.startup', 'titon.shutdown', 'testEvent1']);
		$event1->addCallback(function() {}, ['titon.startup', 'testEvent1', 'testEvent2']);
		$event1->notify('titon.startup');
		$event1->notify('titon.shutdown');
		$event1->notify('testEvent1');
		$event1->notify('testEvent2');

		$listeners = $event1->getListeners();
		$startup = 0;
		$shutdown = 0;
		$testEvent1 = 0;
		$testEvent2 = 0;

		foreach ($listeners as $listener) {
			if (isset($listener['executed']['titon.startup'])) {
				$startup++;
			}

			if (isset($listener['executed']['titon.shutdown'])) {
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
		 * $event2 = new titon\core\Event();
		$event2->addListener(new MockListener(), ['controller' => 'test']);
		$event2->addListener(new MockListener());
		$event2->addCallback(function() {}, [], ['action' => 'action']);
		$event2->addCallback(function() {});

		$_SERVER['PHP_SELF'] = 'index.php/test/index/action/';
		$_SERVER['REQUEST_URI'] = 'http://localhost/test/index/action/';
		titon\Titon::router()->initialize();

		print_r(titon\Titon::router()->current()); */
	}

	/**
	 * Test that listeners return the correct data.
	 */
	public function testListeners() {
		$event = new Event();
		$event->addListener(new MockListener());
		$event->addCallback(function() {});

		$listeners = $event->getListeners();

		$this->assertEquals(2, count($listeners));

		foreach ($listeners as $listener) {
			if ($listener['object'] instanceof MockListener || $listener['object'] instanceof Closure) {
				$this->assertTrue(true);
			} else {
				$this->assertTrue(false);
			}
		}
	}

	/**
	 * Test that registering an event listener works.
	 */
	public function testAddListener() {
		$event = new Event();
		$event->addListener(new MockListener());
		$event->addListener(new MockListener());

		$listeners = $event->getListeners();

		$this->assertEquals(2, count($listeners));

		foreach ($listeners as $listener) {
			if ($listener['object'] instanceof MockListener) {
				$this->assertTrue(true);
			} else {
				$this->assertTrue(false);
			}
		}
	}

	/**
	 * Test that registering an event closure callback works.
	 */
	public function testAddCallback() {
		$event = new Event();
		$event->addCallback(function() {});
		$event->addCallback(function() {});
		$event->addCallback(function() {});

		$listeners = $event->getListeners();

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
		$event = new Event();
		$baseEvents = ['titon.startup', 'titon.shutdown', 'dispatch.preDispatch', 'dispatch.postDispatch', 'controller.preProcess', 'controller.postProcess', 'view.preRender', 'view.postRender'];
		$testEvents = ['testEvent1', 'testEvent2'];

		$this->assertEquals($baseEvents, $event->getEvents());

		$event->setup($testEvents);

		$this->assertEquals(($testEvents + $baseEvents), $event->getEvents());
	}

}

class MockListener extends \titon\libs\listeners\ListenerAbstract {

	public function startup() {}
	public function shutdown() {}
	public function preDispatch() {}
	public function postDispatch() {}
	public function preProcess(Controller $controller) {}
	public function postProcess(Controller $controller) {}
	public function preRender(Engine $engine) {}
	public function postRender(Engine $engine) {}

}
