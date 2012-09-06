<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\listeners;

use titon\Titon;
use titon\tests\TestCase;
use titon\tests\fixtures\ControllerFixture;
use titon\tests\fixtures\DispatcherFixture;
use titon\tests\fixtures\EngineFixture;
use titon\tests\fixtures\ListenerFixture;

/**
 * Test class for titon\libs\listeners\Listener.
 */
class ListenerTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		Titon::event()->addListener(new ListenerFixture());
		Titon::router()->initialize();
	}

	/**
	 * Test that sending notifications get executed.
	 */
	public function testNotifications() {
		Titon::event()->notify('titon.startup');
		$this->assertEquals(['startup'], Titon::event()->getListeners()[0]['object']->executed);

		Titon::event()->notify('dispatch.postDispatch', new DispatcherFixture());
		$this->assertEquals(['startup', 'postDispatch'], Titon::event()->getListeners()[0]['object']->executed);

		Titon::event()->notify('controller.preProcess', new ControllerFixture());
		$this->assertEquals(['startup', 'postDispatch', 'preProcess'], Titon::event()->getListeners()[0]['object']->executed);

		Titon::event()->notify('view.postRender', new EngineFixture());
		$this->assertEquals(['startup', 'postDispatch', 'preProcess', 'postRender'], Titon::event()->getListeners()[0]['object']->executed);

		Titon::event()->notify('customEvent'); // Not in the listener
		$this->assertEquals(['startup', 'postDispatch', 'preProcess', 'postRender'], Titon::event()->getListeners()[0]['object']->executed);
	}

}