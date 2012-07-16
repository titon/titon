<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\helpers\html;

use titon\tests\TestCase;
use titon\libs\helpers\html\BreadcrumbHelper;

/**
 * Test class for titon\libs\helpers\html\BreadcrumbHelper.
 */
class BreadcrumbHelperTest extends TestCase {

	/**
	 * Test with a single crumb.
	 */
	public function testOneCrumb() {
		$helper = new BreadcrumbHelper();
		$helper->add('Title', '/');

		$this->assertEquals([
			'<a href="/">Title</a>' . PHP_EOL
		], $helper->generate());
	}

	/**
	 * Test with multiple crumbs, attributes and dynamic URLs.
	 */
	public function testMultipleCrumbs() {
		$helper = new BreadcrumbHelper();
		$helper
			->add('Title', '/')
			->add('Title 2', '/static/url', ['class' => 'tier2'])
			->add('Title 3', ['action' => 'view', 123], ['class' => 'tier3']);

		$this->assertEquals([
			'<a href="/">Title</a>' . PHP_EOL,
			'<a class="tier2" href="/static/url">Title 2</a>' . PHP_EOL,
			'<a class="tier3" href="/pages/index/view/123">Title 3</a>' . PHP_EOL
		], $helper->generate());
	}

}