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
use titon\libs\helpers\html\AssetHelper;

/**
 * Test class for titon\libs\helpers\html\AssetHelper.
 */
class AssetHelperTest extends TestCase {

	/**
	 * Test that scripts are added and retrieved by location.
	 */
	public function testScripts() {
		$helper = new AssetHelper();
		$helper
			->addScript('script.js')
			->addScript('path/commons.js', AssetHelper::HEADER)
			->addScript('path/no-extension')
			->addScript('/a/really/really/deep/path/include.js', AssetHelper::HEADER);

		$this->assertEquals(null, $helper->scripts('fakeLocation'));

		$this->assertEquals(
			'<script src="path/commons.js" type="text/javascript"></script>' . PHP_EOL .
			'<script src="/a/really/really/deep/path/include.js" type="text/javascript"></script>' . PHP_EOL
		, $helper->scripts(AssetHelper::HEADER));

		$this->assertEquals(
			'<script src="script.js" type="text/javascript"></script>' . PHP_EOL .
			'<script src="path/no-extension.js" type="text/javascript"></script>' . PHP_EOL
		, $helper->scripts(AssetHelper::FOOTER));

		// with ordering
		$helper = new AssetHelper();
		$helper
			->addScript('script.js', AssetHelper::FOOTER, 3)
			->addScript('path/commons.js', AssetHelper::FOOTER, 2)
			->addScript('path/no-extension', AssetHelper::FOOTER)
			->addScript('/a/really/really/deep/path/include.js', AssetHelper::FOOTER, 5);

		$this->assertEquals(
			'<script src="path/commons.js" type="text/javascript"></script>' . PHP_EOL .
			'<script src="script.js" type="text/javascript"></script>' . PHP_EOL .
			'<script src="path/no-extension.js" type="text/javascript"></script>' . PHP_EOL .
			'<script src="/a/really/really/deep/path/include.js" type="text/javascript"></script>' . PHP_EOL
		, $helper->scripts(AssetHelper::FOOTER));
	}

	/**
	 * Test that stylesheets are added and retrieved.
	 */
	public function testStylesheets() {
		$helper = new AssetHelper();
		$helper
			->addStylesheet('style.css')
			->addStylesheet('a/really/deep/path/with/no/extension/style.css')
			->addStylesheet('mobile.css', 'mobile');

		$this->assertEquals(
			'<link href="style.css" media="screen" rel="stylesheet" type="text/css">' . PHP_EOL .
			'<link href="a/really/deep/path/with/no/extension/style.css" media="screen" rel="stylesheet" type="text/css">' . PHP_EOL .
			'<link href="mobile.css" media="mobile" rel="stylesheet" type="text/css">' . PHP_EOL
		, $helper->stylesheets());

		// with ordering
		$helper = new AssetHelper();
		$helper
				->addStylesheet('style.css', 'handheld', 3)
				->addStylesheet('a/really/deep/path/with/no/extension/style.css', 'screen', 1)
				->addStylesheet('mobile.css', 'mobile', 2);

		$this->assertEquals(
			'<link href="a/really/deep/path/with/no/extension/style.css" media="screen" rel="stylesheet" type="text/css">' . PHP_EOL .
			'<link href="mobile.css" media="mobile" rel="stylesheet" type="text/css">' . PHP_EOL .
			'<link href="style.css" media="handheld" rel="stylesheet" type="text/css">' . PHP_EOL
		, $helper->stylesheets());
	}

}